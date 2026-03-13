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
use App\Http\Controllers\MyBookingsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookingDetailsController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\CustomerConsultationController;

use App\Http\Controllers\AstrologerAppointmentsController;

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

Route::get('/my-bookings', [MyBookingsController::class, 'index'])->name('my-bookings');
Route::get('/customer/live-consultation-status', [MyBookingsController::class, 'activeConsultationStatus'])->name('customer.liveConsultationStatus');
Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

Route::get('/booking/{id}', [MyBookingsController::class, 'show'])->name('booking.details');
Route::get('/booking/{id}/invoice', [InvoiceController::class, 'download'])->middleware('api.user.auth')->name('booking.invoice.download');
Route::post('/booking/{id}/reschedule', [MyBookingsController::class, 'reschedule'])->middleware('api.user.auth')->name('booking.reschedule');
Route::match(['GET', 'POST'], '/booking/{id}/join-consultation', [MyBookingsController::class, 'joinConsultation'])->name('booking.consultation.join');

    Route::get('/appointments', [AstrologerAppointmentsController::class, 'index'])->name('astrologer.appointments');
    Route::get('/astrologer/appointments/{id}', [AstrologerAppointmentsController::class, 'show'])->name('astrologer.appointment.details');

// Astrologer Appointment Actions
use App\Http\Controllers\AstrologerAppointmentDetailsController;
// Route::middleware(['auth'])->group(function () {
    Route::post('/astrologer/appointments/{id}/start', [AstrologerAppointmentDetailsController::class, 'start'])->name('astrologer.appointment.start');
    Route::get('/astrologer/appointments/{id}/video', [AstrologerAppointmentDetailsController::class, 'video'])->name('astrologer.appointment.video');
    Route::post('/astrologer/appointments/{id}/start-video', [AstrologerAppointmentDetailsController::class, 'startVideo'])->name('astrologer.appointment.startVideo');
    Route::post('/astrologer/appointments/{id}/suggest-product', [AstrologerAppointmentDetailsController::class, 'suggestProduct'])->name('astrologer.appointment.suggestProduct');
    Route::post('/astrologer/appointments/{id}/suggested-products', [AstrologerAppointmentDetailsController::class, 'addSuggestedProduct'])->name('astrologer.appointment.addSuggestedProduct');
    Route::post('/astrologer/appointments/{id}/save-notes', [AstrologerAppointmentDetailsController::class, 'saveNotes'])->name('astrologer.appointment.saveNotes');
    Route::post('/astrologer/appointments/{id}/cancel', [AstrologerAppointmentDetailsController::class, 'cancel'])->name('astrologer.appointment.cancel');
    Route::post('/astrologer/appointments/{id}/reschedule', [AstrologerAppointmentDetailsController::class, 'reschedule'])->name('astrologer.appointment.reschedule');
    Route::post('/astrologer/appointments/{id}/send-link', [\App\Http\Controllers\AstrologerAppointmentDetailsController::class, 'sendCustomerJoinLink'])->name('astrologer.appointment.sendLink');
// });




// AJAX endpoint for customer to poll session status
Route::get('/astrologer/appointments/{id}/ajax-status', [\App\Http\Controllers\AstrologerAppointmentDetailsController::class, 'ajaxStatus'])->name('astrologer.appointment.ajaxStatus');
// AJAX endpoints for starting/ending video consultation session
Route::post('/astrologer/appointments/{id}/ajax-start-video-session', [\App\Http\Controllers\AstrologerAppointmentDetailsController::class, 'ajaxStartVideoSession'])->name('astrologer.appointment.ajaxStartVideoSession');
Route::post('/astrologer/appointments/{id}/ajax-end-video-session', [\App\Http\Controllers\AstrologerAppointmentDetailsController::class, 'ajaxEndVideoSession'])->name('astrologer.appointment.ajaxEndVideoSession');
Route::get('/astrologer/appointments/{id}/leave-video', [\App\Http\Controllers\AstrologerAppointmentDetailsController::class, 'leaveVideo'])->name('astrologer.appointment.leaveVideo');

Route::post('/logout', [OtpAuthController::class, 'logout'])
    ->name('logout');

Route::get('/customer/consultation/video/{meetingId}', [CustomerConsultationController::class, 'video'])->name('customer.consultation.video');

