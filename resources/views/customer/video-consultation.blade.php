@extends('layouts.app')

@section('content')
@php
    $resolvedMeetingId = $meetingId ?? request()->get('meeting_id');
    $resolvedBookingId = $resolvedMeetingId ? str_replace('astro-', '', $resolvedMeetingId) : null;
    $customerVideoConsultationPageData = [
        'meetingId' => $resolvedMeetingId,
        'bookingDetailsUrl' => $resolvedBookingId ? route('booking.details', ['id' => $resolvedBookingId]) : route('my-bookings'),
        'apiKey' => config('services.videosdk.api_key'),
        'participantName' => 'Customer',
    ];
@endphp
<style>
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        background: #181c24;
        overflow: hidden;
    }
    .video-consultation-fullscreen {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: #181c24;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        align-items: stretch;
        justify-content: flex-start;
        padding: 0;
        margin: 0;
        color: #fff;
        font-family: 'Segoe UI', Arial, sans-serif;
        overflow: hidden;
    }
    .video-consultation-header {
        width: 100vw;
        min-height: 56px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 20px 0 20px;
        box-sizing: border-box;
        flex-shrink: 0;
        display: none;
    }
    .video-consultation-title {
        font-size: 1.5rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .video-consultation-close {
        background: rgba(0,0,0,0.4);
        border: none;
        color: #fff;
        font-size: 2rem;
        border-radius: 50%;
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.2s;
    }
    .video-consultation-close:hover {
        background: #ff4d4f;
        color: #fff;
    }
    #session-status-alert {
        flex-shrink: 0;
        margin-bottom: 6px;
        font-size: 1rem;
        padding: 8px 12px;
        display: none;
    }
    .video-consultation-controls {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 12px;
        margin-bottom: 8px;
        width: 100vw;
        max-width: 600px;
        flex-shrink: 0;
        display: none;
    }
    #customerJoinMeetingBtn, #customerRefreshStatusBtn {
        min-width: 220px;
        font-size: 1.15rem;
        font-weight: 500;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.10);
        padding: 12px 0;
    }
    #customerJoinMeetingBtn {
        background: linear-gradient(90deg, #00c853 0%, #43e97b 100%);
        border: none;
        color: #fff;
    }
    #customerJoinMeetingBtn:hover {
        background: linear-gradient(90deg, #43e97b 0%, #00c853 100%);
        color: #fff;
    }
    #customerRefreshStatusBtn {
        background: #fff;
        color: #007bff;
        border: 1.5px solid #007bff;
    }
    #customerRefreshStatusBtn:hover {
        background: #e3f2fd;
        color: #0056b3;
    }
    #customer-videosdk-meeting-root {
        flex: 1 1 0%;
        width: 100vw !important;
        min-height: 0 !important;
        height: 100vh !important;
        max-width: 100vw !important;
        max-height: 100vh !important;
        margin: 0 auto;
        background: #000;
        border-radius: 0;
        box-shadow: none;
        overflow: hidden;
        display: flex;
    }
</style>
<div class="video-consultation-fullscreen">
        <div class="video-consultation-header">
                <div class="video-consultation-title">
                        <i class="fa-solid fa-video"></i> Join Your Video Consultation
                </div>
                <button class="video-consultation-close" id="videoConsultationCloseBtn" title="Close Consultation" onclick="window.location.href='/'">
                        <i class="fa-solid fa-xmark"></i>
                </button>
        </div>
        <div class="video-consultation-controls">
                <button id="customerJoinMeetingBtn" class="btn btn-success btn-lg">
                        <i class="fa-solid fa-video me-1"></i> Join Video Call
                </button>
                <button id="customerRefreshStatusBtn" class="btn btn-outline-primary btn-lg" type="button">
                        <i class="fa-solid fa-rotate-right me-1"></i> Refresh Status
                </button>
        </div>
        <div id="session-status-alert" class="alert alert-info" style="max-width:600px;">
                Please wait for the astrologer to start the video call. Once started, you can join using the button below.
        </div>
        <div id="customer-videosdk-meeting-root" style="display:none;"></div>
        <div id="customer-completed-state" class="alert alert-success w-100 mt-3" style="display:none;max-width:600px;">
                <div class="fw-bold mb-1"><i class="fa-solid fa-circle-check me-1"></i> Consultation ended</div>
                <div>This consultation has been completed. The meeting room is now closed.</div>
        </div>
