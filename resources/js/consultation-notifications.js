import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

const PAGE_DATA_ID = 'global-live-consultation-data';
const LAST_NOTIFICATION_KEY = 'globalLiveConsultationLastNotification';
const sharedEcho = initializeEchoClient();

document.addEventListener('DOMContentLoaded', () => {
    const pageDataElement = document.getElementById(PAGE_DATA_ID);

    if (!pageDataElement) {
        return;
    }

    const pageData = JSON.parse(pageDataElement.textContent || '{}');

    if (!pageData.enabled || !pageData.userId) {
        return;
    }

    const popup = document.getElementById('global-live-consultation-popup');
    const joinButton = document.getElementById('global-live-consultation-join');
    const dismissButton = document.getElementById('global-live-consultation-dismiss');
    const titleElement = document.getElementById('global-live-consultation-title');
    const messageElement = document.getElementById('global-live-consultation-message');

    let activeBooking = null;
    let dismissedNotificationKey = null;

    ensureNotificationPermission();

    const echo = sharedEcho;

    if (echo) {
        subscribeToConsultationChannel(echo, pageData.userId, pageData, handleConsultationUpdate);
    }

    if (joinButton) {
        joinButton.addEventListener('click', () => redirectToJoinUrl(activeBooking));
    }

    if (dismissButton) {
        dismissButton.addEventListener('click', () => {
            if (activeBooking) {
                dismissedNotificationKey = notificationKey(activeBooking);
            }

            hidePopup();
        });
    }

    function ensureNotificationPermission() {
        if (!('Notification' in window)) {
            return;
        }

        if (Notification.permission === 'default') {
            Notification.requestPermission().catch(() => {});
        }
    }

    function handleConsultationUpdate(payload) {
        const booking = normalizeBookingPayload(payload, pageData);

        if (!booking) {
            return;
        }

        dispatchConsultationStatusUpdated(booking);

        if (booking.status === 'ended') {
            if (activeBooking && activeBooking.bookingId === booking.bookingId) {
                activeBooking = null;
            }

            dismissedNotificationKey = null;
            hidePopup();
            return;
        }

        activeBooking = booking;

        if (isCurrentLocation(booking.joinUrl)) {
            hidePopup();
            return;
        }

        const currentNotificationKey = notificationKey(booking);

        if (dismissedNotificationKey === currentNotificationKey) {
            return;
        }

        if (localStorage.getItem(LAST_NOTIFICATION_KEY) === currentNotificationKey) {
            return;
        }

        localStorage.setItem(LAST_NOTIFICATION_KEY, currentNotificationKey);
        showPopup(booking);
        notifyBrowser(booking, pageData.iconUrl);
    }

    function hidePopup() {
        if (popup) {
            popup.style.display = 'none';
        }
    }

    function showPopup(booking) {
        if (!popup) {
            return;
        }

        if (titleElement) {
            titleElement.textContent = booking.status === 'ready_to_start'
                ? 'Consultation Is Ready'
                : 'Consultation Is Live';
        }

        if (messageElement) {
            messageElement.textContent = booking.status === 'ready_to_start'
                ? `Astrologer ${booking.astrologerName} is ready. Join now to start the consultation.`
                : `Astrologer ${booking.astrologerName} has started the consultation. Join now.`;
        }

        popup.style.display = 'flex';
    }

    function notifyBrowser(booking, iconUrl) {
        if (!booking || !('Notification' in window) || Notification.permission !== 'granted') {
            return;
        }

        const notification = new Notification(
            booking.status === 'ready_to_start' ? 'Consultation is ready' : 'Consultation is live',
            {
                body: booking.status === 'ready_to_start'
                    ? `Astrologer ${booking.astrologerName} is ready. Click to join now.`
                    : `Astrologer ${booking.astrologerName} has started the consultation. Click to join now.`,
                icon: iconUrl,
                tag: `global-live-consultation-${booking.bookingId}-${booking.status}`,
            },
        );

        notification.onclick = () => {
            window.focus();
            redirectToJoinUrl(booking);
            notification.close();
        };
    }
});

function initializeEchoClient() {
    if (window.Echo) {
        return window.Echo;
    }

    const appKey = import.meta.env.VITE_REVERB_APP_KEY || import.meta.env.VITE_PUSHER_APP_KEY;

    if (!appKey) {
        return null;
    }

    const scheme = String(import.meta.env.VITE_REVERB_SCHEME || import.meta.env.VITE_PUSHER_SCHEME || 'http').replace(':', '').toLowerCase();
    const host = import.meta.env.VITE_REVERB_HOST || import.meta.env.VITE_PUSHER_HOST || window.location.hostname;
    const port = Number(import.meta.env.VITE_REVERB_PORT || import.meta.env.VITE_PUSHER_PORT || 8080);
    const csrfToken = document.head.querySelector('meta[name="csrf-token"]')?.content;

    window.Pusher = Pusher;

    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: appKey,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1',
        wsHost: host,
        wsPort: port,
        wssPort: port,
        forceTLS: scheme === 'https',
        enabledTransports: ['ws', 'wss'],
        disableStats: true,
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {},
        },
    });

    return window.Echo;
}

function subscribeToConsultationChannel(echo, userId, pageData, handler) {
    echo.private(`consultation.user.${userId}`)
        .listen('.consultation.status.updated', (event) => {
            handler(event, pageData);
        });
}

function normalizeBookingPayload(payload, pageData) {
    const bookingId = Number(payload.bookingId || payload.id || 0);
    const status = normalizeStatus(payload.status);

    if (!bookingId || !status) {
        return null;
    }

    return {
        bookingId,
        status,
        astrologerName: payload.astrologerName || 'Your astrologer',
        joinUrl: payload.joinUrl || null,
        bookingDetailsUrl: payload.bookingDetailsUrl || null,
        currentUrl: pageData.currentUrl || window.location.href,
    };
}

function normalizeStatus(status) {
    const normalized = String(status || '').trim().toLowerCase();

    if (!normalized) {
        return null;
    }

    if (normalized === 'in_progress') {
        return 'live';
    }

    if (normalized === 'completed') {
        return 'ended';
    }

    return normalized;
}

function notificationKey(booking) {
    return `${booking.bookingId}:${booking.status}`;
}

function dispatchConsultationStatusUpdated(booking) {
    window.dispatchEvent(new CustomEvent('consultation-status.updated', {
        detail: booking,
    }));
}

function redirectToJoinUrl(booking) {
    if (booking?.joinUrl) {
        window.location.href = booking.joinUrl;
    }
}

function isCurrentLocation(joinUrl) {
    if (!joinUrl) {
        return false;
    }

    try {
        const currentUrl = new URL(window.location.href);
        const targetUrl = new URL(joinUrl, window.location.origin);

        return currentUrl.pathname === targetUrl.pathname;
    } catch {
        return window.location.href === joinUrl;
    }
}
