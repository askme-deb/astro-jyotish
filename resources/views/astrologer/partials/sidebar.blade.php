     <div class="astro-sidebar-inner">

                        @php
                            $currentRouteName = request()->route()?->getName();
                            $currentPath = trim(request()->path(), '/');
                            $isDashboardActive = in_array($currentRouteName, ['dashboard', 'astrologer.dashboard'], true) || $currentPath === 'dashboard' || $currentPath === 'astrologer/dashboard';
                            $isAppointmentsActive = $currentRouteName === 'astrologer.appointments' || $currentPath === 'appointments';
                            $isCompletedActive = $currentRouteName === 'astrologer.appointments.completed';
                            $isCancelledActive = $currentRouteName === 'astrologer.appointments.cancelled';
                            $isEarningsActive = str_starts_with((string) $currentRouteName, 'astrologer.earnings');
                            $isSupportActive = str_starts_with((string) $currentRouteName, 'astrologer.supportTickets');
                            $isProfileActive = $currentRouteName === 'profile' || $currentRouteName === 'profile.update' || $currentPath === 'profile';
                        @endphp

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

                                <a class="{{ $isDashboardActive ? 'active' : '' }}" href="/dashboard">
                                    <i class="fas fa-gauge"></i> Dashboard
                                </a>
                                <a class="{{ $isAppointmentsActive ? 'active' : '' }}" href="/appointments">
                                    <i class="fas fa-calendar-check"></i> Appointments
                                </a>
                                <a class="{{ $isCompletedActive ? 'active' : '' }}" href="{{ route('astrologer.appointments.completed') }}">
                                    <i class="fas fa-circle-check"></i> Completed
                                </a>
                                <a class="{{ $isCancelledActive ? 'active' : '' }}" href="{{ route('astrologer.appointments.cancelled') }}">
                                    <i class="fas fa-ban"></i> Cancelled
                                </a>
                                 <a class="{{ $isEarningsActive ? 'active' : '' }}" href="{{ route('astrologer.earnings') }}">
                                        <i class="fas fa-wallet"></i> Earnings
                                    </a>
                                <a class="{{ $isSupportActive ? 'active' : '' }}" href="{{ route('astrologer.supportTickets.index') }}">
                                    <i class="fas fa-life-ring"></i> Support
                                </a>
                                {{-- <a class="" href="/my-bookings">
                                    <i class="fas fa-calendar-check"></i> Availability
                                </a>

                                    {{-- <a class="" href="/my-bookings">
                                        <i class="fas fa-calendar-check"></i> Availability
                                    </a> --}}
                                <a class="{{ $isProfileActive ? 'active' : '' }}" href="/profile">
                                    <i class="fas fa-user"></i> My Profile
                                </a>

                            </nav>

                        </div>


                        <!-- Quick Actions -->
                        <div class="astro-action-card">

                            <h6 class="menu-title">QUICK ACTIONS</h6>

                            <a class="btn btn-primary w-100 mb-2" href="/consultation">
                                + Book Consultation
                            </a>
                            <a class="btn btn-outline-secondary w-100" href="/">
                                Back to Home
                            </a>

                        </div>

                    </div>
