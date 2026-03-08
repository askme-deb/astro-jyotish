<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AstrologerBookingService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the dashboard page.
     */
    public function index(Request $request, AstrologerBookingService $bookingService)
    {

        $roles = session('auth.roles', []);
        if (in_array('Astrologer', $roles)) {
            $user = session('auth.user');
            $astrologerId = $user['id'] ?? null;
            $token = $request->cookie('auth_api_token');
            $bookings = collect();
            if ($astrologerId && $token) {
                $response = $bookingService->getAstrologerBookings($astrologerId, $token);
                $bookings = collect($response['data'] ?? []);
            }
            return view('astrologer.astrologer-dashboard', compact('bookings'));
        }

        $token = $request->cookie('auth_api_token');
        $response = $bookingService->getBookings($token);
        $allBookings = collect($response['data'] ?? []);
        $upcomingBookings = $allBookings->filter(function ($booking) {
            if (!isset($booking['scheduled_at'])) return false;
            $now = Carbon::now();
            $scheduled = Carbon::parse($booking['scheduled_at']);
            return $scheduled->isAfter($now) && $scheduled->lte($now->copy()->addDays(7));
        })->sortBy('scheduled_at')->values();

        // Wallet balance placeholder (replace with real value if available)
        $walletBalance = 0.00;

        return view('dashboard', compact('upcomingBookings', 'allBookings', 'walletBalance'));
    }
}
