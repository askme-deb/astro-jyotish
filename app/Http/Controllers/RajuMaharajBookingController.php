<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AstrologerBookingService;
use Illuminate\Support\Carbon;

class RajuMaharajBookingController extends Controller
{
    protected $bookingService;
    private $bookingType = 'raju_maharaj';
    private $rajuMaharajId = 9999; // TODO: Set actual astrologer ID for Raju Maharaj

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
            'selected_date' => ['required', 'date', function ($attribute, $value, $fail) {
                $days = now()->diffInDays(Carbon::parse($value), false);
                if ($days > 45) {
                    $fail('Booking is only allowed within 45 days from today.');
                }
                if ($days < 0) {
                    $fail('Booking date cannot be in the past.');
                }
            }],
            'slot_id' => ['required', 'string'],
            'user_name' => ['required', 'string', 'max:255'],
            'user_phone' => ['required', 'string', 'max:20'],
            'user_email' => ['nullable', 'email'],
        ]);

        $price = $this->calculatePrice($validated['selected_date']);
        if ($price === null) {
            return back()->withErrors(['selected_date' => 'Booking is only allowed within 45 days from today.']);
        }

        $data = [
            'astrologer_id' => $this->rajuMaharajId,
            'date' => $validated['selected_date'],
            'slot_id' => $validated['slot_id'],
            'user_name' => $validated['user_name'],
            'user_phone' => $validated['user_phone'],
            'user_email' => $validated['user_email'],
            'price' => $price,
            'booking_type' => $this->bookingType,
        ];

        $result = $this->bookingService->bookConsultation($data);
        if (isset($result['success']) && $result['success']) {
            // Redirect to payment or confirmation page as per existing flow
            return redirect()->route('booking.raju-maharaj.form')->with('success', 'Booking created! Proceed to payment.');
        }
        return back()->withErrors(['general' => $result['message'] ?? 'Booking failed. Please try again.']);
    }

        // AJAX: Get available slots for Raju Maharaj
        public function getSlots(Request $request)
        {
            $date = $request->query('date');
            if (!$date) {
                return response()->json(['slots' => []]);
            }
            $slots = $this->bookingService->getSlots($this->rajuMaharajId, $date);
            // Normalize slots for frontend
            $normalized = collect($slots['data'] ?? $slots ?? [])->map(function ($slot) {
                return [
                    'id' => $slot['id'] ?? $slot['slot_id'] ?? null,
                    'label' => $slot['label'] ?? $slot['time'] ?? null,
                ];
            })->filter(fn($s) => $s['id'] && $s['label'])->values();
            return response()->json(['slots' => $normalized]);
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
