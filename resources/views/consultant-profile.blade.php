@extends('layouts.app')

@section('title', $consultant['name'] ?? 'Consultant Profile')

@section('content')

<div class="container mt-4 inner_back">

    <div class="banner">

        <!-- Background Image -->
        <img src="{{ asset('assets/images/consul2.png') }}" alt="Astrology Banner">

        <!-- Overlay -->
        <div class="banner-overlay">

            <div class="banner-content">
                <h1>
                    Discover Your Destiny with <br>
                    Trusted <span>Astrologers</span>
                </h1>

                @php $isLoggedIn = session('auth.user') ? true : false; @endphp
                <button class="appointment-btn" onclick="@if(!$isLoggedIn) showAuthModal(); @else window.location.href='/consultation'; @endif">
                    Get an Appointment
                </button>
            </div>

        </div>

    </div>

</div>

<div class="container my-4 dite_ls">
    <div class="row">
        <!-- LEFT SIDE -->
        <div class="col-lg-8">
            <!-- Profile -->
            <div class="profile-card d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <img src="{{ $consultant['image'] ?? asset('assets/images/default-profile.png') }}" class="profile-img me-3">
                    <div>
                        <h4 class="mb-1">{{ $consultant['name'] ?? '-' }}</h4>
                        <p class="mb-1 text-muted">{{ $consultant['skills'] ? implode(', ', $consultant['skills']) : 'Astrology' }}</p>
                        <small class="text-muted">{{ $consultant['languages'] }} | {{ $consultant['experience'] }} | {{ $consultant['location'] ?? '' }}</small>
                        <div class="nhgd">
                            @if(!empty($consultant['skills']))
                                @foreach($consultant['skills'] as $skill)
                                    <span class="skill-badge">{{ $skill }}</span>
                                @endforeach
                            @endif
                        </div>
                        <div class="mt-2">
                            <span class="badge bg-warning text-dark">{{ $consultant['rating'] ?? '-' }} ★</span>
                        </div>
                    </div>
                </div>
                <!-- <button id="followBtn" class="btn btn-outline-primary btn-sm btn-custom" onclick="toggleFollow()">+ Follow</button> -->
            </div>

          

            <!-- About -->
            <div class="section-box">
                <h6>About</h6>
                <p class="text-muted">
                    {{ $consultant['bio'] ?? 'No bio available.' }}
                </p>
            </div>

            <!-- Reviews -->
            <div class="container">

                <div class="section-box">
                    <h5 class="mb-3">Reviews</h5>

                    <div id="reviewList">

                        <div class="review-card">
                            <strong>R S. ⭐⭐⭐⭐⭐</strong>
                            <p class="mb-0 text-muted">Thanks 🙏</p>
                        </div>

                        <div class="review-card">
                            <strong>V K. ⭐⭐⭐⭐⭐</strong>
                            <p class="mb-0 text-muted">She's good.</p>
                        </div>

                    </div>

                    <div class="text-center mt-3">
                        <button class="btn btn-outline-warning view-more" onclick="loadMore()">View More</button>
                    </div>

                </div>

            </div>



        </div>

        <!-- RIGHT SIDE -->
        <div class="col-lg-4">

            <!-- Price & Actions -->
            <div class="price-card mb-3">
                <h5>₹ {{ $consultant['price'] ?? '-' }} / Session</h5>
                <!-- Video Consultation Button (shown if session is in progress, adjust logic as needed) -->
                @php
                    $appointmentId = $consultant['appointment_id'] ?? null;
                    $sessionInProgress = $consultant['session_status'] ?? false;
                @endphp
                @if($appointmentId)
                    <a href="{{ $sessionInProgress ? route('customer.consultation.video', ['meetingId' => 'astro-' . $appointmentId]) : '#' }}"
                       class="btn btn-success w-100 mb-2 btn-custom{{ !$sessionInProgress ? ' disabled' : '' }}"
                       @if(!$sessionInProgress) tabindex="-1" aria-disabled="true" @endif>
                        <i class="fa-solid fa-video me-1"></i> Join Video Consultation
                    </a>
                @endif
                @php $isLoggedIn = session('auth.user') ? true : false; @endphp
                <button class="btn btn-danger mt-3 tgwe" onclick="@if(!$isLoggedIn) showAuthModal(); @else window.location.href='/consultation'; @endif">
                    <i class="fas fa-calendar-check"></i> Get an Appointment
                </button>
                <script>
                function showAuthModal() {
                    var modal = document.getElementById('authModal');
                    if (modal) {
                        var bsModal = bootstrap.Modal.getInstance(modal) || new bootstrap.Modal(modal);
                        bsModal.show();
                    }
                }
                </script>
            </div>

            <!-- Ratings -->
            <div class="price-card mb-3">
                <h6>Ratings</h6>
                <h4>{{ $consultant['rating'] ?? '-' }} ★</h4>
                <!-- If you have rating breakdown data, render it here. Otherwise, show only the average. -->
            </div>

            <!-- Security Info -->
            <div class="price-card">
                <p><i class="bi bi-shield-check text-success"></i> Money Back Guarantee</p>
                <p><i class="bi bi-patch-check text-primary"></i> Verified Expert Astrologers</p>
                <p><i class="bi bi-lock text-danger"></i> 100% Secure Payments</p>
            </div>

        </div>

    </div>
</div>
@endsection

