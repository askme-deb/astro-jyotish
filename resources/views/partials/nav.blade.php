<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="/"> <img src="{{ asset('assets/images/Logo.png') }}"> Astro Raju Maharaj</a>
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
            <a href="/consultation" class="btn btn-astro ms-3">Book Consultation</a>
        </div>
    </div>
</nav>