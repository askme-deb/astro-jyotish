<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AstrologerEarningsController extends Controller
{
    public function index(Request $request)
    {
        $userId = session('api_user_id');
        $token = session('auth.api_token');
        $apiService = app(\App\Services\Api\Clients\AstrologerApiService::class);
        $response = $apiService->getAstrologerBookings($userId, $token);
        $bookings = collect($response['data'] ?? []);
        return view('astrologer.earnings', compact('bookings'));
    }

    public function export(Request $request)
    {
        $userId = session('api_user_id');
        $token = session('auth.api_token');
        $apiService = app(\App\Services\Api\Clients\AstrologerApiService::class);
        $response = $apiService->getAstrologerBookings($userId, $token);
        $bookings = collect($response['data'] ?? []);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="earnings.csv"',
        ];

        $columns = ['Booking ID', 'Date', 'Customer', 'Type', 'Rate (₹)', 'GST (18%)', 'Commission (30%)', 'Net Earning (₹)'];

        $callback = function () use ($bookings, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($bookings as $booking) {
                $rate = (float)($booking['rate'] ?? 0);
                $gst = $rate * (18/118);
                $commission = $rate * 0.30;
                $net = $rate - $commission;
                fputcsv($file, [
                    'BKNG' . $booking['id'],
                    isset($booking['scheduled_at']) ? \Carbon\Carbon::parse($booking['scheduled_at'])->format('d M Y') : '',
                    $booking['customer']['name'] ?? '-',
                    ucfirst($booking['consultation_type'] ?? '-'),
                    number_format($rate, 2),
                    number_format($gst, 2),
                    number_format($commission, 2),
                    number_format($net, 2),
                ]);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}