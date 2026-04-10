<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Api\AstrologerApiService;
use Illuminate\Http\JsonResponse;

class ConsultantController extends Controller
{

    protected AstrologerApiService $astrologerApiService;

    public function __construct(AstrologerApiService $astrologerApiService)
    {
        $this->astrologerApiService = $astrologerApiService;
    }

    /**
     * Display a listing of astrologers from the API.
     */
    public function show(Request $request)
    {
        $forceRefresh = filter_var($request->query('refresh', false), FILTER_VALIDATE_BOOLEAN);
        $allAstrologers = $this->astrologerApiService->getAstrologers($forceRefresh) ?? [];

        try {
            $filteredResponse = $this->astrologerApiService->filterAstrologers([
                'page' => 1,
                'per_page' => 15,
            ]);

            $astrologers = $filteredResponse['items'];
            $pagination = $filteredResponse['meta'];
        } catch (\Throwable $exception) {
            report($exception);

            $astrologers = [];
            $pagination = [
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 15,
                'total' => 0,
            ];
        }

        if ($astrologers === [] && $allAstrologers !== []) {
            $astrologers = array_slice($allAstrologers, 0, 15);
            $total = count($allAstrologers);
            $pagination = [
                'current_page' => 1,
                'last_page' => max(1, (int) ceil($total / 15)),
                'per_page' => 15,
                'total' => $total,
            ];
        }

        $filters = $this->astrologerApiService->getConsultantFilterSections($allAstrologers);
        return view('consultant', compact('astrologers', 'filters', 'pagination'));
    }

    public function filter(Request $request): JsonResponse
    {
        try {
            $response = $this->astrologerApiService->filterAstrologers($request->query());
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Unable to load astrologers right now.',
            ], 502);
        }

        return response()->json([
            'html' => view('consultant.partials.results', [
                'astrologers' => $response['items'],
            ])->render(),
            'pagination' => view('consultant.partials.pagination', [
                'pagination' => $response['meta'],
            ])->render(),
            'meta' => $response['meta'],
        ]);
    }

    public function profile($identifier)
    {
        $consultant = $this->astrologerApiService->getAstrologerByIdOrSlug($identifier);
        if (!$consultant) {
            abort(404, 'Consultant not found');
        }

        // Try to get the latest appointment for this consultant and the current user
        $user = session('auth.user');
        $userId = session('api_user_id');
        $appointmentId = null;
        $sessionInProgress = false;
        $allBookings = [];
        if ($user && $userId && isset($consultant['id'])) {
            $token = session('auth.api_token') ?? null;
            $apiService = app(\App\Services\Api\Clients\AstrologerApiService::class);
            $bookings = $apiService->getBookings($token);
            if (isset($bookings['data']) && is_array($bookings['data'])) {
                $filtered = collect($bookings['data'])
                    ->where('astrologer_id', $consultant['id'])
                    ->where('user_id', $userId)
                    ->sortByDesc('created_at');
                $allBookings = $filtered->map(function ($b) {
                    return ['id' => $b['id'], 'status' => $b['status'] ?? ''];
                })->values()->all();

                $joinableStatuses = ['ready_to_start', 'in_progress'];

                // Find the most recent joinable booking
                $inProgress = $filtered->first(function ($b) {
                    return in_array(strtolower(trim($b['status'] ?? '')), ['ready_to_start', 'in_progress'], true);
                });
                if ($inProgress) {
                    $appointmentId = $inProgress['id'];
                    $sessionInProgress = true;
                    $consultant['raw_status'] = $inProgress['status'] ?? '';
                } else {
                    // Fallback to latest booking
                    $latest = $filtered->first();
                    if ($latest) {
                        $appointmentId = $latest['id'];
                        $status = strtolower(trim($latest['status'] ?? ''));
                        $sessionInProgress = in_array($status, $joinableStatuses, true);
                        $consultant['raw_status'] = $latest['status'] ?? '';
                    }
                }
            }
        }
        $consultant['appointment_id'] = $appointmentId;
        $consultant['session_status'] = $sessionInProgress;
        $consultant['all_bookings'] = $allBookings;
        return view('consultant-profile', compact('consultant'));
    }
}
