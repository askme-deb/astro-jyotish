<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Services\AstrologerBookingService;
use App\Services\ConsultationStateService;

class MyBookingsController extends Controller
{
    /**
     * Display the user's bookings page.
     */
    public function index(Request $request, AstrologerBookingService $bookingService)
    {
        $token = $request->cookie('auth_api_token');
        $response = $bookingService->getBookings($token);
        $bookings = $response['data'] ?? [];
        return view('my-bookings', compact('bookings'));
    }

    public function show($id, Request $request, AstrologerBookingService $bookingService)
    {
        $token = $this->getUserApiToken($request);
        $booking = $this->findBookingById((int) $id, $request, $bookingService);
        $suggestedProducts = [];

        // Compute astrologer initials if booking and astrologer name exist
        $astroInitials = '';
        if ($booking && isset($booking['astrologer']['name'])) {
            $names = preg_split('/\s+/', trim($booking['astrologer']['name']));
            foreach ($names as $n) {
                if ($n !== '') {
                    $astroInitials .= mb_strtoupper(mb_substr($n, 0, 1));
                }
            }
        }

        if ($booking) {
            $astrologerId = $this->firstNumericValue($booking, [
                'astrologer_id',
                'astrologer.id',
                'assigned_astrologer_id',
            ]) ?? session('api_user_id');

            $cartResponse = app(\App\Services\Api\Clients\AstrologerApiService::class)->getAstrologerCarts([
                'astrologer_id' => (int) $astrologerId,
                'booking_id' => (int) $booking['id'],
            ], $token);
            //dd($cartResponse);
            $suggestedProducts = $this->extractSuggestedProductsForBooking($cartResponse, (int) $booking['id']);
        }

        return view('booking-details', compact('booking', 'astroInitials', 'suggestedProducts'));
    }

    public function downloadNotesPdf($id, Request $request, AstrologerBookingService $bookingService)
    {
        $booking = $this->findBookingById((int) $id, $request, $bookingService);

        if (!is_array($booking) || empty($booking)) {
            return back()->with('error', 'Booking not found.');
        }

        $isNoteFinalized = (bool) ($booking['final_confirmation_from_astrologer'] ?? false)
            || (($booking['astrologer_note_status'] ?? null) === 'finalized');

        if (!$isNoteFinalized) {
            return back()->with('error', 'Astrologer note is not available for download yet.');
        }

        $filename = 'consultation-note-bkng' . (int) $booking['id'] . '.pdf';

        $pdf = Pdf::loadView('astrologer.appointment-note-pdf', [
            'appointment' => $booking,
            'generatedAt' => now(),
            'logoPath' => public_path('assets/images/Logo.png'),
            'appName' => config('app.name', 'Astro Consultant'),
        ])->setPaper('a4');

        return $pdf->download($filename);
    }

    public function activeConsultationStatus(Request $request, AstrologerBookingService $bookingService)
    {
        $token = $request->cookie('auth_api_token');

        if (!$token) {
            return response()->json([
                'success' => true,
                'active' => false,
            ]);
        }

        $response = $bookingService->getBookings($token);
        $stateService = app(ConsultationStateService::class);
        $bookings = collect($response['data'] ?? [])->map(function ($booking) use ($stateService) {
            return is_array($booking) ? $stateService->mergeIntoBooking($booking, (int) ($booking['id'] ?? 0)) : $booking;
        });
        $joinableStatuses = ['ready_to_start', 'in_progress'];

        $activeBooking = $bookings
            ->filter(function ($booking) use ($joinableStatuses) {
                return is_array($booking) && in_array(($booking['status'] ?? null), $joinableStatuses, true);
            })
            ->sortByDesc(function ($booking) {
                return (int) ($booking['id'] ?? 0);
            })
            ->first();

        if (!$activeBooking) {
            return response()->json([
                'success' => true,
                'active' => false,
            ]);
        }

        $bookingId = (int) ($activeBooking['id'] ?? 0);

        return response()->json([
            'success' => true,
            'active' => true,
            'bookingId' => $bookingId,
            'status' => $activeBooking['status'] ?? 'ready_to_start',
            'joinUrl' => route('customer.consultation.video', ['meetingId' => 'astro-' . $bookingId, 'duration' => (int) ($activeBooking['duration'] ?? 0)]),
            'bookingDetailsUrl' => route('booking.details', ['id' => $bookingId]),
            'astrologerName' => $activeBooking['astrologer']['name'] ?? 'your astrologer',
        ]);
    }

