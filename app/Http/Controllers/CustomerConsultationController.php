<?php

namespace App\Http\Controllers;

use App\Services\ConsultationStateService;
use Illuminate\Http\Request;

class CustomerConsultationController extends Controller
{
    public function video(string $meetingId, Request $request, ConsultationStateService $stateService)
    {
        $bookingId = (int) str_replace('astro-', '', $meetingId);
        $token = $request->cookie('auth_api_token') ?? session('auth.api_token') ?? session('auth_api_token');
        $apiService = app(\App\Services\Api\Clients\AstrologerApiService::class);

        $booking = null;
        if ($token && $bookingId > 0) {
            try {
                $response = $apiService->getBookings($token);
                $booking = collect($response['data'] ?? [])->firstWhere('id', $bookingId);
            } catch (\Throwable $exception) {
                $booking = null;
            }
        }

        if (is_array($booking)) {
            $booking = $stateService->mergeIntoBooking($booking, $bookingId);
        }

        $state = $stateService->get($bookingId);
        $currentStatus = $booking['status'] ?? ($state['status'] ?? 'pending');
        $meetingStartedAt = $booking['meeting_started_at'] ?? ($state['meeting_started_at'] ?? null);

        if ($currentStatus === 'ready_to_start' && $bookingId > 0) {
            $localState = $stateService->markInProgress($bookingId, null, $meetingId);
            $currentStatus = 'in_progress';
            $meetingStartedAt = $localState['meeting_started_at'] ?? $meetingStartedAt;

            if ($token) {
                try {
                    $apiService->joinVideoConsultation($bookingId, $token);
                } catch (\Throwable $exception) {
                    // Keep the local transition even if the external API is unavailable.
                }
            }
        }

        return view('customer.video-consultation', [
            'meetingId' => $meetingId,
            'initialConsultationState' => [
                'bookingId' => $bookingId > 0 ? $bookingId : null,
                'status' => $currentStatus,
                'meetingStartedAt' => $meetingStartedAt,
            ],
        ]);
    }
}
