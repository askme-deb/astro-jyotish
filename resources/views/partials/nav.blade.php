<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="/"> <img src="{{ asset('assets/images/Logo.png') }}"> Astrologer Raju Maharaj</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('consultant') }}">Astrologers</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Horoscope</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('contact') }}">Contact</a></li>
            </ul>
             @if(session()->has('auth.api_token'))
            <a href="/consultation" class="btn btn-astro ms-3">Book Consultation</a>
            @else
            <a href="javascripr:;" class="btn btn-astro ms-3"  data-bs-toggle="modal" data-bs-target="#authModal">Book Consultation</a>
            @endif
        </div>
    </div>
</nav>
