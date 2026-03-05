<?php
use App\Http\Controllers\OtpAuthController;

use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConsultantController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\LoginController;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RegisterController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/consultants', [ConsultantController::class, 'show'])->name('consultant');
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::get('/consultant/{identifier}', [ConsultantController::class, 'profile'])->name('consultant.profile');
Route::get('/services/{slug}', [ServicesController::class, 'service'])->name('services.dynamic');
Route::get('/consultation', [ConsultationController::class, 'index'])->name('consultation');
Route::get('/consultation/slots', [ConsultationController::class, 'getSlots'])->name('consultation.slots');
Route::post('/consultation/book', [ConsultationController::class, 'store'])->name('consultation.book');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
// OTP Login (modal)
Route::middleware(['guest'])->group(function () {
    Route::post('/login/otp/request', [OtpAuthController::class, 'requestOtp'])
        ->middleware('throttle:otp')
        ->name('login.otp.request');

    Route::post('/login/otp/resend', [OtpAuthController::class, 'resendOtp'])
        ->middleware('throttle:otp')
        ->name('login.otp.resend');

    Route::post('/login/otp/verify', [OtpAuthController::class, 'verifyOtp'])
        ->middleware('throttle:otp')
        ->name('login.otp.verify');

    // Register route
    Route::post('/v1/register', [RegisterController::class, 'register']);

    // Password login (AJAX, modal)
    Route::post('/v1/login', [LoginController::class, 'login']);    
});


Route::get('/consultation/session-duration', [ConsultationController::class, 'sessionDuration'])->name('consultation.sessionDuration');


Route::post('/logout', [OtpAuthController::class, 'logout'])
    ->name('logout');