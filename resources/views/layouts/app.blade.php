<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', config('app.name'))</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/fab.png') }}">
    <!-- Style CSS -->
         <link href="{{ asset('assets/css/style2.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @stack('head')
    @php
        $globalLiveConsultationData = [
            'enabled' => (bool) request()->cookie('auth_api_token') && !in_array('Astrologer', session('auth.roles', []), true),
            'userId' => session('api_user_id') ?? data_get(session('auth.user', []), 'id'),
            'iconUrl' => asset('assets/images/Logo.png'),
            'currentUrl' => url()->current(),
        ];
    @endphp
    <style>
        .global-live-consultation-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.52);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1080;
            padding: 1rem;
        }

        .global-live-consultation-card {
            width: 100%;
            max-width: 430px;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.22);
            padding: 1.5rem;
            text-align: center;
        }
    </style>
</head>

<body>
    @include('partials.header')

        @yield('content')

    <div id="global-live-consultation-popup" class="global-live-consultation-backdrop">
        <div class="global-live-consultation-card">
            <div class="mb-3" style="font-size:2rem;color:#198754;"><i class="fa-solid fa-bell"></i></div>
            <h4 class="mb-2" id="global-live-consultation-title">Consultation Is Live</h4>
            <p class="text-muted mb-4" id="global-live-consultation-message">Your astrologer has started the consultation.</p>
            <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
                <button type="button" id="global-live-consultation-dismiss" class="btn btn-outline-secondary">Later</button>
                <button type="button" id="global-live-consultation-join" class="btn btn-success">
                    <i class="fa-solid fa-video me-1"></i> Join Now
                </button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script id="global-live-consultation-data" type="application/json">{!! json_encode($globalLiveConsultationData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
    @include('partials.footer')
    @vite(['resources/js/app.js'])
    @stack('scripts')
</body>

</html>
