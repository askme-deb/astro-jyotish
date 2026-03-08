@extends('layouts.app')

@section('content')
@php
    $myBookingsPageData = [
        'bookingIds' => collect($bookings ?? [])->pluck('id')->values()->all(),
    ];
@endphp
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
                        </div>
                        <!-- Menu -->
                        <div class="astro-menu-card">
                            <h6 class="menu-title">MAIN MENU</h6>
                            <nav class="astro-menu">
                                <a class="" href="/dashboard">
                                    <i class="fas fa-gauge"></i> Dashboard
                                </a>
                                <!-- class="active" -->
                                <a  href="/my-bookings">
                                    <i class="fas fa-calendar-check"></i> My Bookings
                                </a>
                                <a class="" href="/profile">
                                    <i class="fas fa-user"></i> My Profile
                                </a>
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
                <div class="sidebar-card dashboard-card dashboard-header" data-aos="fade-up" data-aos-delay="80">
                    <div class="dashboard-header-left">
                        <div class="dashboard-title">My Bookings</div>
                        <div class="dashboard-subtitle">View and manage your consultation bookings.</div>
                    </div>
                </div>
                <div class="sidebar-card dashboard-card mt-4">
                    <div class="dashboard-section-head">
                        <h3>Bookings List</h3>
                    </div>
                    <style>
                        .bookings-table th {
                            background: #f8f9fa;
                            color: #333;
                            font-weight: 600;
                            border-bottom: 2px solid #dee2e6;
                        }
                        .bookings-table tbody tr {
                            transition: background 0.2s;
                        }
                        .bookings-table tbody tr:hover {
                            background: #f1f7ff;
                        }
                        .bookings-table td, .bookings-table th {
                            vertical-align: middle;
                        }
                        .badge-status {
                            font-size: 0.95em;
                            padding: 0.4em 0.8em;
                            border-radius: 12px;
                            font-weight: 500;
                        }
                        .badge-pending {
                            background: #fff3cd;
                            color: #856404;
                            border: 1px solid #ffeeba;
                        }
                        .badge-confirmed {
                            background: #d4edda;
                            color: #155724;
                            border: 1px solid #c3e6cb;
                        }
                        .action-btns .btn {
                            margin-right: 0.25rem;
                        }
                    </style>
                    <div class="row g-3">
                        @forelse ($bookings as $booking)
                            <div class="col-md-6 col-lg-6">
                                <div class="card shadow-sm h-100" style="border-radius: 16px;">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <div style="font-size:1.15rem;font-weight:600;">{{ ucfirst($booking['consultation_type']) }} Consultation</div>
                                                <div style="font-size:0.95rem;color:#666;">Booking ID: BKNG{{ $booking['id'] }}</div>
                                            </div>
                                            <span
                                                class="badge {{ $booking['status'] === 'in_progress' ? 'bg-success' : ($booking['status'] === 'confirmed' ? 'bg-primary' : ($booking['status'] === 'pending' ? 'bg-warning text-dark' : 'bg-secondary')) }}"
                                                style="font-size:0.95em;min-width:80px;text-align:center;"
                                                data-booking-status-badge="{{ $booking['id'] }}"
                                            >{{ str_replace('_', ' ', ucfirst($booking['status'])) }}</span>
                                        </div>
                                        <hr class="my-2">
                                        <style>
                                            .text-theme-orange { color: #f98800 !important; }
                                        </style>
                                        <div class="d-flex flex-wrap align-items-center mb-2" style="gap: 1rem;">
                                            <div style="min-width:120px;">
                                                <i class="fa-solid fa-calendar-day text-theme-orange me-1"></i>
                                                {{ \Carbon\Carbon::parse($booking['scheduled_at'])->format('d F Y') }}
                                            </div>
                                            <div style="min-width:90px;">
                                                <i class="fa-solid fa-location-dot text-theme-orange me-1"></i>
                                                {{ $booking['location'] ?? 'Online' }}
                                            </div>
                                        </div>
                                        <div class="d-flex flex-wrap align-items-center mb-2" style="gap: 1rem;">
                                            <div style="min-width:120px;">
                                                <i class="fa-solid fa-clock text-theme-orange me-1"></i>
                                                {{ \Carbon\Carbon::parse($booking['scheduled_at'])->format('h:i A') }}
                                                @if(isset($booking['end_time']))
                                                    - {{ \Carbon\Carbon::parse($booking['end_time'])->format('h:i A') }}
                                                @endif
                                            </div>
                                            <div style="font-weight:600;color:#219150;font-size:1.1rem;">
                                                ₹{{ $booking['rate'] }}
                                            </div>
                                        </div>
                                        <hr class="my-2">
                                        <style>
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
                                            .btn-outline-theme-danger {
                                                border: 1.5px solid #dc3545;
                                                color: #dc3545;
                                            }
                                            .btn-outline-theme-danger:hover, .btn-outline-theme-danger:focus {
                                                background: #dc3545;
                                                color: #fff;
                                            }
                                            .btn-outline-theme-success {
                                                border: 1.5px solid #28a745;
                                                color: #28a745;
                                            }
                                            .btn-outline-theme-success:hover, .btn-outline-theme-success:focus {
                                                background: #28a745;
                                                color: #fff;
                                            }
                                        </style>
                                        <div class="d-flex justify-content-between align-items-center mt-2 flex-wrap gap-2">
                                            <div class="d-flex flex-wrap gap-2">
                                                <a href="{{ route('booking.details', ['id' => $booking['id']]) }}" class="btn btn-outline-theme btn-sm">
                                                    <i class="fa-regular fa-eye"></i> View Details
                                                </a>
                                                <a
                                                    href="{{ route('customer.consultation.video', ['meetingId' => 'astro-' . $booking['id']]) }}"
                                                    class="btn btn-success btn-sm{{ ($booking['status'] ?? null) === 'in_progress' ? '' : ' d-none' }}"
                                                    data-booking-join-btn="{{ $booking['id'] }}"
                                                >
                                                    <i class="fa-solid fa-video me-1"></i> Join Consultation
                                                </a>
                                            </div>
                                            <div class="d-flex flex-wrap gap-2">
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
                            </div>
                        @empty
                            <div class="col-12 text-center text-muted py-4">No bookings found.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script id="my-bookings-page-data" type="application/json">{!! json_encode($myBookingsPageData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const pageDataEl = document.getElementById('my-bookings-page-data');

    if (!pageDataEl) {
        return;
    }

    const pageData = JSON.parse(pageDataEl.textContent || '{}');
    const bookingIds = Array.isArray(pageData.bookingIds) ? pageData.bookingIds : [];

    function formatStatus(status) {
        return String(status || 'pending')
            .replace(/_/g, ' ')
            .replace(/\b\w/g, function(char) {
                return char.toUpperCase();
            });
    }

    function applyBookingStatus(bookingId, status) {
        const badge = document.querySelector('[data-booking-status-badge="' + bookingId + '"]');
        const joinBtn = document.querySelector('[data-booking-join-btn="' + bookingId + '"]');

        if (badge) {
            badge.className = 'badge';

            if (status === 'in_progress') {
                badge.classList.add('bg-success');
            } else if (status === 'confirmed') {
                badge.classList.add('bg-primary');
            } else if (status === 'pending') {
                badge.classList.add('bg-warning', 'text-dark');
            } else {
                badge.classList.add('bg-secondary');
            }

            badge.style.fontSize = '0.95em';
            badge.style.minWidth = '80px';
            badge.style.textAlign = 'center';
            badge.textContent = formatStatus(status);
        }

        if (joinBtn) {
            joinBtn.classList.toggle('d-none', status !== 'in_progress');
        }
    }

    function fetchBookingStatus(bookingId) {
        return fetch('/astrologer/appointments/' + bookingId + '/ajax-status', {
            headers: { 'Accept': 'application/json' }
        })
        .then(function(res) {
            return res.json();
        })
        .then(function(data) {
            if (data && data.success && data.status) {
                applyBookingStatus(bookingId, data.status);
            }
        })
        .catch(function() {
            // Ignore transient polling failures on the bookings list.
        });
    }

    function refreshStatuses() {
        bookingIds.forEach(function(bookingId) {
            fetchBookingStatus(bookingId);
        });
    }

    refreshStatuses();

    if (bookingIds.length > 0) {
        setInterval(refreshStatuses, 10000);
    }
});
</script>
@endsection
