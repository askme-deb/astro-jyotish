<?php

namespace App\Http\Controllers;

use App\Services\AstrologerBookingService;
use App\Services\ConsultationBroadcastService;
use App\Services\ConsultationStateService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\CustomerVideoConsultationLink;

class AstrologerAppointmentDetailsController extends Controller
{
    private const STARTABLE_VIDEO_STATUSES = ['confirmed'];
    private const ENDABLE_VIDEO_STATUSES = ['ready_to_start', 'in_progress'];
    private const CANCELLED_APPOINTMENT_MESSAGE = 'This appointment has been cancelled. All actions are disabled.';
    private ?array $productGradeLookup = null;

    public function start($id, Request $request)
    {
         $userId = session('api_user_id');
            if (!$userId) {
                return redirect()->route('home');
            }
        $appointment = $this->getAppointmentById($id);
        if ($appointment && $this->isAppointmentCancelled($appointment)) {
            return Redirect::back()->with('error', self::CANCELLED_APPOINTMENT_MESSAGE);
        }

        // TODO: Implement logic to start the consultation
        // Example: Update appointment status, log start time, etc.
        return Redirect::back()->with('success', 'Consultation started.');
    }

    public function startVideo($id, Request $request)
    {
        $appointment = $this->getAppointmentById($id);
        if (!$appointment) {
            return Redirect::back()->with('error', 'Appointment not found.');
        }

        if ($this->isAppointmentCancelled($appointment)) {
            return Redirect::back()->with('error', self::CANCELLED_APPOINTMENT_MESSAGE);
        }

        // Example: Call VideoSDK API to create a meeting room
        $apiKey = config('services.videosdk.api_key');
        $apiSecret = config('services.videosdk.api_secret');
        $apiUrl = 'https://api.videosdk.live/v2/rooms';

        $client = new \GuzzleHttp\Client();
        try {
            $response = $client->post($apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiSecret,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'roomId' => 'astro-' . $id,
                    'name' => 'Consultation Room #' . $id,
                ],
            ]);
            $data = json_decode($response->getBody(), true);
            $meetingUrl = 'https://app.videosdk.live/rooms/' . ($data['roomId'] ?? ('astro-' . $id));
            // Optionally: Save $meetingUrl to the appointment in DB
            session(['video_call_started' => true]);
            return Redirect::back()->with('success', 'Video call started. <a href="' . $meetingUrl . '" target="_blank">Join Meeting</a>');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Failed to start video call: ' . $e->getMessage());
        }
    }

    public function suggestProduct($id, Request $request)
    {
        if (!$this->currentUserIsAstrologer()) {
            return $this->respondProductSearchError($request, 'Only astrologers can search suggested products.', 403);
        }

        $appointment = $this->getAppointmentById($id);
        if (!$appointment) {
            return $this->respondProductSearchError($request, 'Appointment not found.', 404);
        }

        if ($this->isAppointmentCancelled($appointment)) {
            return $this->respondProductSearchError($request, self::CANCELLED_APPOINTMENT_MESSAGE, 422, [
                'cancelled' => true,
            ]);
        }

        if ($this->isAppointmentFinalized($appointment)) {
            return $this->respondProductSearchError($request, 'This appointment has already been finalized. Suggested products can no longer be changed.', 422, [
                'finalized' => true,
            ]);
        }

        $validator = Validator::make($request->all(), [
            'q' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', 'min:1'],
            'product_grade_id' => ['nullable', 'integer', 'min:1'],
            'ratti' => ['nullable', 'numeric', 'min:0'],
            'carat' => ['nullable', 'numeric', 'min:0'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'gte:min_price'],
            'in_stock' => ['nullable', 'boolean'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please correct the product search filters.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            return Redirect::back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();
        $query = $this->buildProductSearchQuery($validated);
        if (empty($query)) {
            return $this->respondProductSearchError($request, 'Provide at least one product search filter.', 422);
        }

        $result = $this->getApiService()->searchProducts($query, $this->getApiToken());
        if (isset($result['error']) && $result['error']) {
            return $this->respondProductSearchError($request, $result['message'] ?? 'Failed to search products.', 500);
        }

        $normalized = $this->normalizeProductSearchResponse($result);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $normalized['products'] ? 'Products fetched successfully.' : 'No products matched the selected filters.',
                'products' => $normalized['products'],
                'meta' => $normalized['meta'],
                'raw' => $result,
            ]);
        }

        return Redirect::back()->with('success', 'Products fetched successfully.');
    }

    public function saveNotes($id, Request $request)
    {
        if (!$this->currentUserIsAstrologer()) {
            return $this->respondNoteError($request, 'Only astrologers can save consultation notes.', 403);
        }

        $appointment = $this->getAppointmentById($id);
        if (!$appointment) {
            return $this->respondNoteError($request, 'Appointment not found.', 404);
        }

        if ($this->isAppointmentCancelled($appointment)) {
            return $this->respondNoteError($request, self::CANCELLED_APPOINTMENT_MESSAGE, 422);
        }

        if ($this->isAppointmentFinalized($appointment)) {
            return $this->respondNoteError($request, 'This appointment has already been finalized. Notes can no longer be edited.', 422);
        }

        $validator = Validator::make($request->all(), [
            'astrologer_note' => ['nullable', 'string', 'max:5000'],
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please correct the highlighted note field.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            return Redirect::back()->withErrors($validator)->withInput();
        }

        $astrologerNote = trim((string) ($validator->validated()['astrologer_note'] ?? ''));
        $result = $this->getApiService()->saveAstrologerNote($id, $astrologerNote, $this->getApiToken());

        if (isset($result['error']) && $result['error']) {
            return $this->respondNoteError($request, $result['message'] ?? 'Failed to save notes.', 500);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Notes saved successfully.',
                'data' => $result,
                'astrologer_note' => $astrologerNote,
            ]);
        }

        return Redirect::back()->with('success', $result['message'] ?? 'Notes saved successfully.');
    }

    public function finalizeNotes($id, Request $request)
    {
        if (!$this->currentUserIsAstrologer()) {
            return $this->respondNoteError($request, 'Only astrologers can finalize consultation notes.', 403);
        }

        $appointment = $this->getAppointmentById($id);
        if (!$appointment) {
            return $this->respondNoteError($request, 'Appointment not found.', 404);
        }

        if ($this->isAppointmentCancelled($appointment)) {
            return $this->respondNoteError($request, self::CANCELLED_APPOINTMENT_MESSAGE, 422);
        }

        if ($this->isAppointmentFinalized($appointment)) {
            return $this->respondNoteError($request, 'This appointment has already been finalized.', 422);
        }

        $result = $this->getApiService()->finalizeAstrologerNote($id, $this->getApiToken());

        if (isset($result['error']) && $result['error']) {
            return $this->respondNoteError($request, $result['message'] ?? 'Failed to finalize notes.', 500);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Notes finalized successfully.',
                'data' => $result,
                'astrologer_note_status' => 'finalized',
                'final_confirmation_from_astrologer' => true,
            ]);
        }

        return Redirect::back()->with('success', $result['message'] ?? 'Notes finalized successfully.');
    }

    public function downloadNotesPdf($id, Request $request)
    {
        if (!$this->currentUserIsAstrologer()) {
            return Redirect::back()->with('error', 'Only astrologers can download consultation notes.');
        }

        $appointment = $this->getAppointmentById($id);

        if (!is_array($appointment) || $appointment === [] || (count($appointment) === 1 && isset($appointment['id']))) {
            return Redirect::back()->with('error', 'Appointment not found.');
        }

        if ($this->isAppointmentCancelled($appointment)) {
            return Redirect::back()->with('error', self::CANCELLED_APPOINTMENT_MESSAGE);
        }

        $filename = 'consultation-note-bkng' . (int) $appointment['id'] . '.pdf';

        $pdf = Pdf::loadView('astrologer.appointment-note-pdf', [
            'appointment' => $appointment,
            'generatedAt' => now(),
            'logoPath' => public_path('assets/images/Logo.png'),
            'appName' => config('app.name', 'Astro Consultant'),
        ])->setPaper('a4');

        return $pdf->download($filename);
    }

    public function addSuggestedProduct($id, Request $request)
    {
        if (!$this->currentUserIsAstrologer()) {
            return $this->respondSuggestedProductError($request, 'Only astrologers can suggest products.', 403);
        }

        $appointment = $this->getAppointmentById($id);
        if (!$appointment) {
            return $this->respondSuggestedProductError($request, 'Appointment not found.', 404);
        }

        if ($this->isAppointmentCancelled($appointment)) {
            return $this->respondSuggestedProductError($request, self::CANCELLED_APPOINTMENT_MESSAGE, 422, [
                'cancelled' => true,
            ]);
        }

        if ($this->isAppointmentFinalized($appointment)) {
            return $this->respondSuggestedProductError($request, 'This appointment has already been finalized. Suggested products can no longer be changed.', 422, [
                'finalized' => true,
            ]);
        }

        $validator = Validator::make($request->all(), [
            'product_id' => ['required', 'integer', 'min:1'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'variation_id' => ['nullable', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please provide a valid product suggestion payload.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            return Redirect::back()->withErrors($validator)->withInput();
        }

        $customerId = $this->resolveAppointmentUserId($appointment);
        $astrologerId = $this->resolveAppointmentAstrologerId($appointment);

        if (!$customerId) {
            return $this->respondSuggestedProductError($request, 'Unable to determine the booking customer for this suggestion.', 422);
        }

        if (!$astrologerId) {
            return $this->respondSuggestedProductError($request, 'Unable to determine the astrologer for this suggestion.', 422);
        }

        $validated = $validator->validated();
        $payload = [
            'product_id' => (int) $validated['product_id'],
            'quantity' => (int) ($validated['quantity'] ?? 1),
            'astrologer_id' => $astrologerId,
            'user_id' => $customerId,
            'booking_id' => (int) $appointment['id'],
        ];

        if (!empty($validated['variation_id'])) {
            $payload['variation_id'] = (int) $validated['variation_id'];
        }

        $result = $this->getApiService()->addSuggestedProduct($payload, $this->getApiToken());

        if (isset($result['error']) && $result['error']) {
            return $this->respondSuggestedProductError($request, $result['message'] ?? 'Failed to suggest product.', 500);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Product suggested successfully.',
                'data' => $result,
                'payload' => $payload,
            ]);
        }

        return Redirect::back()->with('success', $result['message'] ?? 'Product suggested successfully.');
    }

    public function removeSuggestedProduct($id, Request $request)
    {
        if (!$this->currentUserIsAstrologer()) {
            return $this->respondSuggestedProductError($request, 'Only astrologers can remove suggested products.', 403);
        }

        $appointment = $this->getAppointmentById($id);
        if (!$appointment) {
            return $this->respondSuggestedProductError($request, 'Appointment not found.', 404);
        }

        if ($this->isAppointmentCancelled($appointment)) {
            return $this->respondSuggestedProductError($request, self::CANCELLED_APPOINTMENT_MESSAGE, 422, [
                'cancelled' => true,
            ]);
        }

        if ($this->isAppointmentFinalized($appointment)) {
            return $this->respondSuggestedProductError($request, 'This appointment has already been finalized. Suggested products can no longer be changed.', 422, [
                'finalized' => true,
            ]);
        }

        $validator = Validator::make($request->all(), [
            'cart_id' => ['required', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please provide a valid suggested product to remove.',
                    'errors' => $validator->errors(),
                ], 422);
            }

            return Redirect::back()->withErrors($validator)->withInput();
        }

        $astrologerId = $this->resolveAppointmentAstrologerId($appointment);
        if (!$astrologerId) {
            return $this->respondSuggestedProductError($request, 'Unable to determine the astrologer for this suggestion.', 422);
        }

        $payload = [
            'cart_id' => (int) $validator->validated()['cart_id'],
            'astrologer_id' => (int) $astrologerId,
            'booking_id' => (int) $appointment['id'],
        ];

        $result = $this->getApiService()->removeSuggestedProduct($payload, $this->getApiToken());

        if (isset($result['error']) && $result['error']) {
            return $this->respondSuggestedProductError($request, $result['message'] ?? 'Failed to remove suggested product.', 500);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Suggested product removed successfully.',
                'data' => $result,
                'payload' => $payload,
            ]);
        }

        return Redirect::back()->with('success', $result['message'] ?? 'Suggested product removed successfully.');
    }

    public function cancel($id, Request $request, AstrologerBookingService $bookingService)
    {
        if (!$this->currentUserIsAstrologer()) {
            return $this->jsonOrRedirectError($request, 'Only astrologers can cancel appointments.', 403);
        }

        $appointment = $this->getAppointmentById($id);
        if (!$appointment) {
            return $this->jsonOrRedirectError($request, 'Appointment not found.', 404);
        }

        $status = (string) ($appointment['status'] ?? '');
        $blockedStatuses = config('booking.cancel_blocked_statuses', []);

        if (in_array($status, $blockedStatuses, true)) {
            return $this->jsonOrRedirectError($request, 'This appointment can no longer be cancelled.', 422, [
                'status' => $status,
            ]);
        }

        $result = $bookingService->cancelBooking((int) $id, $this->getApiToken());
        $succeeded = !($result['error'] ?? false) && (bool) ($result['status'] ?? $result['success'] ?? true);

        if (!$succeeded) {
            return $this->jsonOrRedirectError($request, $result['message'] ?? 'Unable to cancel appointment right now.', 422);
        }

        app(ConsultationStateService::class)->forget((int) $id);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $result['message'] ?? 'Appointment cancelled successfully.',
                'status' => 'cancelled',
                'data' => $result['data'] ?? $result,
            ]);
        }

        return Redirect::back()->with('success', $result['message'] ?? 'Appointment cancelled successfully.');
    }

    public function reschedule($id, Request $request, AstrologerBookingService $bookingService)
    {
        if (!$this->currentUserIsAstrologer()) {
            return $this->jsonError('Only astrologers can reschedule appointments.', 403);
        }

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

        $result = $bookingService->rescheduleAstrologerBooking((int) $id, $payload, $token);
        $succeeded = ! ($result['error'] ?? false) && (bool) ($result['status'] ?? $result['success'] ?? false);

        if (!$succeeded) {
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

    public function video($id)
    {
        // Fetch real appointment details from DB or service
        $appointment = $this->getAppointmentById($id); // Replace with your actual fetching logic
        if (!$appointment) {
            return back()->with('error', 'Appointment not found.');
        }

        if ($this->isAppointmentCancelled($appointment)) {
            return back()->with('error', self::CANCELLED_APPOINTMENT_MESSAGE);
        }

        $productCategories = $this->getProductCategories();
        $productGrades = $this->getProductGrades();
        // Reset the session flag after showing the video page
        //dd($appointment);
        session()->forget('video_call_started');
        return view('astrologer.video-consultation', compact('appointment', 'productCategories', 'productGrades'));
    }

    public function sendCustomerJoinLink($id)
    {
        $appointment = $this->getAppointmentById($id); // Assume this method exists or replace with actual logic
        if ($appointment && $this->isAppointmentCancelled($appointment)) {
            return back()->with('error', self::CANCELLED_APPOINTMENT_MESSAGE);
        }

        if (!$appointment || empty($appointment['customer_email'])) {
            return back()->with('error', 'Customer email not found.');
        }
        $meetingId = 'astro-' . $appointment['id'];
        $link = route('customer.consultation.video', ['meetingId' => $meetingId]);
        Mail::to($appointment['customer_email'])->send(new CustomerVideoConsultationLink($appointment, $link));
        return back()->with('success', 'Join link sent to customer email.');
    }


        /**
     * Fetch appointment with user details by ID.
     * Replace with your actual fetching logic as needed.
     */
    private function getAppointmentById($id)
    {
        $token = $this->getApiToken();
        $astrologerId = session('api_user_id');
        $apiService = $this->getApiService();
        $stateService = app(ConsultationStateService::class);
        $appointment = null;
        $astrologer = null;

        if ($astrologerId && $this->currentUserIsAstrologer()) {
            $response = $apiService->getAstrologerBookings($astrologerId, $token);
            $appointment = collect($response['data'] ?? [])->firstWhere('id', (int) $id);
            $astrologer = is_array($response['astrologer'] ?? null) ? $response['astrologer'] : null;
        } else {
            $response = $apiService->getBookings($token);
            $appointment = collect($response['data'] ?? [])->firstWhere('id', (int) $id);
        }

        if (!is_array($appointment)) {
            return null;
        }

        if ($astrologer !== null) {
            $appointment['astrologer'] = $appointment['astrologer'] ?? $astrologer;
            $appointment['astrologer_id'] = $appointment['astrologer_id'] ?? ($astrologer['id'] ?? null);
        }

        return $stateService->mergeIntoBooking($appointment, (int) $id);
    }


    /**
     * AJAX: Start a video consultation session (PATCH via API).
     */
    public function ajaxStartVideoSession($id, Request $request)
    {
        if (!$this->currentUserIsAstrologer()) {
            return $this->jsonError('Only astrologers can start video sessions.', 403);
        }

        $appointment = $this->getAppointmentById($id);
        if (!$appointment) {
            return $this->jsonError('Appointment not found.', 404);
        }

        if ($this->isAppointmentCancelled($appointment)) {
            return $this->jsonError(self::CANCELLED_APPOINTMENT_MESSAGE, 422, ['status' => 'cancelled']);
        }

        $status = $appointment['status'] ?? null;
        if (!in_array($status, self::STARTABLE_VIDEO_STATUSES, true)) {
            return $this->jsonError($this->startBlockedMessage($status), 422, ['status' => $status]);
        }

        $token = $this->getApiToken();
        $apiService = $this->getApiService();
        $result = $apiService->startVideoConsultation($id, $token);
        $appointmentDuration = isset($appointment['duration']) && is_numeric($appointment['duration']) ? (int) $appointment['duration'] : null;
        $requestDuration = $request->filled('duration_minutes') ? (int) $request->input('duration_minutes') : null;
        $localState = app(ConsultationStateService::class)->markReadyToStart(
            (int) $id,
            'astro-' . $id,
            ($appointmentDuration && $appointmentDuration > 0)
                ? $appointmentDuration
                : (($requestDuration && $requestDuration > 0) ? $requestDuration : null)
        );
        app(ConsultationBroadcastService::class)->broadcastReadyToStart(
            array_merge($appointment, $localState, ['id' => (int) $id]),
            isset($localState['duration']) && is_numeric($localState['duration']) ? (int) $localState['duration'] : null
        );

        if (isset($result['error']) && $result['error']) {
            return response()->json([
                'success' => true,
                'message' => 'Video consultation is ready for the customer to join.',
                'data' => $result,
                'status' => 'ready_to_start',
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => $result['message'] ?? 'Video consultation is ready for the customer to join.',
            'data' => $result,
            'status' => 'ready_to_start',
        ]);
    }

    /**
     * AJAX: End a video consultation session (PATCH via API).
     */
    public function ajaxEndVideoSession($id, Request $request)
    {
        if (!$this->currentUserIsAstrologer()) {
            return $this->jsonError('Only astrologers can end video sessions.', 403);
        }

        $appointment = $this->getAppointmentById($id);
        if (!$appointment) {
            return $this->jsonError('Appointment not found.', 404);
        }

        if ($this->isAppointmentCancelled($appointment)) {
            return $this->jsonError(self::CANCELLED_APPOINTMENT_MESSAGE, 422, ['status' => 'cancelled']);
        }

        $status = $appointment['status'] ?? null;
        if (!in_array($status, self::ENDABLE_VIDEO_STATUSES, true)) {
            return $this->jsonError($this->endBlockedMessage($status), 422, ['status' => $status]);
        }

        $token = $this->getApiToken();
        $apiService = $this->getApiService();
        $result = $apiService->endVideoConsultation($id, $token);
        $localState = app(ConsultationStateService::class)->markCompleted((int) $id);
        app(ConsultationBroadcastService::class)->broadcastEnded(
            array_merge($appointment, $localState, ['id' => (int) $id])
        );

        if (isset($result['error']) && $result['error']) {
            return response()->json([
                'success' => true,
                'message' => 'Video consultation ended successfully.',
                'data' => $result,
                'status' => 'completed',
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => $result['message'] ?? 'Video consultation ended successfully.',
            'data' => $result,
            'status' => 'completed',
        ]);
    }

    /**
     * End the video consultation session after the user leaves the meeting UI.
     */
    public function leaveVideo($id)
    {
        if (!$this->currentUserIsAstrologer()) {
            return redirect()
                ->route('astrologer.appointment.video', ['id' => $id])
                ->with('error', 'Only astrologers can end the video session.');
        }

        $appointment = $this->getAppointmentById($id);
        if (!$appointment) {
            return redirect()
                ->route('astrologer.appointments')
                ->with('error', 'Appointment not found.');
        }

        if ($this->isAppointmentCancelled($appointment)) {
            return redirect()
                ->route('astrologer.appointment.video', ['id' => $id])
                ->with('error', self::CANCELLED_APPOINTMENT_MESSAGE);
        }

        $status = $appointment['status'] ?? null;
        if (in_array($status, self::ENDABLE_VIDEO_STATUSES, true)) {
            $this->getApiService()->endVideoConsultation($id, $this->getApiToken());
            $localState = app(ConsultationStateService::class)->markCompleted((int) $id);
            app(ConsultationBroadcastService::class)->broadcastEnded(
                array_merge($appointment, $localState, ['id' => (int) $id])
            );
        }

        return redirect()
            ->route('astrologer.appointment.video', ['id' => $id])
            ->with('video_consultation_ended', true)
            ->with('success', 'Video consultation ended successfully.');
    }

        /**
     * AJAX: Get current session status for customer polling.
     */
    public function ajaxStatus($id, Request $request)
    {
        $booking = $this->getAppointmentById($id) ?? [];
        $bookingDuration = isset($booking['duration']) ? (int) $booking['duration'] : 0;
        return response()->json([
            'success' => true,
            'status' => $booking['status'] ?? null,
            'meetingStartedAt' => $booking['meeting_started_at'] ?? null,
            'durationMinutes' => $bookingDuration > 0 ? $bookingDuration : null,
        ]);
    }

    private function getApiToken(): ?string
    {
        return request()->cookie('auth_api_token') ?? session('auth.api_token') ?? session('auth_api_token');
    }

    private function getApiService(): \App\Services\Api\Clients\AstrologerApiService
    {
        return app(\App\Services\Api\Clients\AstrologerApiService::class);
    }

    private function currentUserIsAstrologer(): bool
    {
        $roles = session('auth.roles', []);
        return in_array('Astrologer', $roles, true);
    }

    private function isAppointmentCancelled(array $appointment): bool
    {
        return strtolower((string) ($appointment['status'] ?? '')) === 'cancelled';
    }

    private function isAppointmentFinalized(array $appointment): bool
    {
        $finalConfirmation = $appointment['final_confirmation_from_astrologer'] ?? false;
        $noteStatus = strtolower((string) ($appointment['astrologer_note_status'] ?? ''));

        return filter_var($finalConfirmation, FILTER_VALIDATE_BOOLEAN) || $noteStatus === 'finalized';
    }

    private function jsonError(string $message, int $statusCode, array $extra = [])
    {
        return response()->json(array_merge([
            'success' => false,
            'message' => $message,
        ], $extra), $statusCode);
    }

    private function startBlockedMessage(?string $status): string
    {
        if ($status === 'cancelled') {
            return self::CANCELLED_APPOINTMENT_MESSAGE;
        }

        if ($status === 'ready_to_start') {
            return 'Video session is already ready for the customer to join.';
        }

        if ($status === 'in_progress') {
            return 'Video session is already in progress.';
        }

        if ($status === 'completed') {
            return 'Completed consultations cannot be started again.';
        }

        return 'This consultation cannot be started in its current state.';
    }

    private function endBlockedMessage(?string $status): string
    {
        if ($status === 'cancelled') {
            return self::CANCELLED_APPOINTMENT_MESSAGE;
        }

        if ($status === 'completed') {
            return 'Video session has already been completed.';
        }

        if ($status === 'confirmed') {
            return 'Video session has not started yet.';
        }

        return 'This consultation cannot be ended in its current state.';
    }

    private function respondNoteError(Request $request, string $message, int $statusCode)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
            ], $statusCode);
        }

        return Redirect::back()->with('error', $message);
    }

    private function respondProductSearchError(Request $request, string $message, int $statusCode, array $extra = [])
    {
        if ($request->expectsJson()) {
            return response()->json(array_merge([
                'success' => false,
                'message' => $message,
            ], $extra), $statusCode);
        }

        return Redirect::back()->with('error', $message);
    }

    private function respondSuggestedProductError(Request $request, string $message, int $statusCode, array $extra = [])
    {
        if ($request->expectsJson()) {
            return response()->json(array_merge([
                'success' => false,
                'message' => $message,
            ], $extra), $statusCode);
        }

        return Redirect::back()->with('error', $message);
    }

    private function jsonOrRedirectError(Request $request, string $message, int $statusCode, array $extra = [])
    {
        if ($request->expectsJson()) {
            return response()->json(array_merge([
                'success' => false,
                'message' => $message,
            ], $extra), $statusCode);
        }

        return Redirect::back()->with('error', $message);
    }

    private function buildProductSearchQuery(array $validated): array
    {
        $query = [];

        if (!empty($validated['q'])) {
            $query['q'] = trim((string) $validated['q']);
        }
        if (!empty($validated['category_id'])) {
            $query['category_id'] = [(int) $validated['category_id']];
        }
        if (!empty($validated['product_grade_id'])) {
            $query['product_grade_id'] = (int) $validated['product_grade_id'];
        }
        if (isset($validated['ratti']) && $validated['ratti'] !== null && $validated['ratti'] !== '') {
            $query['ratti'] = (float) $validated['ratti'];
        }
        if (isset($validated['carat']) && $validated['carat'] !== null && $validated['carat'] !== '') {
            $query['carat'] = (float) $validated['carat'];
        }
        if (isset($validated['min_price']) && $validated['min_price'] !== null && $validated['min_price'] !== '') {
            $query['min_price'] = (float) $validated['min_price'];
        }
        if (isset($validated['max_price']) && $validated['max_price'] !== null && $validated['max_price'] !== '') {
            $query['max_price'] = (float) $validated['max_price'];
        }
        if (array_key_exists('in_stock', $validated) && $validated['in_stock'] !== null && $validated['in_stock'] !== '') {
            $query['in_stock'] = $validated['in_stock'] ? 1 : 0;
        }
        if (!empty($validated['per_page'])) {
            $query['per_page'] = (int) $validated['per_page'];
        }

        return $query;
    }

    private function normalizeProductSearchResponse(array $response): array
    {
        $payload = $response['data'] ?? $response;
        $items = [];

        if (isset($payload['data']) && is_array($payload['data'])) {
            $items = $payload['data'];
        } elseif (isset($payload['items']) && is_array($payload['items'])) {
            $items = $payload['items'];
        } elseif (isset($payload['products']) && is_array($payload['products'])) {
            $items = $payload['products'];
        } elseif (array_is_list($payload)) {
            $items = $payload;
        }

        $products = array_values(array_filter(array_map(function ($item) {
            return is_array($item) ? $this->normalizeProductItem($item) : null;
        }, $items)));

        return [
            'products' => $products,
            'meta' => [
                'total' => $payload['total'] ?? count($products),
                'current_page' => $payload['current_page'] ?? 1,
                'last_page' => $payload['last_page'] ?? 1,
                'per_page' => $payload['per_page'] ?? count($products),
            ],
        ];
    }

    private function normalizeProductItem(array $item): array
    {
        $image = $this->firstFilledValue($item, [
            'image',
            'image_url',
            'thumbnail',
            'thumbnail_url',
            'media.image',
            'media.thumbnail',
        ]);

        if (is_array($image)) {
            $image = $image['url'] ?? $image['src'] ?? null;
        }

        $category = $this->firstFilledScalar($item, [
            'category_name',
            'category.name',
            'category.title',
            'category_title',
            'subcategory.name',
        ]);

        $brand = $this->firstFilledScalar($item, [
            'brand_name',
            'brand.name',
            'brand.title',
            'brand_title',
        ]);

        $productGradeId = $this->firstFilledValue($item, [
            'product_grade_id',
            'product_grade.id',
            'grade_id',
            'grade.id',
        ]);

        $grade = $this->firstFilledScalar($item, [
            'product_grade_name',
            'product_grade.name',
            'product_grade.title',
            'grade.name',
            'grade.title',
            'grade_name',
            'product_grade',
        ]);

        if (!$grade && $productGradeId !== null && $productGradeId !== '') {
            $grade = $this->resolveProductGradeName($productGradeId);
        }

        $price = $this->firstFilledValue($item, [
            'final_price',
            'sale_price',
            'price',
            'amount',
            'pricing.final_price',
            'pricing.sale_price',
        ]);

        $stockQuantity = $this->firstFilledValue($item, [
            'stock_quantity',
            'qty',
            'quantity',
            'inventory.qty',
            'inventory.quantity',
        ]);

        $inStock = $item['in_stock'] ?? (is_numeric($stockQuantity) ? ((int) $stockQuantity > 0) : null);

        $ratti = $this->firstFilledValue($item, [
            'ratti',
            'ratti_value',
            'weight.ratti',
            'weight.ratti_value',
            'gemstone.ratti',
            'gemstone.ratti_value',
            'attributes.ratti',
            'product_details.ratti',
        ]);

        $carat = $this->firstFilledValue($item, [
            'carat',
            'carat_value',
            'weight.carat',
            'weight.carat_value',
            'gemstone.carat',
            'gemstone.carat_value',
            'attributes.carat',
            'product_details.carat',
        ]);

        return [
            'id' => $item['id'] ?? null,
            'name' => $item['name'] ?? $item['title'] ?? $item['product_name'] ?? 'Unnamed Product',
            'category' => $category,
            'brand' => $brand,
            'grade' => is_scalar($grade) ? (string) $grade : null,
            'product_grade_id' => is_numeric($productGradeId) ? (int) $productGradeId : (is_scalar($productGradeId) ? (string) $productGradeId : null),
            'variation_id' => $this->normalizeIntegerValue($this->firstFilledValue($item, [
                'variation_id',
                'variant_id',
                'default_variation_id',
                'variation.id',
                'variant.id',
                'default_variation.id',
            ])),
            'price' => is_numeric($price) ? (float) $price : null,
            'currency_symbol' => $item['currency_symbol'] ?? '₹',
            'ratti' => is_numeric($ratti) ? (float) $ratti : (is_scalar($ratti) ? (string) $ratti : null),
            'carat' => is_numeric($carat) ? (float) $carat : (is_scalar($carat) ? (string) $carat : null),
            'in_stock' => (bool) $inStock,
            'stock_quantity' => is_numeric($stockQuantity) ? (int) $stockQuantity : null,
            'image' => is_string($image) ? $image : null,
            'url' => $this->firstFilledScalar($item, ['url', 'product_url', 'permalink']),
        ];
    }

    private function firstFilledValue(array $source, array $paths): mixed
    {
        foreach ($paths as $path) {
            $value = data_get($source, $path);

            if ($value === null) {
                continue;
            }

            if (is_string($value) && trim($value) === '') {
                continue;
            }

            if (is_array($value) && $value === []) {
                continue;
            }

            return $value;
        }

        return null;
    }

    private function firstFilledScalar(array $source, array $paths): ?string
    {
        $value = $this->firstFilledValue($source, $paths);

        if (!is_scalar($value)) {
            return null;
        }

        $stringValue = trim((string) $value);

        return $stringValue === '' ? null : $stringValue;
    }

    private function resolveProductGradeName(mixed $gradeId): ?string
    {
        if ($this->productGradeLookup === null) {
            $this->productGradeLookup = [];

            foreach ($this->getProductGrades() as $grade) {
                if (empty($grade['id']) || empty($grade['name'])) {
                    continue;
                }

                $this->productGradeLookup[(string) $grade['id']] = (string) $grade['name'];
            }
        }

        $lookupKey = is_scalar($gradeId) ? (string) $gradeId : null;

        return $lookupKey !== null ? ($this->productGradeLookup[$lookupKey] ?? null) : null;
    }

    private function normalizeIntegerValue(mixed $value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }

    private function resolveAppointmentUserId(array $appointment): ?int
    {
        return $this->normalizeIntegerValue($this->firstFilledValue($appointment, [
            'user_id',
            'customer_id',
            'user.id',
            'customer.id',
            'customer.user_id',
        ]));
    }

    private function resolveAppointmentAstrologerId(array $appointment): ?int
    {
        $sessionUserId = session('api_user_id') ?? session('auth.user.id');

        return $this->normalizeIntegerValue($this->firstFilledValue($appointment, [
            'astrologer_id',
            'astrologer.id',
            'assigned_astrologer_id',
        ]) ?? $sessionUserId);
    }

    private function getProductCategories(): array
    {
        $response = $this->getApiService()->getCategories($this->getApiToken());
        $payload = $response['data'] ?? $response;

        if (isset($payload['data']) && is_array($payload['data'])) {
            $payload = $payload['data'];
        } elseif (isset($payload['categories']) && is_array($payload['categories'])) {
            $payload = $payload['categories'];
        }

        if (!is_array($payload)) {
            return [];
        }

        $flatCategories = [];
        $groupedCategories = [];

        foreach ($payload as $item) {
            if (!is_array($item) || empty($item['id']) || empty($item['name'])) {
                continue;
            }

            $normalized = [
                'id' => (int) $item['id'],
                'name' => (string) $item['name'],
                'parent_id' => isset($item['parent_id']) && is_numeric($item['parent_id']) ? (int) $item['parent_id'] : null,
            ];

            $flatCategories[$normalized['id']] = $normalized;

            if (!empty($item['children']) && is_array($item['children'])) {
                $options = [];

                foreach ($item['children'] as $child) {
                    if (!is_array($child) || empty($child['id']) || empty($child['name'])) {
                        continue;
                    }

                    $options[] = [
                        'id' => (int) $child['id'],
                        'name' => (string) $child['name'],
                    ];

                    $flatCategories[(int) $child['id']] = [
                        'id' => (int) $child['id'],
                        'name' => (string) $child['name'],
                        'parent_id' => $normalized['id'],
                    ];
                }

                if ($options) {
                    $groupedCategories[] = [
                        'label' => $normalized['name'],
                        'options' => $options,
                    ];
                }
            }
        }

        if ($groupedCategories) {
            $usedOptionIds = collect($groupedCategories)
                ->flatMap(function ($group) {
                    return collect($group['options'] ?? [])->pluck('id');
                })
                ->all();

            $standaloneCategories = array_values(array_filter($flatCategories, function ($category) use ($usedOptionIds) {
                return !in_array($category['id'], $usedOptionIds, true) && $category['parent_id'] === null;
            }));

            if ($standaloneCategories) {
                $groupedCategories[] = [
                    'label' => 'Other Categories',
                    'options' => array_map(function ($category) {
                        return [
                            'id' => $category['id'],
                            'name' => $category['name'],
                        ];
                    }, $standaloneCategories),
                ];
            }

            return $groupedCategories;
        }

        $rootCategories = array_values(array_filter($flatCategories, function ($category) {
            return $category['parent_id'] === null;
        }));

        $childGroups = [];
        foreach ($flatCategories as $category) {
            if ($category['parent_id'] === null) {
                continue;
            }

            $parentName = $flatCategories[$category['parent_id']]['name'] ?? 'Other Categories';
            $childGroups[$parentName][] = [
                'id' => $category['id'],
                'name' => $category['name'],
            ];
        }

        $groups = [];
        foreach ($childGroups as $label => $options) {
            $groups[] = [
                'label' => $label,
                'options' => $options,
            ];
        }

        if ($rootCategories) {
            $groups[] = [
                'label' => 'Other Categories',
                'options' => array_map(function ($category) {
                    return [
                        'id' => $category['id'],
                        'name' => $category['name'],
                    ];
                }, $rootCategories),
            ];
        }

        return $groups;
    }

    private function getProductGrades(): array
    {
        $response = $this->getApiService()->getProductGrades($this->getApiToken());
        $payload = $response['data'] ?? $response;

        if (isset($payload['data']) && is_array($payload['data'])) {
            $payload = $payload['data'];
        } elseif (isset($payload['product_grades']) && is_array($payload['product_grades'])) {
            $payload = $payload['product_grades'];
        } elseif (isset($payload['items']) && is_array($payload['items'])) {
            $payload = $payload['items'];
        }

        if (!is_array($payload)) {
            return [];
        }

        $grades = [];

        foreach ($payload as $item) {
            if (!is_array($item) || empty($item['id']) || empty($item['name'])) {
                continue;
            }

            $grades[] = [
                'id' => (int) $item['id'],
                'name' => (string) $item['name'],
            ];
        }

        usort($grades, function ($left, $right) {
            return strcasecmp($left['name'], $right['name']);
        });

        return $grades;
    }
}
