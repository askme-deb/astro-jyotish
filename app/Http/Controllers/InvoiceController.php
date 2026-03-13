<?php

namespace App\Http\Controllers;

use App\Services\AstrologerBookingService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function download(int $id, Request $request, AstrologerBookingService $bookingService)
    {
        $token = $request->cookie('auth_api_token') ?? session('auth.api_token') ?? session('auth_api_token');

        if (! $token) {
            abort(401, 'Unauthenticated.');
        }

        $response = $bookingService->getBookingById($id, $token);
        $booking = $response['data'] ?? null;

        if (! is_array($booking) || empty($booking)) {
            abort(404, 'Booking not found.');
        }

        $generatedDate = now();
        $invoiceNumber = sprintf(
            '%s-%s-%06d',
            config('invoice.invoice_prefix', 'AJ'),
            $generatedDate->format('Ymd'),
            $id
        );

        $pdf = Pdf::loadView('invoice', [
            'booking' => $booking,
            'generatedDate' => $generatedDate,
            'invoiceNumber' => $invoiceNumber,
            'business' => config('invoice.business', []),
        ])->setPaper('a4');

        return $pdf->download(strtolower($invoiceNumber) . '.pdf');
    }
}
