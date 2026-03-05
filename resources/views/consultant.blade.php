@extends('layouts.app')

@section('title', 'Consultant')

@section('content')


<div class="container mt-4 inner_back">

    <div class="banner">

        <!-- Background Image -->
        <img src="{{ asset('assets/images/consult.png') }}" alt="Astrology Banner">

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

<section class="sear_pat">
    <div class="container">
        <div class="row">

            <div class="col-lg-10 col-md-6">
                <div class="search_warp">
                    <div class="input-group">
                        <input type="text" id="searchInput" class="form-control search-input" placeholder="Search Astrologer...">
                        <button class="btn btn-primary search-btn" onclick="searchData()">Search</button>
                        <!-- <button class="btn btn-danger search-btn" onclick="clearSearch()">Clear</button> -->
                    </div>
                </div>
            </div>

            <div class="col-lg-2 col-md-6 mb-4">
                <div class="filter_warp">
                    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#filterModal">
                        Open Filters
                    </button>


                    <!-- Modal -->
                    <div class="modal fade" id="filterModal" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">

                                <div class="modal-body position-relative">

                                    <button class="close-btn" data-bs-dismiss="modal">&times;</button>

                                    <div class="filter-title">Filters</div>

                                    <div class="filter-wrapper">

                                        <!-- Left Tabs -->
                                        <div class="left-tab">
                                            <button class="tab-btn active" data-tab="expertise">Expertise</button>
                                            <button class="tab-btn" data-tab="languages">Languages</button>
                                            <button class="tab-btn" data-tab="rating">Rating</button>
                                            <button class="tab-btn" data-tab="experience">Experience</button>
                                            <button class="tab-btn" data-tab="price">Price</button>
                                        </div>

                                        <!-- Right Content -->
                                        <div class="right-tab">

                                            <div class="tab-content active" id="expertise">
                                                <div class="form-check"><input class="form-check-input" type="checkbox" value="Vedic"> <label class="form-check-label">Vedic</label></div>
                                                <div class="form-check"><input class="form-check-input" type="checkbox" value="Vastu"> <label class="form-check-label">Vastu</label></div>
                                                <div class="form-check"><input class="form-check-input" type="checkbox" value="Tarot"> <label class="form-check-label">Tarot</label></div>
                                                <div class="form-check"><input class="form-check-input" type="checkbox" value="Numerology"> <label class="form-check-label">Numerology</label></div>
                                                <div class="form-check"><input class="form-check-input" type="checkbox" value="Fengshui"> <label class="form-check-label">Fengshui</label></div>
                                            </div>

                                            <div class="tab-content d-none" id="languages">
                                                <div class="form-check"><input class="form-check-input" type="checkbox" value="English"> <label class="form-check-label">English</label></div>
                                                <div class="form-check"><input class="form-check-input" type="checkbox" value="Hindi"> <label class="form-check-label">Hindi</label></div>
                                                <div class="form-check"><input class="form-check-input" type="checkbox" value="Tamil"> <label class="form-check-label">Tamil</label></div>
                                            </div>

                                            <div class="tab-content d-none" id="rating">
                                                <div class="form-check"><input class="form-check-input" type="checkbox" value="5 Star"> <label class="form-check-label">5 Star</label></div>
                                                <div class="form-check"><input class="form-check-input" type="checkbox" value="4 Star"> <label class="form-check-label">4 Star & Above</label></div>
                                            </div>

                                            <div class="tab-content d-none" id="experience">
                                                <div class="form-check"><input class="form-check-input" type="checkbox" value="1-3 Years"> <label class="form-check-label">1-3 Years</label></div>
                                                <div class="form-check"><input class="form-check-input" type="checkbox" value="5+ Years"> <label class="form-check-label">5+ Years</label></div>
                                            </div>

                                            <div class="tab-content d-none" id="price">
                                                <div class="form-check"><input class="form-check-input" type="checkbox" value="Below 10"> <label class="form-check-label">Below ₹10/min</label></div>
                                                <div class="form-check"><input class="form-check-input" type="checkbox" value="10-30"> <label class="form-check-label">₹10 - ₹30/min</label></div>
                                            </div>

                                        </div>

                                    </div>

                                </div>

                                <!-- Footer Buttons -->
                                <div class="modal-footer-custom">
                                    <button class="btn btn-clear" id="clearFilters">Clear Filters</button>
                                    <button class="btn btn-apply" id="applyFilters">Apply</button>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>




<div class="container">

    <!-- Top Online Astrologers -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="section-title">Top Online Astrologers</div>
        <!-- <button class="view-all">View All</button> -->
    </div>

    <div class="row g-4">
        @if(!empty($astrologers) && is_array($astrologers))
        @foreach($astrologers as $astrologer)
        <!-- Card  -->
        <x-astrologer-card :astrologer="$astrologer" />

        @endforeach
        @else
        <div class="col-12">
            <div class="alert alert-warning text-center">No astrologers found.</div>
        </div>
        @endif

    </div>
</div>

<div class="container py-5 text-center">
    <nav>
        <ul class="pagination justify-content-center">
            <!-- Previous -->
            <li class="page-item disabled">
                <a class="page-link" href="#" tabindex="-1">Previous</a>
            </li>
            <!-- Page Numbers -->
            <li class="page-item active">
                <a class="page-link" href="#">1</a>
            </li>

            <li class="page-item">
                <a class="page-link" href="#">2</a>
            </li>

            <li class="page-item">
                <a class="page-link" href="#">3</a>
            </li>

            <li class="page-item">
                <a class="page-link" href="#">4</a>
            </li>

            <li class="page-item">
                <a class="page-link" href="#">5</a>
            </li>

            <!-- Next -->
            <li class="page-item">
                <a class="page-link" href="#">Next</a>
            </li>

        </ul>
    </nav>
</div>


@endsection