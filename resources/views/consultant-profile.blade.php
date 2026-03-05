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

                <button class="appointment-btn">
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
                    <img src="https://randomuser.me/api/portraits/women/65.jpg" class="profile-img me-3">
                    <div>
                        <h4 class="mb-1">Tarot Vedansshi</h4>
                        <p class="mb-1 text-muted">Tarot Reading</p>
                        <small class="text-muted">English, Hindi | 1 Year | Noida</small>
                        <div class="nhgd">
                            <span class="skill-badge">Career Counseling</span>
                            <span class="skill-badge">Career Counseling</span>
                            <span class="skill-badge">Career Counseling</span>
                        </div>
                        <div class="mt-2">
                            <span class="badge bg-warning text-dark">4.96 ★</span>
                        </div>
                    </div>
                </div>
                <!-- <button id="followBtn" class="btn btn-outline-primary btn-sm btn-custom" onclick="toggleFollow()">+ Follow</button> -->
            </div>

          

            <!-- About -->
            <div class="section-box">
                <h6>About</h6>
                <p class="text-muted">
                    I am Tarot Vedansshi, a tarot reader with one year of experience in guiding individuals
                    through life’s questions and crossroads. My approach is calm, empathetic, and non-judgmental,
                    creating a safe space to explore thoughts and emotions.
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
                <h5>₹ 12 / Min</h5>
                <button class="btn btn-success w-100 mb-2 btn-custom">
                    <i class="bi bi-telephone"></i> Join Call
                </button>
                <button class="btn btn-success w-100 mb-2 btn-custom">
                    <i class="bi bi-chat-dots"></i> Join Chat
                </button>
                <button class="btn btn-danger mt-3 tgwe"> <i class="fas fa-calendar-check"></i> Get an Appointment</button>
            </div>

            <!-- Ratings -->
            <div class="price-card mb-3">
                <h6>Ratings</h6>
                <h4>4.96 ★</h4>

                <div class="mb-2">
                    5 ★
                    <div class="progress">
                        <div class="progress-bar bg-success" style="width:95%"></div>
                    </div>
                </div>

                <div class="mb-2">
                    4 ★
                    <div class="progress">
                        <div class="progress-bar bg-success" style="width:3%"></div>
                    </div>
                </div>

                <div class="mb-2">
                    3 ★
                    <div class="progress">
                        <div class="progress-bar bg-warning" style="width:1%"></div>
                    </div>
                </div>

                <div>
                    2 ★
                    <div class="progress">
                        <div class="progress-bar bg-danger" style="width:1%"></div>
                    </div>
                </div>
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