    public function joinConsultation($id, Request $request)
    {
        $validatedId = (int) $id;
        $durationFromRequest = $request->filled('duration')
            ? (int) $request->input('duration')
            : ($request->filled('duration_minutes') ? (int) $request->input('duration_minutes') : null);
        $token = $request->cookie('auth_api_token') ?? session('auth.api_token') ?? session('auth_api_token');
        $stateService = app(ConsultationStateService::class);
        $state = $stateService->get($validatedId);

        $apiService = app(\App\Services\Api\Clients\AstrologerApiService::class);
        $booking = null;

        if ($token) {
            try {
                $bookingsResponse = $apiService->getBookings($token);
                $booking = collect($bookingsResponse['data'] ?? [])->firstWhere('id', $validatedId);
            } catch (\Throwable $exception) {
                $booking = null;
            }
        }

        if (is_array($booking)) {
            $booking = $stateService->mergeIntoBooking($booking, $validatedId);
        }

        if (! is_array($booking) && is_array($state)) {
            $booking = array_merge(['id' => $validatedId], $state);
        }

        if (! is_array($booking) || empty($booking)) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found.',
            ], 404);
        }

        $status = (string) ($booking['status'] ?? '');

        if ($status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'This consultation has already ended.',
            ], 422);
        }

        if ($status === 'confirmed' || $status === 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Please wait for the astrologer to start the consultation.',
            ], 422);
        }

        if ($status === 'in_progress') {
            $response = [
                'success' => true,
                'message' => 'Consultation is already live.',
                'status' => 'in_progress',
                'meetingStartedAt' => $booking['meeting_started_at'] ?? null,
            ];

            if ($request->isMethod('get')) {
                return response()->json($response);
            }

            return response()->json($response);
        }

        $result = [];
        if ($token) {
            $result = $apiService->joinVideoConsultation($validatedId, $token);
        }

        $payload = $result['data'] ?? $result;
        $bookingDuration = isset($booking['duration']) && is_numeric($booking['duration']) ? (int) $booking['duration'] : null;
        $stateDuration = isset($state['duration']) && is_numeric($state['duration']) ? (int) $state['duration'] : null;
        $resolvedDuration = ($bookingDuration && $bookingDuration > 0)
            ? $bookingDuration
            : (($stateDuration && $stateDuration > 0)
                ? $stateDuration
                : (($durationFromRequest && $durationFromRequest > 0) ? $durationFromRequest : null));
        $localState = $stateService->markInProgress(
            $validatedId,
            $payload['meeting_started_at'] ?? null,
            $payload['meeting_id'] ?? ($booking['meeting_id'] ?? ('astro-' . $validatedId)),
            $resolvedDuration
        );
        $succeeded = ! ($result['error'] ?? false) && (bool) ($result['status'] ?? $result['success'] ?? false);

        if (! $succeeded && !empty($result)) {
            // Fall back to the local consultation state so the session can still start inside this app.
            $payload = is_array($payload) ? array_merge($payload, $localState) : $localState;
        } elseif (empty($result)) {
            $payload = $localState;
        }

        $response = [
            'success' => true,
            'message' => $result['message'] ?? 'Consultation is now live.',
            'status' => 'in_progress',
            'meetingStartedAt' => $payload['meeting_started_at'] ?? ($payload['data']['meeting_started_at'] ?? $localState['meeting_started_at'] ?? now()->utc()->toIso8601String()),
            'data' => $payload,
        ];

        if ($request->isMethod('get')) {
            return response()->json($response);
        }

        return response()->json($response);
    }

    public function reschedule($id, Request $request, AstrologerBookingService $bookingService)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'slot_id' => 'required|string',
        ]);

        $token = $request->cookie('auth_api_token') ?? session('auth.api_token') ?? session('auth_api_token');

        if (! $token) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $bookingResponse = $bookingService->getBookingById((int) $id, $token);
        $booking = $bookingResponse['data'] ?? null;

        if (! is_array($booking) || empty($booking)) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found.',
            ], 404);
        }

        $status = (string) ($booking['status'] ?? '');
        $blockedStatuses = config('booking.reschedule_blocked_statuses', []);

        if (in_array($status, $blockedStatuses, true)) {
            return response()->json([
                'success' => false,
                'message' => 'This booking can no longer be rescheduled.',
            ], 422);
        }

        $payload = [
            'booking_id' => (int) $id,
            'date' => $validated['date'],
            'slot_id' => $validated['slot_id'],
        ];

        $result = $bookingService->rescheduleBooking($payload, $token);
        $succeeded = (bool) ($result['status'] ?? $result['success'] ?? false);

        if (! $succeeded) {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Unable to reschedule booking right now.',
                'errors' => $result['errors'] ?? null,
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'] ?? 'Booking rescheduled successfully.',
            'data' => $result['data'] ?? $result,
        ]);
    }

    private function findBookingById(int $id, Request $request, AstrologerBookingService $bookingService): ?array
    {
        $token = $this->getUserApiToken($request);

        if (!$token) {
            return null;
        }

        $response = $bookingService->getBookings($token);
        $booking = collect($response['data'] ?? [])->firstWhere('id', $id);

        return is_array($booking) && !empty($booking) ? $booking : null;
    }

    private function getUserApiToken(Request $request): ?string
    {
        return $request->cookie('auth_api_token') ?? session('auth.api_token') ?? session('auth_api_token');
    }

    private function extractSuggestedProductsForBooking(array $response, int $bookingId): array
    {
        $payload = $response['data'] ?? $response;
        $items = [];

        if (isset($payload['data']) && is_array($payload['data'])) {
            $items = $payload['data'];
        } elseif (isset($payload['items']) && is_array($payload['items'])) {
            $items = $payload['items'];
        } elseif (isset($payload['carts']) && is_array($payload['carts'])) {
            $items = $payload['carts'];
        } elseif (array_is_list($payload)) {
            $items = $payload;
        }

        $normalized = array_values(array_filter(array_map(function ($item) use ($bookingId) {
            return is_array($item) ? $this->normalizeSuggestedProductItem($item, $bookingId) : null;
        }, $items)));

        usort($normalized, function ($left, $right) {
            return ($right['id'] ?? 0) <=> ($left['id'] ?? 0);
        });

        return $normalized;
    }

    private function normalizeSuggestedProductItem(array $item, int $bookingId): ?array
    {
        $itemBookingId = $this->firstNumericValue($item, [
            'booking_id',
            'booking.id',
            'cart.booking_id',
        ]);

        if ($itemBookingId === null || (int) $itemBookingId !== $bookingId) {
            return null;
        }

        $productId = $this->firstNumericValue($item, [
            'product_id',
            'product.id',
        ]);

        $variationId = $this->firstNumericValue($item, [
            'product_variation_options_id',
            'variation_id',
            'variation.id',
            'variant_id',
        ]);

        $quantity = $this->firstNumericValue($item, [
            'quantity',
            'qty',
        ]) ?? 1;

        $price = $this->firstNumericValue($item, [
            'product.price',
            'product.product_price',
            'amount',
            'price',
            'variation_price',
        ]);

        $productPrice = $this->firstNumericValue($item, [
            'product.product_price',
            'product.price',
            'price',
        ]);

        $discountRate = $this->firstNumericValue($item, [
            'product.discount_rate',
            'discount_rate',
        ]);

        $discountedPrice = $productPrice;

        if ($productPrice !== null && $discountRate !== null && $discountRate > 0) {
            $discountedPrice = round($productPrice - (($productPrice * $discountRate) / 100), 2);
        }

        if ($discountedPrice === null) {
            $discountedPrice = $price;
        }

        $image = $this->firstScalarValue($item, [
            'product.image',
            'product.image_url',
            'image',
            'image_url',
        ]);

        return [
            'id' => $item['id'] ?? null,
            'product_id' => $productId,
            'variation_id' => $variationId,
            'name' => $this->firstScalarValue($item, [
                'product.name',
                'product.title',
                'product_name',
                'name',
            ]) ?? 'Suggested Product',
            'price' => $discountedPrice,
            'original_price' => $productPrice ?? $price,
            'product_price' => $productPrice,
            'discount_rate' => $discountRate,
            'currency_symbol' => $this->firstScalarValue($item, [
                'currency_symbol',
                'product.currency_symbol',
            ]) ?? '₹',
            'quantity' => $quantity,
            'image' => $image,
            'slug' => $this->firstScalarValue($item, [
                'product.slug',
                'slug',
            ]),
            'url' => $this->firstScalarValue($item, [
                'product.url',
                'product.product_url',
                'url',
            ]),
            'grade' => $this->firstScalarValue($item, [
                'product.product_grade_name',
                'product.product_grade.name',
                'product.grade.name',
                'variation_name',
                'grade',
            ]),
            'ratti' => $this->firstScalarValue($item, [
                'product.ratti',
                'product.weight.ratti',
                'ratti',
            ]),
            'carat' => $this->firstScalarValue($item, [
                'product.carat',
                'product.weight.carat',
                'carat',
            ]),
        ];
    }

    private function firstScalarValue(array $source, array $paths): ?string
    {
        foreach ($paths as $path) {
            $value = data_get($source, $path);

            if (!is_scalar($value)) {
                continue;
            }

            $value = trim((string) $value);
            if ($value === '') {
                continue;
            }

            return $value;
        }

        return null;
    }

    private function firstNumericValue(array $source, array $paths): ?float
    {
        foreach ($paths as $path) {
            $value = data_get($source, $path);

            if (!is_numeric($value)) {
                continue;
            }

            return (float) $value;
        }

        return null;
    }
}
