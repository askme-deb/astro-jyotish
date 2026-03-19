<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AstrologerBookingService;
use Illuminate\Support\Carbon;

class RajuMaharajBookingController extends Controller
{
    protected $bookingService;
    private $bookingType = 'raju_maharaj';
    private $rajuMaharajId = 15; // TODO: Set actual astrologer ID for Raju Maharaj

    public function __construct(AstrologerBookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    public function showForm(Request $request)
    {
        return view('booking.raju-maharaj');
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'user_name' => 'required|string|max:255',
            'user_phone' => 'required|string|max:20',
            'user_email' => 'required|email',
            'consultation_type' => 'required|string',
            'duration' => 'required|integer',
            'selected_date' => ['required', 'date', function ($attribute, $value, $fail) {
                $days = now()->diffInDays(Carbon::parse($value), false);
                if ($days > 45) {
                    $fail('Booking is only allowed within 45 days from today.');
                }
                if ($days < 0) {
                    $fail('Booking date cannot be in the past.');
                }
            }],
            'slot_id' => 'required',
            'birth_date' => 'nullable|date',
            'birth_time' => 'nullable|string',
            'place' => 'nullable|string',
            'notes' => 'nullable|string',
            'payment_method' => 'required|string',
            'type' => 'required|string',
            'rate' => 'required|numeric',
        ]);

        // Map fields to match API expectations
        $apiPayload = $validated;
        $apiPayload['astrologer_id'] = $this->rajuMaharajId;
        $apiPayload['email'] = $validated['user_email'];
        $apiPayload['date'] = $validated['selected_date'];
        unset($apiPayload['user_email']);
        unset($apiPayload['selected_date']);

        // Ensure date is Y-m-d
        if (!empty($apiPayload['date'])) {
            try {
                $apiPayload['date'] = \Carbon\Carbon::parse($apiPayload['date'])->format('Y-m-d');
            } catch (\Exception $e) {
                // fallback: leave as is if parse fails
            }
        }

        // Get token from cookie (if needed)
        $token = $request->cookie('auth_api_token');
        try {
            $result = $this->bookingService->bookConsultation($apiPayload, $token);
            // If AJAX, return JSON
            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'data' => $result]);
            }
            // Else, redirect as before
            if (isset($result['success']) && $result['success']) {
                return redirect()->route('booking.raju-maharaj.form')->with('success', 'Booking created! Proceed to payment.');
            }
            return back()->withErrors(['general' => $result['message'] ?? 'Booking failed. Please try again.']);
        } catch (\Exception $e) {
            \Log::error('Booking error: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Booking failed.'], 500);
            }
            return back()->withErrors(['general' => 'Booking failed. Please try again.']);
        }
    }

        // AJAX: Get available slots for Raju Maharaj
    public function getSlots(Request $request)
    {
        $date = $request->query('date');
        if (!$date) {
            return response()->json(['success' => true, 'slots' => []]);
        }
        $result = $this->bookingService->getSlots($this->rajuMaharajId, $date);
        \Log::debug('RajuMaharajBookingController@getSlots API response', ['response' => $result]);
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
    }

    private function calculatePrice($selectedDate)
    {
        $days = now()->diffInDays(Carbon::parse($selectedDate), false);
        if ($days < 0 || $days > 45) {
            return null;
        } elseif ($days <= 2) {
            return 21000;
        } elseif ($days <= 15) {
            return 11000;
        } elseif ($days <= 45) {
            return 5000;
        }
        return null;
    }
}
