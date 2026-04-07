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
            <div class="col-lg-3">
                <aside class="astro-sidebar">
                    <div class="astro-sidebar-inner">
                        <div class="astro-profile-card">
                            <style>
                                .astro-avatar-placeholder {
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    width: 64px;
                                    height: 64px;
                                    border-radius: 50%;
                                    background: #e0e0e0;
                                    position: relative;
                                }
                                .avatar-initials {
                                    font-size: 2rem;
                                    font-weight: bold;
                                    color: #555;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    width: 100%;
                                    height: 100%;
                                    border-radius: 50%;
                                    user-select: none;
                                }
                                .status-dot {
                                    position: absolute;
                                    bottom: 8px;
                                    right: 8px;
                                    width: 14px;
                                    height: 14px;
                                    background: #4caf50;
                                    border: 2px solid #fff;
                                    border-radius: 50%;
                                }
                                .text-theme-orange { color: #f98800 !important; }
                                :root {
                                    --theme-orange: #f98700;
                                }
                                .btn-outline-theme {
                                    border: 1.5px solid var(--theme-orange);
                                    color: var(--theme-orange);
                                }
                                .btn-outline-theme:hover, .btn-outline-theme:focus {
                                    background: var(--theme-orange);
                                    color: #fff;
                                }
                            </style>
                            <div class="astro-avatar astro-avatar-placeholder">
                                @php
                                    $first = session('auth.user.first_name');
                                    $last = session('auth.user.last_name');
                                    $initials = '';
                                    if ($first) $initials .= strtoupper(mb_substr($first, 0, 1));
                                    if ($last) $initials .= strtoupper(mb_substr($last, 0, 1));
                                @endphp
                                <span class="avatar-initials">{{ $initials }}</span>
                                <span class="status-dot"></span>
                            </div>
                            <h4 class="astro-name">{{ session('auth.user.first_name') }} {{ session('auth.user.last_name') }}</h4>
                        </div>
                        <div class="astro-menu-card">
                            <h6 class="menu-title">MAIN MENU</h6>
                            <nav class="astro-menu">
                                <a class="" href="/dashboard">
                                    <i class="fas fa-gauge"></i> Dashboard
                                </a>
                                <a href="/my-bookings">
                                    <i class="fas fa-calendar-check"></i> My Bookings
                                </a>
                                <a href="{{ route('my-bookings.completed') }}">
                                    <i class="fas fa-circle-check"></i> Completed
                                </a>
                                <a href="{{ route('my-bookings.cancelled') }}">
                                    <i class="fas fa-ban"></i> Cancelled
                                </a>
                                <a class="" href="/profile">
                                    <i class="fas fa-user"></i> My Profile
                                </a>
                                <a class="" href="{{ route('customer.supportTickets.index') }}">
                                    <i class="fas fa-life-ring"></i> Support Tickets
                                </a>
                            </nav>
                        </div>
                        <div class="astro-action-card">
                            <h6 class="menu-title">QUICK ACTIONS</h6>
                            <a class="btn btn-primary w-100 mb-2" href="/book-consultation">
                                + Book Consultation
                            </a>
                            <a class="btn btn-outline-secondary w-100" href="/">
                                Back to Home
                            </a>
                        </div>
                    </div>
                </aside>
            </div>
            <div class="col-lg-9">
                <div class="sidebar-card dashboard-card dashboard-header" data-aos="fade-up" data-aos-delay="80">
                    <div class="dashboard-header-left">
                        <div class="dashboard-title">{{ $pageTitle }}</div>
                        <div class="dashboard-subtitle">{{ $pageSubtitle }}</div>
                    </div>
                </div>
                <div class="sidebar-card dashboard-card mt-4">
                    <div class="dashboard-section-head">
                        <h3>{{ $pageTitle }}</h3>
                        <div class="dashboard-section-actions d-flex flex-wrap gap-2">
                            <a href="{{ route('my-bookings') }}" class="btn btn-outline-secondary btn-sm">Active</a>
                            <a href="{{ route('my-bookings.completed') }}" class="btn btn-outline-secondary btn-sm">Completed</a>
                            <a href="{{ route('my-bookings.cancelled') }}" class="btn btn-outline-secondary btn-sm">Cancelled</a>
                        </div>
                    </div>
                    <div class="row g-3">
                        @forelse ($bookings as $booking)
                            <div class="col-md-6 col-lg-6">
                                <div class="card shadow-sm h-100" style="border-radius: 16px;">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <div style="font-size:1.15rem;font-weight:600;">{{ ucfirst($booking['consultation_type'] ?? 'Consultation') }} Consultation</div>
                                                <div style="font-size:0.95rem;color:#666;">Booking ID: BKNG{{ $booking['id'] }}</div>
                                            </div>
                                            <span class="badge {{ ($booking['status'] ?? null) === 'completed' ? 'bg-success' : (($booking['status'] ?? null) === 'cancelled' ? 'bg-danger' : 'bg-secondary') }}" style="font-size:0.95em;min-width:80px;text-align:center;">{{ str_replace('_', ' ', ucfirst($booking['status'] ?? 'status')) }}</span>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex flex-wrap align-items-center mb-2" style="gap: 1rem;">
                                            <div style="min-width:120px;">
                                                <i class="fa-solid fa-calendar-day text-theme-orange me-1"></i>
                                                {{ !empty($booking['scheduled_at']) ? \Carbon\Carbon::parse($booking['scheduled_at'])->format('d F Y') : '-' }}
                                            </div>
                                            <div style="min-width:90px;">
                                                <i class="fa-solid fa-location-dot text-theme-orange me-1"></i>
                                                {{ $booking['location'] ?? 'Online' }}
                                            </div>
                                        </div>
                                        <div class="d-flex flex-wrap align-items-center mb-2" style="gap: 1rem;">
                                            <div style="min-width:120px;">
                                                <i class="fa-solid fa-clock text-theme-orange me-1"></i>
                                                {{ !empty($booking['scheduled_at']) ? \Carbon\Carbon::parse($booking['scheduled_at'])->format('h:i A') : '-' }}
                                                @if(isset($booking['end_time']))
                                                    - {{ \Carbon\Carbon::parse($booking['end_time'])->format('h:i A') }}
                                                @endif
                                            </div>
                                            <div style="font-weight:600;color:#219150;font-size:1.1rem;">
                                                ₹{{ $booking['rate'] ?? '-' }}
                                            </div>
                                        </div>
                                        <hr class="my-2">
                                        <div class="d-flex justify-content-start align-items-center mt-2">
                                            <a href="{{ route('booking.details', ['id' => $booking['id']]) }}" class="btn btn-outline-theme btn-sm">
                                                <i class="fa-regular fa-eye"></i> View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center text-muted py-4">{{ $emptyMessage }}</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
