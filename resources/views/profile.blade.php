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
                    <div class="astro-sidebar-inner">
                        <!-- Profile Card -->
                        <div class="astro-profile-card">
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
                                <a class="" href="/my-bookings">
                                    <i class="fas fa-calendar-check"></i> My Bookings
                                </a>
                                <a  href="/profile">
                                    <i class="fas fa-user"></i> My Profile
                                </a>
                                <a href="{{ route('customer.supportTickets.index') }}">
                                    <i class="fas fa-life-ring"></i> Support Tickets
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
                </aside>
            </div>
            <!-- Main content -->
            <div class="col-lg-9">
                <div class="sidebar-card dashboard-card dashboard-header" data-aos="fade-up" data-aos-delay="80">
                    <div class="dashboard-header-left">
                        <div class="dashboard-title">My Profile</div>
                        <div class="dashboard-subtitle">View and update your profile information.</div>
                    </div>
                </div>
                <div class="sidebar-card dashboard-card mt-4">
                    <div class="dashboard-section-head">
                        <h3>Profile Details</h3>
                    </div>
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form id="profile-update-form" method="POST" action="{{ route('profile.update') }}" autocomplete="off">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">First Name</label>
                                <input type="text" class="form-control" name="first_name" value="{{ old('first_name', session('auth.user.first_name')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-control" name="last_name" value="{{ old('last_name', session('auth.user.last_name')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="{{ old('email', session('auth.user.email')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mobile</label>
                                <input type="text" class="form-control" name="mobile_no" value="{{ old('mobile_no', session('auth.user.mobile_no')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Alternate Phone</label>
                                <input type="text" class="form-control" name="alt_ph" value="{{ old('alt_ph', session('auth.user.alt_ph')) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Address</label>
                                <input type="text" class="form-control" name="address" value="{{ old('address', session('auth.user.address')) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control" name="city" value="{{ old('city', session('auth.user.city')) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">State</label>
                                <input type="text" class="form-control" name="state" value="{{ old('state', session('auth.user.state')) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Country ID</label>
                                <input type="number" class="form-control" name="country_id" value="{{ old('country_id', session('auth.user.country_id')) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pincode</label>
                                <input type="text" class="form-control" name="pincode" value="{{ old('pincode', session('auth.user.pincode')) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Wallet Balance</label>
                                <input type="text" class="form-control" value="₹{{ session('auth.user.wallet_balance', '0.00') }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <input type="text" class="form-control" value="{{ session('auth.user.status') == 'Y' ? 'Active' : 'Inactive' }}" readonly>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary" id="profile-update-btn">Update Profile</button>
                        </div>
                    </form>
                    <div id="profile-update-message" class="mt-3"></div>
                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const form = document.getElementById('profile-update-form');
                        const btn = document.getElementById('profile-update-btn');
                        const msg = document.getElementById('profile-update-message');
                        form.addEventListener('submit', function(e) {
                            e.preventDefault();
                            msg.innerHTML = '';
                            btn.disabled = true;
                            const formData = new FormData(form);
                            fetch(form.action, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                                },
                                body: formData
                            })
                            .then(response => response.json().catch(() => response.text()))
                            .then(data => {
                                btn.disabled = false;
                                if (typeof data === 'object' && data !== null) {
                                    if (data.success) {
                                        msg.innerHTML = '<div class="alert alert-success">' + data.success + '</div>';
                                        setTimeout(() => window.location.reload(), 1200);
                                    } else if (data.error) {
                                        msg.innerHTML = '<div class="alert alert-danger">' + data.error + '</div>';
                                    } else if (data.errors) {
                                        let html = '<div class="alert alert-danger"><ul>';
                                        for (const key in data.errors) {
                                            html += '<li>' + data.errors[key][0] + '</li>';
                                        }
                                        html += '</ul></div>';
                                        msg.innerHTML = html;
                                    }
                                } else {
                                    msg.innerHTML = '<div class="alert alert-danger">Unexpected error. Please try again.</div>';
                                }
                            })
                            .catch(() => {
                                btn.disabled = false;
                                msg.innerHTML = '<div class="alert alert-danger">Network error. Please try again.</div>';
                            });
                        });
                    });
                    </script>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
