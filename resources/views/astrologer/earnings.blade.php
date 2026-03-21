@extends('layouts.app')

@section('content')
<div class="container mt-4 inner_back">
    <div class="banner">
        <img src="{{ asset('assets/images/consult.png') }}" alt="Astrology Banner">
    </div>
</div>
<section class="section-padding dashboard-layout">
    <div class="container">
        <div class="row g-4">
            <!-- Sidebar -->
            <div class="col-lg-3">
                <aside class="astro-sidebar">
                    @include('astrologer.partials.sidebar')
                </aside>
            </div>
            <!-- Main content -->
            <div class="col-lg-9">
                <div class="sidebar-card dashboard-card dashboard-header" data-aos="fade-up" data-aos-delay="80">
                    <div class="dashboard-header-left">
                        <div class="dashboard-title">Earnings Breakdown</div>
                        <div class="dashboard-subtitle">Detailed per-booking earnings, GST, and commission.</div>
                    </div>
                    <div class="dashboard-header-right">
                        <a href="{{ route('astrologer.earnings.export') }}" class="btn btn-success btn-sm"><i class="fa fa-download"></i> Export CSV</a>
                        <a href="{{ route('astrologer.dashboard') }}" class="btn btn-secondary btn-sm">Back to Dashboard</a>
                    </div>
                </div>
                <div class="sidebar-card dashboard-card mt-4">
                    <div class="dashboard-section-head">
                        <h3>Earnings Table</h3>
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
                                    <td>
                                        <a href="{{ route('astrologer.appointment.details', ['id' => $booking['id']]) }}" target="_blank">BKNG{{ $booking['id'] }}</a>
                                    </td>
                                    <td>{{ isset($booking['scheduled_at']) ? \Carbon\Carbon::parse($booking['scheduled_at'])->format('d M Y') : '-' }}</td>
                                    <td>{{ $booking['name'] ?? '-' }}</td>
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
            </div>
        </div>
    </div>
</section>
@endsection
