@extends('layouts.app')

@section('title', 'Welcome')

@section('content')

    <!-- Hero Section -->
    <div class="container top_banner_warp">
        <div id="heroSlider" class="carousel slide hero-slider" data-bs-ride="carousel">
            <!-- Indicators -->
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="1"></button>
                <!-- <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="2"></button> -->
            </div>
            <div class="carousel-inner">
                <!-- Slide 1 -->
                <div class="carousel-item active">
                    <div class="container">
                        <div class="hero-banner d-flex flex-column flex-md-row align-items-center justify-content-between">
                            <div class="content_warp">
                                <h1>Not Just Astrology. <br>A Deeper Perspective of Life.</h1>
                               <p style="color: #fff;">With Astrologer Raju Maharaj Ji
                                Go beyond predictions and understand<br> the true
                                influence of planetary energies in your journey.</p>
                                    @php $isLoggedIn = session('auth.user') ? true : false; @endphp
                                    <button class="btn btn-danger mt-3" onclick="@if(!$isLoggedIn) showAuthModal(); @else window.location.href='/consultation'; @endif"> Consult Now</button>
                            </div>
                            <img src="{{ asset('assets/images/banner-01.png') }}" class="img-fluid rounded">
                        </div>
                    </div>
                </div>
                <!-- Slide 2 -->
                <div class="carousel-item">
                    <div class="container">
                        <div class="hero-banner d-flex flex-column flex-md-row align-items-center justify-content-between">
                            <div class="content_warp">
                                <h1>Guided by the Wisdom<br> of Vedic Astrology</h1>
                                <ul style="color: #fff; list-style-type: none; padding-left: 0;">
                                    <li> Trusted Expertise</li>
                                    <li> Accurate Predictions</li>
                                    <li> Meaningful Guidance</li>
                                </ul>
                                <p style="color: #fff;">Discover deeper insights into your life through authentic astrology practices.</p>
                    @php $isLoggedIn = session('auth.user') ? true : false; @endphp
                    <!-- <button class="btn btn-danger mt-3" onclick="@if(!$isLoggedIn) showAuthModal(); @else window.location.href='/consultation'; @endif"> Consult Now</button> -->
                            </div>
                            <img src="{{ asset('assets/images/banner-02.png') }}" class="img-fluid rounded">
                        </div>
                    </div>
                </div>
                <!-- Slide 3 -->
                <!-- <div class="carousel-item">
                    <div class="container">
                        <div class="hero-banner d-flex flex-column flex-md-row align-items-center justify-content-between">
                            <div class="content_warp">
                                <h1>Discover Your Destiny with <br> Trusted <span class="text-danger">Astrologers</span></h1>
                    @php $isLoggedIn = session('auth.user') ? true : false; @endphp
                    <button class="btn btn-danger mt-3" onclick="@if(!$isLoggedIn) showAuthModal(); @else window.location.href='/consultation'; @endif"> Get an Appointment</button>
                            </div>
                            <img src="{{ asset('assets/images/banner-2.png') }}" class="img-fluid rounded">
                        </div>
                    </div>
                </div>
            </div> -->
            <!-- Controls -->
            <button class="carousel-control-prev" type="button" data-bs-target="#heroSlider" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroSlider" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
    </div>
    <script>
        function showAuthModal() {
            var modal = document.getElementById('authModal');
            if (modal) {
                var bsModal = bootstrap.Modal.getInstance(modal) || new bootstrap.Modal(modal);
                bsModal.show();
            }
        }
    </script>
    <!-- Service Section -->
    <div class="container mb-5">
        <div class="row g-4 cxswaq">
            <!-- Talk -->
            <div class="col-md-3">
                <div class="service-card d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box">
                            <img src="{{ asset('assets/images/vastu.png') }}" />
                        </div>
                        <div class="service-title">Vastu</div>
                    </div>
                    <i class="bi bi-arrow-right arrow"></i>
                </div>
            </div>
            <!-- Chat -->
            <div class="col-md-3">
                <div class="service-card d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box">
                            <img src="{{ asset('assets/images/remedi.png') }}" />
                        </div>
                        <div class="service-title">Remedies</div>
                    </div>
                    <i class="bi bi-arrow-right arrow"></i>
                </div>
            </div>
            <!-- Horoscope -->
            <div class="col-md-3">
                <div class="service-card d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box">
                            <img src="{{ asset('assets/images/horoscope.png') }}" />
                        </div>
                        <div class="service-title">Horoscope</div>
                    </div>
                    <i class="bi bi-arrow-right arrow"></i>
                </div>
            </div>
            <!-- Kundli -->
            <div class="col-md-3">
                <div class="service-card d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-box">
                            <img src="{{ asset('assets/images/kunduli.png') }}" />
                        </div>
                        <div class="service-title">Kundli</div>
                    </div>
                    <i class="bi bi-arrow-right arrow"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="container my-5">
        <!-- Top Online Astrologers -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="section-title">Top Online Astrologers</div>
            <a href="{{ route('consultant') }}" class="view-all" style="text-decoration: none;">View All</a>
        </div>
        <div class="row g-4">

            @if(!empty($astrologers) && is_array($astrologers))
            @foreach(array_slice($astrologers, 0, 3) as $astrologer)

            <x-astrologer-card :astrologer="$astrologer" />

            @endforeach
            @else
            <div class="col-12">
                <div class="alert alert-warning text-center">No astrologers found.</div>
            </div>
            @endif

        </div>

    </div>
    <section class="sale-banner-wrapper">
        <div class="container">
            <div class="sale-banner-placeholder">
                <div class="banner-content">
                    <h4>Find the Right Gemstone for Your Kundli</h4>
                    <p>Astrology-Based Gemstone Guidance & Recommendations
