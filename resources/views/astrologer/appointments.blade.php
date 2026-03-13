
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
                        <div class="dashboard-title">Appointments</div>
                        <div class="dashboard-subtitle">View and manage your astrologer appointments.</div>
                    </div>
                </div>
                <div class="sidebar-card dashboard-card mt-4">
                    <div class="dashboard-section-head">
                        <h3>Appointments List</h3>
                    </div>
                    <div class="row g-3">
                        @forelse ($appointments as $appointment)
                            <div class="col-md-6 col-lg-6">
                                <div class="card shadow-sm h-100" style="border-radius: 16px;">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <div style="font-size:1.15rem;font-weight:600;">{{ ucfirst($appointment['consultation_type'] ?? 'Consultation') }} Appointment</div>
                                                <div style="font-size:0.95rem;color:#666;">Booking ID: BKNG{{ $appointment['id'] }}</div>
                                            </div>
                                            <span class="badge bg-{{ $appointment['status'] === 'in_progress' ? 'success' : (($appointment['status'] ?? null) === 'ready_to_start' ? 'info' : ($appointment['status'] === 'confirmed' ? 'primary' : ($appointment['status'] === 'pending' ? 'warning' : 'secondary'))) }} {{ ($appointment['status'] ?? null) === 'ready_to_start' ? 'text-dark' : '' }}" style="font-size:0.95em;min-width:80px;text-align:center;">{{ str_replace('_', ' ', ucfirst($appointment['status'])) }}</span>
                                        </div>
                                        <hr class="my-2">
                                        <style>
                                            .text-theme-orange { color: #f98800 !important; }
                                        </style>
                                        <div class="d-flex flex-wrap align-items-center mb-2" style="gap: 1rem;">
                                            <div style="min-width:120px;">
                                                <i class="fa-solid fa-calendar-day text-theme-orange me-1"></i>
                                                {{ \Carbon\Carbon::parse($appointment['scheduled_at'])->format('d F Y') }}
                                            </div>
                                            <div style="min-width:90px;">
                                                <i class="fa-solid fa-location-dot text-theme-orange me-1"></i>
                                                {{ $appointment['location'] ?? 'Online' }}
                                            </div>
                                        </div>
                                        <div class="d-flex flex-wrap align-items-center mb-2" style="gap: 1rem;">
                                            <div style="min-width:120px;">
                                                <i class="fa-solid fa-clock text-theme-orange me-1"></i>
                                                {{ \Carbon\Carbon::parse($appointment['scheduled_at'])->format('h:i A') }}
                                                @if(isset($appointment['end_time']))
                                                    - {{ \Carbon\Carbon::parse($appointment['end_time'])->format('h:i A') }}
                                                @endif
                                            </div>
                                            <div style="font-weight:600;color:#219150;font-size:1.1rem;">
                                                ₹{{ $appointment['rate'] ?? '-' }}
                                            </div>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <a href="{{ route('astrologer.appointment.details', ['id' => $appointment['id']]) }}" class="btn btn-outline-theme btn-sm">
                                                <i class="fa-regular fa-eye"></i> View Details
                                            </a>
                                            <button class="btn btn-outline-theme-danger btn-sm" disabled>
                                                <i class="bi bi-trash"></i>Cancel
                                            </button>
                                            <button class="btn btn-outline-theme-success btn-sm" disabled>
                                                <i class="bi bi-receipt"></i> Invoice
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center text-muted py-4">No appointments found.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
