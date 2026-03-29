<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
// Public endpoint for Raju Maharaj slots
use App\Http\Controllers\RajuMaharajBookingController;

use App\Http\Controllers\AstrologerDataController;
use App\Http\Controllers\LocationController;

Route::post('/v1/login', [LoginController::class, 'login']);

// Astrologer data endpoints
Route::get('/v1/astrologer-languages', [AstrologerDataController::class, 'getLanguages']);
Route::get('/v1/astrologer-skills', [AstrologerDataController::class, 'getSkills']);
// Astrologer registration endpoint
Route::post('/v1/astrologers', [App\Http\Controllers\AstrologerRegistrationController::class, 'store']);

// Booking and Razorpay routes (require api.user.auth middleware)
Route::middleware('api.user.auth')->group(function () {
	Route::post('/v1/bookings', [App\Http\Controllers\ConsultationController::class, 'store']);
	Route::post('/v1/razorpay/order', [App\Http\Controllers\RazorpayController::class, 'createOrder']);
	Route::post('/v1/razorpay/verify', [App\Http\Controllers\RazorpayController::class, 'verifyPayment']);
	Route::post('/v1/razorpay/booking', [App\Http\Controllers\RazorpayController::class, 'confirmBooking']);


	Route::get('/astrologer/15/slots', [RajuMaharajBookingController::class, 'getSlots']);
});

// State and City list endpoints
Route::post('/v1/get-state-list', [LocationController::class, 'getStates']);
Route::post('/v1/get-city-list', [LocationController::class, 'getCities']);
