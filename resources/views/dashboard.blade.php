@extends('layouts.app')

@section('content')

<div class="container mt-4 inner_back">
    <div class="banner">
        <!-- Background Image -->
        <img src="{{ asset('assets/images/consult.png') }}" alt="Astrology Banner">
    </div>
</div>

<section class="section-padding dashboard-layout">
    <div class="container">
        <div class="row g-4">

            <!-- Sidebar -->
            <div class="col-lg-3">

                <aside class="astro-sidebar">

                    <div class="astro-sidebar-inner">

                        <!-- Profile Card -->
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
                            <!-- <div class="astro-role">Vedic Astrology • Hindi/English</div> -->

                            <!-- <div class="astro-badge">
                                ⭐ Verified Astrologer
                            </div> -->

                        </div>


                        <!-- Menu -->

                        <div class="astro-menu-card">
                            <h6 class="menu-title">MAIN MENU</h6>
                            <nav class="astro-menu">
                                <a class="" href="/dashboard">
                                    <i class="fas fa-gauge"></i> Dashboard
                                </a>
                                <a class="" href="/my-bookings">
                                    <i class="fas fa-calendar-check"></i> My Bookings
                                </a>
                                <a class="" href="/profile">
                                    <i class="fas fa-user"></i> My Profile
                                </a>
                                <!-- <a class="" href="/messages">
                                    <i class="fas fa-comments"></i> Messages
                                </a> -->
                            </nav>
                        </div>


                        <!-- Quick Actions -->

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

            <!-- Main content -->
            <div class="col-lg-9">

                <!-- Header strip -->
                <div class="sidebar-card dashboard-card dashboard-header" data-aos="fade-up" data-aos-delay="80">
                    <div class="dashboard-header-left">
                        <div class="dashboard-title">Welcome back, {{ session('auth.user.first_name') }}</div>
                        <div class="dashboard-subtitle">View your upcoming consultations and manage your profile.</div>
                    </div>
                    <div class="dashboard-header-right">
                        <span class="dashboard-status"><span class="dot"></span> Online</span>
                        <a class="btn btn-primary btn-sm" href="/my-bookings" style="margin-top: 0px;"><i class="fas fa-calendar-check me-1"></i> My Bookings</a>
                        <a class="btn btn-outline-secondary btn-sm" href="/profile"><i class="fas fa-user me-1"></i> My Profile</a>
                    </div>
                </div>

                <!-- Stats -->
                <style>
                    .dashboard-kpi-modern {
                        border-radius: 14px;
                        background: #fff;
                        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
                        padding: 1.1rem 1rem 1rem 1rem;
                        display: flex;
                        flex-direction: column;
                        align-items: flex-start;
                        min-height: 110px;
                        position: relative;
                        margin-bottom: 0.5rem;
                        border: 1.2px solid #f3f3f3;
                    }
                    .dashboard-kpi-modern .kpi-accent {
                        position: absolute;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 5px;
                        background: linear-gradient(90deg, #f98700 60%, #fbbf24 100%);
                        border-radius: 14px 14px 0 0;
                    }
                    .dashboard-kpi-modern .kpi-icon {
                        width: 34px;
                        height: 34px;
                        border-radius: 50%;
                        background: #f98700;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: #fff;
                        font-size: 1.1rem;
                        margin-bottom: 0.7rem;
                        box-shadow: 0 1px 4px rgba(249,135,0,0.08);
                    }
                    .dashboard-kpi-modern .kpi-label {
                        font-size: 0.98rem;
                        color: #888;
                        font-weight: 600;
                        margin-bottom: 0.3rem;
                    }
                    .dashboard-kpi-modern .kpi-value {
                        font-size: 1.45rem;
                        font-weight: 800;
                        color: #222;
                        margin-bottom: 0.1rem;
                        letter-spacing: -0.5px;
                    }
                    .dashboard-kpi-modern .kpi-note {
                        font-size: 0.93rem;
                        color: #f98700;
                        font-weight: 500;
                        margin-bottom: 0.1rem;
                    }
                </style>
                <div class="row g-4" data-aos="fade-up" data-aos-delay="110">
                    <div class="col-md-6 col-xl-4">
                        <div class="dashboard-kpi-modern">
                            <div class="kpi-accent"></div>
                            <div class="kpi-icon"><i class="fas fa-calendar-day"></i></div>
                            <div class="kpi-label">Upcoming Consultations</div>
                            <div class="kpi-value">{{ $upcomingBookings->count() }}</div>
                            <div class="kpi-note">{{ $upcomingBookings->count() ? 'Next 7 days' : 'No upcoming bookings' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div class="dashboard-kpi-modern">
                            <div class="kpi-accent"></div>
                            <div class="kpi-icon"><i class="fas fa-users"></i></div>
                            <div class="kpi-label">Total Consultations</div>
                            <div class="kpi-value">{{ $allBookings->count() }}</div>
                            <div class="kpi-note">All time</div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-4">
                        <div class="dashboard-kpi-modern">
                            <div class="kpi-accent"></div>
                            <div class="kpi-icon"><i class="fas fa-wallet"></i></div>
                            <div class="kpi-label">Wallet Balance</div>
                            <div class="kpi-value">₹{{ number_format($walletBalance, 2) }}</div>
                            <div class="kpi-note">Available for use</div>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mt-1 dashboard-section" data-aos="fade-up" data-aos-delay="150">
                    <div class="col-lg-12">
                        <div class="sidebar-card dashboard-card">
                            <div class="dashboard-section-head">
                                <h3>Upcoming Consultations</h3>
                                <div class="dashboard-section-actions">
                                    <a class="btn btn-outline-secondary btn-sm" href="/my-bookings">View all</a>
                                </div>
                            </div>
                            <style>
                                .dashboard-booking-card {
                                    border-radius: 16px;
                                    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
                                    background: #fff;
                                    margin-bottom: 1.2rem;
                                    padding: 1.2rem 1.2rem 1rem 1.2rem;
                                    display: flex;
                                    flex-direction: column;
                                    gap: 0.5rem;
                                }
                                .dashboard-booking-header {
                                    display: flex;
                                    justify-content: space-between;
                                    align-items: flex-start;
                                }
                                .dashboard-booking-title {
                                    font-size: 1.1rem;
                                    font-weight: 600;
                                }
                                .dashboard-booking-status {
                                    font-size: 0.95em;
                                    min-width: 80px;
                                    text-align: center;
                                    border-radius: 8px;
                                    padding: 0.3em 0.9em;
                                }
                                .dashboard-booking-status.confirmed {
                                    background: #d4edda;
                                    color: #155724;
                                    border: 1px solid #c3e6cb;
                                }
                                .dashboard-booking-status.pending {
                                    background: #fff3cd;
                                    color: #856404;
                                    border: 1px solid #ffeeba;
                                }
                                .dashboard-booking-row {
                                    display: flex;
                                    flex-wrap: wrap;
                                    gap: 1.5rem;
                                    margin-bottom: 0.5rem;
                                }
                                .dashboard-booking-label {
                                    color: #888;
                                    font-size: 0.98rem;
                                    min-width: 90px;
                                }
                                .dashboard-booking-value {
                                    font-weight: 500;
                                    color: #222;
                                }
                                .dashboard-booking-actions {
                                    display: flex;
                                    gap: 0.5rem;
                                    margin-top: 0.5rem;
                                }
                            </style>
                            <div>
                                @if($upcomingBookings->count())
                                    @foreach($upcomingBookings as $booking)
                                        <div class="dashboard-booking-card">
                                            <div class="dashboard-booking-header">
                                                <div>
                                                    <div class="dashboard-booking-title">{{ ucfirst($booking['consultation_type']) }} with {{ $booking['astrologer']['name'] ?? '-' }}</div>
                                                    <div style="font-size:0.97rem;color:#666;">Booking ID: BKNG{{ $booking['id'] }}</div>
                                                </div>
                                                <span class="dashboard-booking-status {{ $booking['status'] }}">{{ str_replace('_', ' ', ucfirst($booking['status'])) }}</span>
                                            </div>
                                            <div class="dashboard-booking-row">
                                                <div class="dashboard-booking-label"><i class="fa-solid fa-calendar-day text-theme-orange me-1"></i> Date</div>
                                                <div class="dashboard-booking-value">{{ \Carbon\Carbon::parse($booking['scheduled_at'])->format('d F Y') }}</div>
                                                <div class="dashboard-booking-label"><i class="fa-solid fa-clock text-theme-orange me-1"></i> Time</div>
                                                <div class="dashboard-booking-value">{{ \Carbon\Carbon::parse($booking['scheduled_at'])->format('h:i A') }}@if(isset($booking['end_time'])) - {{ \Carbon\Carbon::parse($booking['end_time'])->format('h:i A') }}@endif</div>
                                                <div class="dashboard-booking-label"><i class="fa-solid fa-hourglass-half text-theme-orange me-1"></i> Duration</div>
                                                <div class="dashboard-booking-value">{{ $booking['duration'] ?? '-' }} min</div>
                                                <div class="dashboard-booking-label"><i class="fa-solid fa-rupee-sign text-theme-orange me-1"></i> Price</div>
                                                <div class="dashboard-booking-value">₹{{ $booking['rate'] }}</div>
                                            </div>
                                            <div class="dashboard-booking-actions">
                                                <a href="{{ route('booking.details', ['id' => $booking['id']]) }}" class="btn btn-outline-theme btn-sm">
                                                    <i class="fa-regular fa-eye"></i> View Details
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="dashboard-booking-card text-center text-muted py-4">No upcoming bookings.</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- <div class="col-lg-4">
                        <div class="sidebar-card dashboard-card">
                            <div class="dashboard-section-head">
                                <h3>Performance</h3>
                                <div class="dashboard-section-actions">
                                    <a class="btn btn-outline-secondary btn-sm" href="astrologer-earnings.php">Earnings</a>
                                </div>
                            </div>

                            <div class="dashboard-metric2">
                                <div class="label">Response Rate</div>
                                <div class="value">92%</div>
                                <div class="bar"><span style="width: 92%"></span></div>
                            </div>
                            <div class="dashboard-metric2">
                                <div class="label">On-time Sessions</div>
                                <div class="value">88%</div>
                                <div class="bar"><span style="width: 88%"></span></div>
                            </div>
                            <div class="dashboard-metric2">
                                <div class="label">Profile Completion</div>
                                <div class="value">75%</div>
                                <div class="bar"><span style="width: 75%"></span></div>
                            </div>
                        </div>

                        <div class="sidebar-card dashboard-card mt-4">
                            <div class="dashboard-section-head">
                                <h3>Inbox</h3>
                                <div class="dashboard-section-actions">
                                    <a class="btn btn-outline-secondary btn-sm" href="astrologer-messages.php">Open</a>
                                </div>
                            </div>

                            <div class="dashboard-list">
                                <a class="dashboard-list-item" href="astrologer-messages.php">
                                    <div class="left">
                                        <div class="avatar"><i class="fas fa-user"></i></div>
                                        <div>
                                            <div class="title">Priya Singh</div>
                                            <div class="sub">Can we reschedule to 6 PM?</div>
                                        </div>
                                    </div>
                                    <div class="right">2h</div>
                                </a>
                                <a class="dashboard-list-item" href="astrologer-messages.php">
                                    <div class="left">
                                        <div class="avatar"><i class="fas fa-user"></i></div>
                                        <div>
                                            <div class="title">Rahul Sharma</div>
                                            <div class="sub">Thank you for the guidance.</div>
                                        </div>
                                    </div>
                                    <div class="right">1d</div>
                                </a>
                            </div>
                        </div> -->
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

@endsection
