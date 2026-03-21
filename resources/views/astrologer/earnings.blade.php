@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">Earnings Breakdown</h2>
    <div class="mb-3">
        <a href="{{ route('astrologer.earnings.export') }}" class="btn btn-success btn-sm"><i class="fa fa-download"></i> Export CSV</a>
        <a href="{{ route('astrologer.dashboard') }}" class="btn btn-secondary btn-sm">Back to Dashboard</a>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>Booking ID</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Type</th>
                    <th>Rate (₹)</th>
                    <th>GST (18%)</th>
                    <th>Commission (30%)</th>
                    <th>Net Earning (₹)</th>
                </tr>
            </thead>
            <tbody>
            @forelse($bookings as $booking)
                @php
                    $rate = (float)($booking['rate'] ?? 0); // GST inclusive
                    $gst = $rate * (18/118); // GST part
                    $base = $rate - $gst; // GST exclusive
                    $commission = $base * 0.30; // 30% of base
                    $net = $base - $commission; // astrologer earning
                @endphp
                <tr>
                    <td>BKNG{{ $booking['id'] }}</td>
                    <td>{{ isset($booking['scheduled_at']) ? \Carbon\Carbon::parse($booking['scheduled_at'])->format('d M Y') : '-' }}</td>
                    <td>{{ $booking['customer']['name'] ?? '-' }}</td>
                    <td>{{ ucfirst($booking['consultation_type'] ?? '-') }}</td>
                    <td>{{ number_format($rate, 2) }}</td>
                    <td>{{ number_format($gst, 2) }}</td>
                    <td>{{ number_format($commission, 2) }}</td>
                    <td>{{ number_format($net, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted">No bookings found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
