<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AstrologerBookingService;

class BookingDetailsController extends Controller
{
    // public function show($id, Request $request, AstrologerBookingService $bookingService)
    // {
    //     $token = $request->cookie('auth_api_token');
    //     $response = $bookingService->getBookings($token);
    //     $booking = collect($response['data'] ?? [])->firstWhere('id', (int)$id);
    //     return view('booking-details', compact('booking'));
    // }
}
