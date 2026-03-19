<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
// Public endpoint for Raju Maharaj slots
use App\Http\Controllers\RajuMaharajBookingController;

Route::post('/v1/login', [LoginController::class, 'login']);

// Booking and Razorpay routes (require api.user.auth middleware)
Route::middleware('api.user.auth')->group(function () {
	Route::post('/v1/bookings', [App\Http\Controllers\ConsultationController::class, 'store']);
	Route::post('/v1/razorpay/order', [App\Http\Controllers\RazorpayController::class, 'createOrder']);
	Route::post('/v1/razorpay/verify', [App\Http\Controllers\RazorpayController::class, 'verifyPayment']);
	Route::post('/v1/razorpay/booking', [App\Http\Controllers\RazorpayController::class, 'confirmBooking']);


	Route::get('/astrologer/15/slots', [RajuMaharajBookingController::class, 'getSlots']);
});