Choose<br>authentic stones aligned with your planetary positions
to enhance<br> balance, growth, and positivity in life.</p>

                    <button>Explore Gemstones</button>
                </div>
            </div>
        </div>
    </section>
    <div class="container section-space">
        <!-- Schedule Appointment -->
        <div class="d-flex justify-content-between align-items-center mb-4 dwez">
            <h4 class="section-title">Schedule Appointment</h4>
            <button class="view-btn">View All</button>
        </div>
        <section class="schedule-section">
            <div class="container">
                <div class="live-show">
                    <a class="live-show-box" href="/consultation">
                        <div class="schedule-category">
                            <div class="schedule-icon">
                                <img loading="lazy" src="{{ asset('assets/images/hend.png') }}" alt="Reiki Healing">
                            </div>
                            <span>Career Counseling</span>
                        </div>
                    </a>
                    <a class="live-show-box" href="/consultation">
                        <div class="schedule-category">
                            <div class="schedule-icon">
                                <img loading="lazy" src="{{ asset('assets/images/astology.png') }}" alt="Popular Astrologer">
                            </div>
                            <span>Face Reading</span>
                        </div>
                    </a>
                    <a class="live-show-box" href="/consultation">
                        <div class="schedule-category">
                            <div class="schedule-icon">
                                <img loading="lazy" src="{{ asset('assets/images/tarot.png') }}" alt="Learn Tarot">
                            </div>
                            <span>Gems Stone Guidance</span>
                        </div>
                    </a>
                    <!-- <a class="live-show-box" href="/consultation">
                   <div class="schedule-category">
                       <div class="schedule-icon">
                           <img loading="lazy" src="https://images.astroyogi.com/strapicmsprod/assets/Rudra_Abhishek_Pooja_0779457b37.svg" alt="Rudra Abhishek Pooja">
                       </div>
                       <span>Rudra Abhishek Pooja</span>
                   </div>
                   </a> -->
                    <a class="live-show-box" href="/astrologer/palm-reader">
                        <div class="schedule-category">
                            <div class="schedule-icon">
                                <img loading="lazy" src="{{ asset('assets/images/rood.png') }}" alt="Palm Reader">
                            </div>
                            <span>Horoscope Reading</span>
                        </div>
                    </a>
                </div>
            </div>
        </section>
    </div>
    <!-- Zodiac Prediction -->
    <div class="container section-space">
        <h4 class="section-title mb-4">Today's Astrology Prediction</h4>
        <div class="row">
            <div class="container my-5">
                <div class="row g-4">
                    <!-- Aries -->
                    <div class="col-lg-2 col-md-4 col-6">
                        <a href="#" class="zodiac-card text-center d-block p-3 shadow rounded">
                            <div class="zodiac-icon mb-2">
                                <img src="{{ asset('assets/images/zodiac/aries.svg') }}" alt="Aries" class="img-fluid" width="60">
                            </div>
                            <h6>Aries</h6>
                            <p class="small text-muted">Mar 21 – Apr 19</p>
                        </a>
                    </div>
                    <!-- Taurus -->
                    <div class="col-lg-2 col-md-4 col-6">
                        <a href="#" class="zodiac-card text-center d-block p-3 shadow rounded">
                            <div class="zodiac-icon mb-2">
                                <img src="{{ asset('assets/images/zodiac/taurus.svg') }}" alt="Taurus" class="img-fluid" width="60">
                            </div>
                            <h6>Taurus</h6>
                            <p class="small text-muted">Apr 20 – May 20</p>
                        </a>
                    </div>
                    <!-- Gemini -->
                    <div class="col-lg-2 col-md-4 col-6">
                        <a href="#" class="zodiac-card text-center d-block p-3 shadow rounded">
                            <div class="zodiac-icon mb-2">
                                <img src="{{ asset('assets/images/zodiac/gemini.svg') }}" alt="Gemini" class="img-fluid" width="60">
                            </div>
                            <h6>Gemini</h6>
                            <p class="small text-muted">May 21 – Jun 20</p>
                        </a>
                    </div>
                    <!-- Cancer -->
                    <div class="col-lg-2 col-md-4 col-6">
                        <a href="#" class="zodiac-card text-center d-block p-3 shadow rounded">
                            <div class="zodiac-icon mb-2">
                                <img src="{{ asset('assets/images/zodiac/cancer.svg') }}" alt="Cancer" class="img-fluid" width="60">
                            </div>
                            <h6>Cancer</h6>
                            <p class="small text-muted">Jun 21 – Jul 22</p>
                        </a>
                    </div>
                    <!-- Leo -->
                    <div class="col-lg-2 col-md-4 col-6">
                        <a href="#" class="zodiac-card text-center d-block p-3 shadow rounded">
                            <div class="zodiac-icon mb-2">
                                <img src="{{ asset('assets/images/zodiac/leo.svg') }}" alt="Leo" class="img-fluid" width="60">
                            </div>
                            <h6>Leo</h6>
                            <p class="small text-muted">Jul 23 – Aug 22</p>
                        </a>
                    </div>
                    <!-- Virgo -->
                    <div class="col-lg-2 col-md-4 col-6">
                        <a href="#" class="zodiac-card text-center d-block p-3 shadow rounded">
                            <div class="zodiac-icon mb-2">
                                <img src="{{ asset('assets/images/zodiac/virgo.svg') }}" alt="Virgo" class="img-fluid" width="60">
                            </div>
                            <h6>Virgo</h6>
                            <p class="small text-muted">Aug 23 – Sep 22</p>
                        </a>
                    </div>
                    <!-- Libra -->
                    <div class="col-lg-2 col-md-4 col-6">
                        <a href="#" class="zodiac-card text-center d-block p-3 shadow rounded">
                            <div class="zodiac-icon mb-2">
                                <img src="{{ asset('assets/images/zodiac/libra.svg') }}" alt="Libra" class="img-fluid" width="60">
                            </div>
                            <h6>Libra</h6>
                            <p class="small text-muted">Sep 23 – Oct 22</p>
                        </a>
                    </div>
                    <!-- Scorpio -->
                    <div class="col-lg-2 col-md-4 col-6">
                        <a href="#" class="zodiac-card text-center d-block p-3 shadow rounded">
                            <div class="zodiac-icon mb-2">
                                <img src="{{ asset('assets/images/zodiac/scorpio.svg') }}" alt="Scorpio" class="img-fluid" width="60">
                            </div>
                            <h6>Scorpio</h6>
                            <p class="small text-muted">Oct 23 – Nov 21</p>
                        </a>
                    </div>
                    <!-- Sagittarius -->
                    <div class="col-lg-2 col-md-4 col-6">
                        <a href="#" class="zodiac-card text-center d-block p-3 shadow rounded">
                            <div class="zodiac-icon mb-2">
                                <img src="{{ asset('assets/images/zodiac/sagittairus.svg') }}" alt="Sagittarius" class="img-fluid" width="60">
                            </div>
                            <h6>Sagittarius</h6>
                            <p class="small text-muted">Nov 22 – Dec 21</p>
                        </a>
                    </div>
                    <!-- Capricorn -->
                    <div class="col-lg-2 col-md-4 col-6">
                        <a href="#" class="zodiac-card text-center d-block p-3 shadow rounded">
                            <div class="zodiac-icon mb-2">
                                <img src="{{ asset('assets/images/zodiac/capricorn.svg') }}" alt="Capricorn" class="img-fluid" width="60">
                            </div>
                            <h6>Capricorn</h6>
                            <p class="small text-muted">Dec 22 – Jan 19</p>
                        </a>
                    </div>
                    <!-- Aquarius -->
                    <div class="col-lg-2 col-md-4 col-6">
                        <a href="#" class="zodiac-card text-center d-block p-3 shadow rounded">
                            <div class="zodiac-icon mb-2">
                                <img src="{{ asset('assets/images/zodiac/aquarius.svg') }}" alt="Aquarius" class="img-fluid" width="60">
                            </div>
                            <h6>Aquarius</h6>
                            <p class="small text-muted">Jan 20 – Feb 18</p>
                        </a>
                    </div>
                    <!-- Pisces -->
                    <div class="col-lg-2 col-md-4 col-6">
                        <a href="#" class="zodiac-card text-center d-block p-3 shadow rounded">
                            <div class="zodiac-icon mb-2">
                                <img src="{{ asset('assets/images/zodiac/pisces.svg') }}" alt="Pisces" class="img-fluid" width="60">
                            </div>
                            <h6>Pisces</h6>
                            <p class="small text-muted">Feb 19 – Mar 20</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="container section-space">
            <h4 class="section-title">Jyotish & Counseling Services</h4>
            <div class="row g-4">
                <!-- Card Item -->
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="javascript:;" class="text-decoration-none">
                        <div class="reading-card">
                            <img src="{{asset('assets/images/readings/match_making.png')}}" alt="Career Counseling">
                            <p>Career Counseling</p>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="javascript:;" class="text-decoration-none">
                        <div class="reading-card">
                            <img src="{{asset('assets/images/readings/kundli.png')}}" alt="Face Reading">
                            <p>Face Reading</p>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="javascript:;" class="text-decoration-none">
                        <div class="reading-card">
                            <img src="{{asset('assets/images/readings/planet.png')}}" alt="Gems Stone Guidance">
                            <p>Gems Stone Guidance</p>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="javascript:;" class="text-decoration-none">
                        <div class="reading-card">
                            <img src="{{asset('assets/images/readings/remedies.png')}}" alt="Horoscope Reading">
                            <p>Horoscope Reading</p>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="javascript:;" class="text-decoration-none">
                        <div class="reading-card">
                            <img src="{{asset('assets/images/readings/love.png')}}" alt="Kundli Matching">
                            <p>Kundli Matching</p>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="javascript:;" class="text-decoration-none">
                        <div class="reading-card">
                            <img src="{{asset('assets/images/readings/panchang.png')}}" alt="Muhurat">
                            <p>Muhurat</p>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="javascript:;" class="text-decoration-none">
                        <div class="reading-card">
                            <img src="{{asset('assets/images/readings/tarot.png')}}" alt="Palm Reading">
                            <p>Palm Reading</p>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="javascript:;" class="text-decoration-none">
                        <div class="reading-card">
                            <img src="{{asset('assets/images/readings/numerology.png')}}" alt="Prashna Kundali">
                            <p>Prashna Kundali</p>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="javascript:;" class="text-decoration-none">
                        <div class="reading-card">
                            <img src="{{asset('assets/images/readings/vastu.png')}}" alt="Vastu Shastra">
                            <p>Vastu Shastra</p>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="javascript:;" class="text-decoration-none">
                        <div class="reading-card">
                            <img src="{{asset('assets/images/readings/zodiac.png')}}" alt="Vedic Astrology">
                            <p>Vedic Astrology</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <div class="container blog-section">
            <!-- Header -->
            <div class="blog-header">
                <h4 class="fw-semibold">Blogs</h4>
                <button class="view-all-btn">View All</button>
            </div>
            <!-- Row -->
            <div class="row g-4">
                @if(!empty($blogs) && is_array($blogs))
                    @foreach(array_slice($blogs, 0, 3) as $blog)
                        <div class="col-md-6 col-lg-4">
                            @php $blogUrl = url('blog/' . ($blog['slug'] ?? $blog['id'])); @endphp
                            <a href="{{ $blogUrl }}" class="text-decoration-none">
                                <div class="blog-card">
                                    <div class="blog-img">
                                        <img src="{{ $blog['image'] ?? asset('assets/images/default-blog.jpg') }}" alt="{{ $blog['title'] ?? '' }}">
                                    </div>
                                    <div class="blog-body">
                                        <div class="blog-title">
                                            {{ $blog['title'] ?? '' }}
                                        </div>
                                        <div class="blog-footer">
                                            <div class="author-info">
                                                <img src="https://i.pravatar.cc/100?u={{ urlencode($blog['author'] ?? 'blog') }}" alt="">
                                                <span>{{ $blog['author'] ?? '' }}{{ $blog['published_at'] ? ' | ' . \Carbon\Carbon::parse($blog['published_at'])->format('D, M d, Y') : '' }}</span>
                                            </div>
                                            <div class="arrow-btn">
                                                <i class="bi bi-arrow-right"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                @else
                    <div class="col-12">
                        <div class="alert alert-warning text-center">No blogs found.</div>
                    </div>
                @endif
            </div>
        </div>


        <section class="testimonial-section">
            <div class="container">
                <h4 class="testimonial-title">Testimonials</h4>
                <div id="testimonialSlider" class="carousel slide" data-bs-ride="carousel">
                    <!-- Indicators -->
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#testimonialSlider" data-bs-slide-to="0" class="active"></button>
                        <button type="button" data-bs-target="#testimonialSlider" data-bs-slide-to="1"></button>
                    </div>
                    <div class="carousel-inner">
                        <!-- Slide 1 -->
                        <div class="carousel-item active">
                            <div class="row g-4">
                                <div class="col-md-6 col-lg-4">
                                    <div class="testimonial-card">
                                        <div class="testimonial-text">
                                            I was confused about my career direction for a long time. After consultation, I got clear guidance and confidence. The predictions were very accurate and practical.
                                        </div>
                                        <div class="user-info">
                                            <div class="user-avatar">RS</div>
                                            <div>Rahul Sharma, Pune</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="testimonial-card">
                                        <div class="testimonial-text">
                                           I had concerns regarding my marriage, and the guidance I received was very helpful. The remedies were simple and effective, and things have improved a lot.
                                        </div>
                                        <div class="user-info">
                                            <div class="user-avatar">AM</div>
                                            <div>Anjali Mehta, Mumbai</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4 d-none d-lg-block">
                                    <div class="testimonial-card">
                                        <div class="testimonial-text">
                                            I consulted for my business issues, and I was impressed with the accuracy of analysis. The suggestions helped me take better financial decisions.
                                        </div>
                                        <div class="user-info">
                                            <div class="user-avatar">PS</div>
                                            <div>Prakash Singh, Delhi</div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <!-- Slide 2 -->
                        <div class="carousel-item">
                            <div class="row g-4">
                                <div class="col-md-6 col-lg-4">
                                    <div class="testimonial-card">
                                        <div class="testimonial-text">
                                            The best astrologer in this platform with whom i have spoken.
                                            You have not only shown the path but also added the motivation
                                        </div>
                                        <div class="user-info">
                                            <div class="user-avatar">R</div>
                                            <div>R K.</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="testimonial-card">
                                        <div class="testimonial-text">
                                            The best astrologer in this platform with whom i have spoken.
                                            You have not only shown the path but also added the motivation
                                        </div>
                                        <div class="user-info">
                                            <div class="user-avatar">A</div>
                                            <div>A M.</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4 d-none d-lg-block">
                                    <div class="testimonial-card">
                                        <div class="testimonial-text">
                                            The best astrologer in this platform with whom i have spoken.
                                            You have not only shown the path but also added the motivation
                                        </div>
                                        <div class="user-info">
                                            <div class="user-avatar">P</div>
                                            <div>P S.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="faq-section">
            <div class="container">
                <div class="faq-header">
                    <h2>Frequently Asked Questions</h2>
                </div>
                <div class="faq-accordion">
                    <!-- FAQ -->
                    <div class="faq-item active">
                        <button class="faq-question" type="button" onclick="this.parentElement.classList.toggle('active')">
                            What is Astrology Consultation?
                            <span class="faq-icon">+</span>
                        </button>
                        <div class="faq-answer">
                            <p>
                                Astrology consultation is a personalized guidance session where an astrologer
                                analyzes your birth chart (Kundli) based on your date, time, and place of birth.
                                It helps understand your life path, career opportunities, relationships,
                                financial growth, and challenges.
                            </p>
                        </div>
                    </div>
                    <!-- FAQ -->
                    <div class="faq-item">
                        <button class="faq-question" type="button" onclick="this.parentElement.classList.toggle('active')">
                            What details are required for astrology reading?
                            <span class="faq-icon">+</span>
                        </button>
                        <div class="faq-answer">
                            <p>
                                For accurate predictions, you need to provide your date of birth,
                                exact birth time, and birth location. These details help astrologers
                                create your horoscope chart and provide precise guidance.
                            </p>
                        </div>
                    </div>
                    <!-- FAQ -->
                    <div class="faq-item">
                        <button class="faq-question" type="button" onclick="this.parentElement.classList.toggle('active')">
                            Can astrology help with career and marriage problems?
                            <span class="faq-icon">+</span>
                        </button>
                        <div class="faq-answer">
                            <p>
                                Yes, astrology can provide insights into career growth, job changes,
                                business success, marriage compatibility, and relationship issues.
                                Remedies such as gemstones, mantras, or rituals may also be suggested.
                            </p>
                        </div>
                    </div>
                    <!-- FAQ -->
                    <div class="faq-item">
                        <button class="faq-question" type="button" onclick="this.parentElement.classList.toggle('active')">
                            How can I book an astrology consultation?
                            <span class="faq-icon">+</span>
                        </button>
                        <div class="faq-answer">
                            <p>
                                You can easily book a consultation through our website by selecting
                                your preferred astrologer, choosing a time slot, and completing the
                                payment. Sessions are available via phone, video call, or chat.
                            </p>
                        </div>
                    </div>
                    <!-- FAQ -->
                    <div class="faq-item">
                        <button class="faq-question" type="button" onclick="this.parentElement.classList.toggle('active')">
                            Are astrology remedies really effective?
                            <span class="faq-icon">+</span>
                        </button>
                        <div class="faq-answer">
                            <p>
                                Astrology remedies are based on planetary influences and traditional
                                Vedic knowledge. When followed correctly with faith, remedies like
                                gemstones, mantras, donations, and rituals may help reduce obstacles
                                and improve positive energies in life.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    </div>
@endsection
