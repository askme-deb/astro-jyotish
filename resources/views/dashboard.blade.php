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

                            <div class="astro-avatar">
                                <img src="images/user.jpg" alt="Astrologer">
                                <span class="status-dot"></span>
                            </div>

                            <h4 class="astro-name">Astrologer Raju Maharaj</h4>
                            <div class="astro-role">Vedic Astrology • Hindi/English</div>

                            <div class="astro-badge">
                                ⭐ Verified Astrologer
                            </div>

                        </div>


                        <!-- Menu -->
                        <div class="astro-menu-card">

                            <h6 class="menu-title">MAIN MENU</h6>

                            <nav class="astro-menu">

                                <a class="" href="astrologer-dashboard.php">
                                    <i class="fas fa-gauge"></i> Dashboard
                                </a>

                                <a class="" href="astrologer-appointments.php">
                                    <i class="fas fa-calendar-check"></i> Appointments
                                </a>

                                <a class="" href="astrologer-availability.php">
                                    <i class="fas fa-clock"></i> Availability
                                </a>

                                <a class="" href="astrologer-earnings.php">
                                    <i class="fas fa-wallet"></i> Earnings
                                </a>

                                <a class="" href="astrologer-messages.php">
                                    <i class="fas fa-comments"></i> Messages
                                </a>

                                <a class="" href="astrologer-settings.php">
                                    <i class="fas fa-user-gear"></i> Profile Settings
                                </a>

                            </nav>

                        </div>


                        <!-- Quick Actions -->
                        <div class="astro-action-card">

                            <h6 class="menu-title">QUICK ACTIONS</h6>

                            <a class="btn btn-primary w-100 mb-2" href="consultation_booking.php">
                                + Create Booking
                            </a>

                            <button class="btn btn-outline-primary w-100 mb-2">
                                Add Time Slot
                            </button>

                            <a class="btn btn-outline-secondary w-100" href="index.php">
                                Back to Website
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
                        <div class="dashboard-title">Welcome back, Raju</div>
                        <div class="dashboard-subtitle">Manage bookings, availability, and earnings in one place.</div>
                    </div>
                    <div class="dashboard-header-right">
                        <span class="dashboard-status"><span class="dot"></span> Online</span>
                        <a class="btn btn-primary btn-sm" href="astrologer-appointments.php"><i class="fas fa-calendar-check me-1"></i> Appointments</a>
                        <a class="btn btn-outline-secondary btn-sm" href="astrologer-availability.php"><i class="fas fa-clock me-1"></i> Availability</a>
                    </div>
                </div>

                <!-- Stats -->
                <div class="row g-4" data-aos="fade-up" data-aos-delay="110">
                    <div class="col-md-6 col-xl-3">
                        <div class="sidebar-card dashboard-card dashboard-kpi">
                            <div class="dashboard-kpi-top">
                                <div>
                                    <div class="dashboard-kpi-label">Today’s Bookings</div>
                                    <div class="dashboard-kpi-value">5</div>
                                </div>
                                <div class="dashboard-kpi-icon"><i class="fas fa-calendar-day"></i></div>
                            </div>
                            <div class="dashboard-kpi-note">2 pending confirmations</div>
                            <div class="dashboard-kpi-bar"><span style="width: 70%"></span></div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="sidebar-card dashboard-card dashboard-kpi">
                            <div class="dashboard-kpi-top">
                                <div>
                                    <div class="dashboard-kpi-label">This Month</div>
                                    <div class="dashboard-kpi-value">₹18,450</div>
                                </div>
                                <div class="dashboard-kpi-icon"><i class="fas fa-wallet"></i></div>
                            </div>
                            <div class="dashboard-kpi-note">+12% vs last month</div>
                            <div class="dashboard-kpi-bar"><span style="width: 55%"></span></div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="sidebar-card dashboard-card dashboard-kpi">
                            <div class="dashboard-kpi-top">
                                <div>
                                    <div class="dashboard-kpi-label">Rating</div>
                                    <div class="dashboard-kpi-value">4.8</div>
                                </div>
                                <div class="dashboard-kpi-icon"><i class="fas fa-star"></i></div>
                            </div>
                            <div class="dashboard-kpi-note">214 reviews</div>
                            <div class="dashboard-kpi-bar"><span style="width: 96%"></span></div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="sidebar-card dashboard-card dashboard-kpi">
                            <div class="dashboard-kpi-top">
                                <div>
                                    <div class="dashboard-kpi-label">Unread</div>
                                    <div class="dashboard-kpi-value">3</div>
                                </div>
                                <div class="dashboard-kpi-icon"><i class="fas fa-comments"></i></div>
                            </div>
                            <div class="dashboard-kpi-note">Messages waiting</div>
                            <div class="dashboard-kpi-bar"><span style="width: 35%"></span></div>
                        </div>
                    </div>
                </div>

                <div class="row g-4 dashboard-section" data-aos="fade-up" data-aos-delay="150">
                    <div class="col-lg-8">
                        <div class="sidebar-card dashboard-card">
                            <div class="dashboard-section-head">
                                <h3>Today’s Schedule</h3>
                                <div class="dashboard-section-actions">
                                    <a class="btn btn-outline-secondary btn-sm" href="astrologer-appointments.php">View all</a>
                                </div>
                            </div>

                            <div class="dashboard-timeline">
                                <div class="dashboard-timeline-item">
                                    <div class="time">10:00 AM</div>
                                    <div class="content">
                                        <div class="title">Video Call • Priya Singh</div>
                                        <div class="meta">Love & Relationship • <span class="badge text-bg-warning">Pending</span></div>
                                    </div>
                                    <div class="actions">
                                        <button type="button" class="btn btn-primary btn-sm">Confirm</button>
                                    </div>
                                </div>
                                <div class="dashboard-timeline-item">
                                    <div class="time">11:00 AM</div>
                                    <div class="content">
                                        <div class="title">Phone Call • Rahul Sharma</div>
                                        <div class="meta">Career Guidance • <span class="badge text-bg-success">Confirmed</span></div>
                                    </div>
                                    <div class="actions">
                                        <button type="button" class="btn btn-outline-secondary btn-sm">Details</button>
                                    </div>
                                </div>
                                <div class="dashboard-timeline-item">
                                    <div class="time">4:00 PM</div>
                                    <div class="content">
                                        <div class="title">Video Call • Meena Patel</div>
                                        <div class="meta">Health • <span class="badge text-bg-secondary">Completed</span></div>
                                    </div>
                                    <div class="actions">
                                        <button type="button" class="btn btn-outline-secondary btn-sm">Notes</button>
                                    </div>
                                </div>
                            </div>

                            <div class="dashboard-muted">Static preview only (you’ll wire dynamic data later).</div>
                        </div>
                    </div>

                    <div class="col-lg-4">
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

                        <!-- <div class="sidebar-card dashboard-card mt-4">
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
