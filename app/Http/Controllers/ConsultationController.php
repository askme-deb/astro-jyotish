<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function show()
    {
        return view('consultation');
    }
    protected $bookingService;

        public function __construct(\App\Services\AstrologerBookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    // Add index() method for GET /consultation
    public function index()
    {
        $astrologers = $this->bookingService->getAstrologers();
       // dd($astrologers);
        return view('consultation', compact('astrologers'));
    }

    public function getSlots(Request $request)
    {
        $request->validate([
            'astrologer_id' => 'required|integer',
            'date' => 'required|date',
        ]);
        try {
            $result = $this->bookingService->getSlots($request->astrologer_id, $request->date);
            $slots = [];
            if (is_array($result) && isset($result['slots']) && is_array($result['slots'])) {
                foreach ($result['slots'] as $slot) {
                    $slots[] = [
                        'slot_id' => $slot['id'] ?? '',
                        'start_time' => $slot['start_time'] ?? '',
                        'end_time' => $slot['end_time'] ?? '',
                    ];
                }
            }
            return response()->json(['success' => true, 'slots' => $slots]);
        } catch (\Exception $e) {
            \Log::error('Slot fetch error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Unable to fetch slots.'], 500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            //'user_id' => 'nullable|integer',
            'name' => 'required|string',
            'phone' => 'required|string',
            'user_email' => 'required|email',
            'consultation_type' => 'required|string',
            'astrologer_id' => 'required|integer',
            'duration' => 'required|integer',
            'scheduled_at' => 'required|date',
            'slot_id' => 'required',
            'birth_date' => 'nullable|date',
            'birth_time' => 'nullable|string',
            'place' => 'nullable|string',
            'notes' => 'nullable|string',
            'payment_method' => 'required|string',
            'type' => 'required|string',
            'rate' => 'required|numeric',
        ]);
        // Store user_id from session if available
        if (empty($validated['user_id']) && session('api_user_id')) {
            $validated['user_id'] = session('api_user_id');
        }
        $validated['status'] = 'confirmed';
        // Map user_email to email for the API call and remove user_email from payload

        $apiPayload = $validated;
        $apiPayload['email'] = $validated['user_email'];
        $apiPayload['date'] = $validated['scheduled_at'];
        unset($apiPayload['user_email']);
        unset($apiPayload['scheduled_at']);
        // Ensure scheduled_at is a date (Y-m-d)
        if (!empty($apiPayload['date'])) {
            try {
                $apiPayload['date'] = \Carbon\Carbon::parse($apiPayload['date'])->format('Y-m-d');
            } catch (\Exception $e) {
                // fallback: leave as is if parse fails
            }
        }

        // Get token from cookie (set by LoginController)
        $token = $request->cookie('auth_api_token');
        try {
            $result = $this->bookingService->bookConsultation($apiPayload, $token);
            return response()->json(['success' => true, 'data' => $result]);
        } catch (\Exception $e) {
            \Log::error('Booking error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Booking failed.'], 500);
        }
    }

    // AJAX: Get session duration for astrologer
    public function sessionDuration(Request $request)
    {
        $request->validate([
            'astrologer_id' => 'required|integer',
        ]);
        try {
            $result = $this->bookingService->getSessionDuration($request->astrologer_id);
            // Handle nested data key if present
            $duration = null;
            if (is_array($result)) {
                if (isset($result['duration'])) {
                    $duration = $result['duration'];
                } elseif (isset($result['data']['duration'])) {
                    $duration = $result['data']['duration'];
                }
            }
            return response()->json([
                'duration' => $duration
            ]);
        } catch (\Exception $e) {
            \Log::error('Session duration fetch error: ' . $e->getMessage());
            return response()->json(['duration' => null], 500);
        }
    }
}
