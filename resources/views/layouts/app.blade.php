<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', config('app.name'))</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/Logo.png') }}">
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
            'statusUrl' => route('customer.liveConsultationStatus'),
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script id="global-live-consultation-data" type="application/json">{!! json_encode($globalLiveConsultationData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const pageDataEl = document.getElementById('global-live-consultation-data');
        if (!pageDataEl) {
            return;
        }

        const pageData = JSON.parse(pageDataEl.textContent || '{}');
        if (!pageData.enabled || !pageData.statusUrl) {
            return;
        }

        const popup = document.getElementById('global-live-consultation-popup');
        const joinBtn = document.getElementById('global-live-consultation-join');
        const dismissBtn = document.getElementById('global-live-consultation-dismiss');
        const titleEl = document.getElementById('global-live-consultation-title');
        const messageEl = document.getElementById('global-live-consultation-message');
        let activeBooking = null;
        let dismissedBookingId = null;

        function ensureNotificationPermission() {
            if (!('Notification' in window)) {
                return;
            }

            if (Notification.permission === 'default') {
                Notification.requestPermission().catch(function() {
                    // Ignore notification permission errors.
                });
            }
        }

        function hidePopup() {
            if (popup) {
                popup.style.display = 'none';
            }
        }

        function showPopup(booking) {
            if (!popup || !booking) {
                return;
            }

            activeBooking = booking;
            if (titleEl) {
                titleEl.textContent = booking.status === 'ready_to_start'
                    ? 'Consultation Is Ready'
                    : 'Consultation Is Live';
            }
            if (messageEl) {
                messageEl.textContent = booking.status === 'ready_to_start'
                    ? 'Astrologer ' + (booking.astrologerName || 'Your astrologer') + ' is ready. Join now to start the consultation.'
                    : 'Astrologer ' + (booking.astrologerName || 'Your astrologer') + ' has started the consultation. Join now.';
            }
            popup.style.display = 'flex';
        }

        function notifyBrowser(booking) {
            if (!booking || !('Notification' in window) || Notification.permission !== 'granted') {
                return;
            }

            const notification = new Notification(
                booking.status === 'ready_to_start' ? 'Consultation is ready' : 'Consultation is live',
                {
                body: booking.status === 'ready_to_start'
                    ? 'Astrologer ' + (booking.astrologerName || 'Your astrologer') + ' is ready. Click to join now.'
                    : 'Astrologer ' + (booking.astrologerName || 'Your astrologer') + ' has started the consultation. Click to join now.',
                icon: pageData.iconUrl,
                tag: 'global-live-consultation-' + booking.bookingId,
                }
            );

            notification.onclick = function() {
                window.focus();
                if (booking.joinUrl) {
                    window.location.href = booking.joinUrl;
                }
                notification.close();
            };
        }

        function fetchLiveConsultationStatus() {
            return fetch(pageData.statusUrl, {
                headers: { 'Accept': 'application/json' }
            })
            .then(function(res) {
                return res.json();
            })
            .then(function(data) {
                if (!data || !data.success) {
                    return;
                }

                if (!data.active || !data.bookingId) {
                    activeBooking = null;
                    dismissedBookingId = null;
                    localStorage.removeItem('globalLiveConsultationNotifiedBooking');
                    hidePopup();
                    return;
                }

                const notifiedBookingId = localStorage.getItem('globalLiveConsultationNotifiedBooking');
                activeBooking = data;

                if (pageData.currentUrl === data.joinUrl) {
                    hidePopup();
                    return;
                }

                if (dismissedBookingId === data.bookingId) {
                    return;
                }

                if (String(notifiedBookingId) !== String(data.bookingId)) {
                    localStorage.setItem('globalLiveConsultationNotifiedBooking', String(data.bookingId));
                    showPopup(data);
                    notifyBrowser(data);
                }
            })
            .catch(function() {
                // Ignore transient global polling failures.
            });
        }

        if (joinBtn) {
            joinBtn.addEventListener('click', function() {
                if (activeBooking && activeBooking.joinUrl) {
                    window.location.href = activeBooking.joinUrl;
                }
            });
        }

        if (dismissBtn) {
            dismissBtn.addEventListener('click', function() {
                if (activeBooking && activeBooking.bookingId) {
                    dismissedBookingId = activeBooking.bookingId;
                }
                hidePopup();
            });
        }

        ensureNotificationPermission();
        fetchLiveConsultationStatus();
        window.setInterval(fetchLiveConsultationStatus, 10000);
    });
    </script>

    @include('partials.footer')
    {{-- Scripts are loaded via Vite --}}
    @stack('scripts')
</body>

</html>