</div>
<script>
// Hide header, nav, and footer for immersive fullscreen
document.addEventListener('DOMContentLoaded', function() {
    var header = document.querySelector('.top-header');
    if(header) header.style.display = 'none';
    var nav = document.querySelector('nav.navbar');
    if(nav) nav.style.display = 'none';
    var footer = document.querySelector('footer');
    if(footer) footer.style.display = 'none';
});
</script>
<script src="https://sdk.videosdk.live/rtc-js-prebuilt/0.3.34/rtc-js-prebuilt.js"></script>
<script id="customer-video-consultation-page-data" type="application/json">{!! json_encode($customerVideoConsultationPageData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const pageData = JSON.parse(document.getElementById('customer-video-consultation-page-data').textContent);
    const joinBtn = document.getElementById('customerJoinMeetingBtn');
    const refreshStatusBtn = document.getElementById('customerRefreshStatusBtn');
    const meetingRoot = document.getElementById('customer-videosdk-meeting-root');
    const completedState = document.getElementById('customer-completed-state');
    const meetingId = pageData.meetingId;
    const appointmentId = meetingId.replace('astro-', '');
    const bookingDetailsUrl = pageData.bookingDetailsUrl;
    const apiKey = pageData.apiKey;
    const name = pageData.participantName;
    const statusAlert = document.getElementById('session-status-alert');
    let meetingInitialized = false;
    let autoJoinAttempted = false;
    let completedRedirectTimeout = null;

    function setButtonLoading(button, isLoading, loadingText, defaultHtml) {
        if (!button) return;

        if (!button.dataset.defaultHtml) {
            button.dataset.defaultHtml = defaultHtml || button.innerHTML;
        }

        if (isLoading) {
            button.disabled = true;
            button.innerHTML = loadingText;
        } else {
            button.disabled = false;
            button.innerHTML = button.dataset.defaultHtml;
        }
    }

    function renderCompletedState() {
        if (completedRedirectTimeout) {
            clearTimeout(completedRedirectTimeout);
        }

        joinBtn.style.display = 'none';
        joinBtn.disabled = true;
        refreshStatusBtn.style.display = 'none';
        meetingRoot.style.display = 'none';
        completedState.style.display = 'block';
        statusAlert.className = 'alert alert-warning';
        statusAlert.textContent = 'This session has ended. Redirecting to your booking details page...';

        if (bookingDetailsUrl) {
            completedRedirectTimeout = window.setTimeout(function() {
                window.location.href = bookingDetailsUrl;
            }, 3000);
        }
    }

    function initMeeting() {
        if (meetingInitialized) {
            return;
        }
        meetingInitialized = true;
        joinBtn.style.display = 'none';
        setButtonLoading(joinBtn, false);
        completedState.style.display = 'none';
        meetingRoot.style.display = 'block';

        if (typeof window.VideoSDKMeeting !== 'function') {
            alert('VideoSDKMeeting is not loaded. Please check your internet connection.');
            meetingInitialized = false;
            autoJoinAttempted = false;
            joinBtn.style.display = 'inline-block';
            setButtonLoading(joinBtn, false);
            meetingRoot.style.display = 'none';
            return;
        }

        try {
            new window.VideoSDKMeeting().init({
                name: name,
                meetingId: meetingId,
                apiKey: apiKey,
                containerId: 'customer-videosdk-meeting-root',
                micEnabled: true,
                webcamEnabled: true,
                participantCanToggleSelfWebcam: true,
                participantCanToggleSelfMic: true,
                chatEnabled: true,
                screenShareEnabled: true,
                joinScreen: {
                    visible: false,
                    title: 'Join Consultation',
                    meetingUrl: window.location.href
                },
                joinWithoutUserInteraction: true,
                participantCanLeave: true,
                participantCanEndMeeting: false,
                redirectOnLeave: window.location.href,
                notificationSoundEnabled: true,
                layout: 'GRID',
            });
        } catch (e) {
            meetingInitialized = false;
            autoJoinAttempted = false;
            joinBtn.style.display = 'inline-block';
            setButtonLoading(joinBtn, false);
            meetingRoot.style.display = 'none';
            alert('Failed to initialize video meeting: ' + e.message);
        }
    }

    function attemptAutoJoin() {
        if (meetingInitialized || autoJoinAttempted) {
            return;
        }

        autoJoinAttempted = true;
        joinBtn.style.display = 'none';
        setButtonLoading(joinBtn, true, '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Joining');
        statusAlert.className = 'alert alert-success';
        statusAlert.textContent = 'Session is live. Connecting you now...';
        initMeeting();
    }

    // Fetch session status from API
    function fetchStatus(showLoading) {
        if (showLoading) {
            setButtonLoading(refreshStatusBtn, true, '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Refreshing');
        }

        return fetch('/astrologer/appointments/' + appointmentId + '/ajax-status', {
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.status) {
                if (data.status === 'in_progress') {
                    joinBtn.disabled = false;
                    refreshStatusBtn.style.display = 'inline-block';
                    if (meetingInitialized) {
                        joinBtn.style.display = 'none';
                        statusAlert.className = 'alert alert-success';
                        statusAlert.textContent = 'Session is live.';
                    } else {
                        attemptAutoJoin();
                    }
                    completedState.style.display = 'none';
                } else if (data.status === 'completed') {
                    renderCompletedState();
                } else {
                    autoJoinAttempted = false;
                    joinBtn.disabled = true;
                    joinBtn.style.display = 'none';
                    setButtonLoading(joinBtn, false);
                    refreshStatusBtn.style.display = 'inline-block';
                    completedState.style.display = 'none';
                    meetingRoot.style.display = 'none';
                    statusAlert.className = 'alert alert-info';
                    statusAlert.textContent = 'Please wait for the astrologer to start the video call.';
                }
            }
        })
        .finally(() => {
            if (showLoading) {
                setButtonLoading(refreshStatusBtn, false);
            }
        });
    }
    joinBtn.style.display = 'none';
    fetchStatus();
    setInterval(fetchStatus, 10000); // Poll every 10s
    joinBtn.addEventListener('click', function() {
        autoJoinAttempted = true;
        setButtonLoading(joinBtn, true, '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Joining');
        initMeeting();
    });
    refreshStatusBtn.addEventListener('click', function() {
        fetchStatus(true);
    });
});
</script>
@endsection
