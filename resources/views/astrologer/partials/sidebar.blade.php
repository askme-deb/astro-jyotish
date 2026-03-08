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
                            <!-- <div class="astro-badge">⭐ Verified Astrologer</div> -->

                        </div>


                        <!-- Menu -->
                        <div class="astro-menu-card">

                            <h6 class="menu-title">MAIN MENU</h6>

                            <nav class="astro-menu">

                                <a class="" href="/dashboard">
                                    <i class="fas fa-gauge"></i> Dashboard
                                </a>
                                <a class="" href="/appointments">
                                    <i class="fas fa-calendar-check"></i> Appointments
                                </a> 
                                <a class="" href="/my-bookings">
                                    <i class="fas fa-calendar-check"></i> Availability
                                </a> 
                                <a class="" href="/my-bookings">
                                    <i class="fas fa-calendar-check"></i> Earnings
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
