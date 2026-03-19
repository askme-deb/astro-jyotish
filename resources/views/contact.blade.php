@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')

<div class="container mt-4 inner_back">

    <div class="banner">

        <!-- Background Image -->
        <img src="{{ asset('assets/images/consult3.png') }}" alt="Astrology Banner">

        <!-- Overlay -->
        <div class="banner-overlay">

            <div class="banner-content">
                <h1>
                    Discover Your Destiny with <br>
                    Trusted <span>Astrologers</span>
                </h1>

                <a href="{{ url('/consultation') }}" class="appointment-btn">
                    Get an Appointment
                </a>
            </div>

        </div>

    </div>

</div>

<div class="container container-box">
    <!-- Heading -->
    <h2 class="title mb-5">Let’s Get In Touch</h2>
    <!-- Contact Info -->
    <div class="row mb-4 text-center">
        <div class="col-md-4 contact-info">
            <div class="icon-box">
                <i class="bi bi-telephone"></i>
            </div>
            <div>+91 90918 40899</div>
            {{-- <div>+91 98765 43211</div> --}}
        </div>
        <div class="col-md-4 contact-info">
            <div class="icon-box">
                <i class="bi bi-envelope"></i>
            </div>
            <div>contact@astrorajumaharaj.com</div>
            {{-- <div>support@email.com</div> --}}
        </div>
        <div class="col-md-4 contact-info">
            <div class="icon-box">
                <i class="bi bi-geo-alt"></i>
            </div>
            <div>Bagda, P.S.-Puncha, Dist. - Purulia, West Bengal, Pin - 723151</div>
            {{-- <div>Worldwide Online</div> --}}
        </div>
    </div>
    <div class="cfrom_warp">
        <h5 class="title mb-5">Or fill out the form below</h5>
        <!-- Form -->
        <form>
            <div class="row g-4">
                <!-- Name -->
                <div class="col-md-6">
                    <label>First Name</label>
                    <div class="input-icon">
                        <i class="bi bi-person"></i>
                        <input type="text" class="form-control" placeholder="Enter your Frist name">
                    </div>
                </div>
                <div class="col-md-6">
                    <label>Last Name</label>
                    <div class="input-icon">
                        <i class="bi bi-person"></i>
                        <input type="text" class="form-control" placeholder="Enter your Last name">
                    </div>
                </div>
                <!-- Email -->
                <div class="col-md-6">
                    <label>Email</label>
                    <div class="input-icon">
                        <i class="bi bi-envelope"></i>
                        <input type="email" class="form-control" placeholder="Enter email address">
                    </div>
                </div>
                <!-- Phone -->
                <div class="col-md-6">
                    <label>Phone Number</label>
                    <div class="input-icon">
                        <i class="bi bi-telephone"></i>
                        <input type="text" class="form-control" placeholder="Enter phone number">
                    </div>
                </div>
                <!-- Message -->
                <div class="col-12 bvcfz">
                    <label>Your Question</label>
                    <div class="input-icon">
                        <i class="bi bi-chat-dots"></i>
                        <textarea class="form-control" placeholder="Enter your message"></textarea>
                    </div>
                </div>
                <!-- Button -->
                <div class="col-12 text-end">
                    <button type="submit" class="submit-btn">
                        Submit Form <i class="bi bi-send ms-2"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="container map_warp">
    <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d14749.031769143541!2d88.3919015!3d22.4569392!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2sin!4v1771249442645!5m2!1sen!2sin" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
</div>

@endsection
