@extends('layouts.app')

@section('content')
<style>
    .booking-header-custom {
        background: linear-gradient(90deg, #f98700 70%, #fbbf24 100%);
        color: #fff;
        border-radius: 14px 14px 0 0;
        padding: 1.5rem 2rem 1.2rem 2rem;
        margin-bottom: 0;
        position: relative;
        box-shadow: 0 2px 12px rgba(249, 135, 0, 0.08);
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
    }

    .booking-status-badge {
        position: absolute;
        top: 1.5rem;
        right: 2rem;
        background: #fff;
        color: #f98700;
        font-weight: 700;
        border-radius: 8px;
        padding: 0.35em 1.2em;
        font-size: 1em;
        box-shadow: 0 1px 4px rgba(249, 135, 0, 0.08);
        letter-spacing: 0.01em;
    }

    .booking-status-badge.pending {
        background: #ffc107;
        color: #333;
    }

    .booking-section {
        background: #fff;
        border-radius: 0 0 14px 14px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        padding: 2.2rem 2rem 1.7rem 2rem;
        margin-bottom: 1.5rem;
    }

    .booking-details-row {
        display: flex;
        gap: 2.2rem;
        margin-bottom: 1.7rem;
        flex-wrap: wrap;
    }

    .booking-details-col {
        flex: 1 1 320px;
        background: #f7fafc;
        border-radius: 10px;
        padding: 1.3rem 1.3rem 1.1rem 1.3rem;
        min-width: 260px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03);
    }

    .booking-details-label {
        font-weight: 700;
        color: #f98700;
        font-size: 1.08rem;
        margin-bottom: 0.5rem;
        letter-spacing: 0.01em;
        display: flex;
        align-items: center;
        gap: 0.4em;
    }

    .booking-details-value {
        color: #222;
        margin-bottom: 0.3rem;
        font-size: 1.01rem;
    }

    .booking-table-summary th,
    .booking-table-summary td {
        text-align: left;
        padding: 0.6rem 1.2rem;
        border: 1px solid #e0e0e0;
    }

    .booking-table-summary th {
        background: #f9f5ef;
        font-weight: 700;
        color: #f98700;
        font-size: 1.01rem;
    }

    .booking-table-summary {
        width: 100%;
        margin-bottom: 1.7rem;
        /* border-radius: 10px; */
        overflow: hidden;
        border-collapse: separate;
        border-spacing: 0;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03);
    }

    .booking-actions {
        display: flex;
        gap: 1rem;
        margin-top: 1.7rem;
        justify-content: flex-end;
    }

    .btn-theme-orange {
        background: linear-gradient(90deg, #f98700 60%, #fbbf24 100%);
        color: #fff;
        border: none;
        font-weight: 600;
        letter-spacing: 0.01em;
        box-shadow: 0 1px 4px rgba(249, 135, 0, 0.08);
    }

    .btn-theme-orange:hover,
    .btn-theme-orange:focus {
        background: #d97706;
        color: #fff;
    }
</style>

<!-- Video Consultation Modal -->
<div class="modal fade" id="astrologerVideoConsultationModal" tabindex="-1" aria-labelledby="astrologerVideoConsultationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="astrologerVideoConsultationModalLabel">Video Consultation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="min-height: 600px;">
                <div id="astrologer-videosdk-meeting-root" style="width:100%;height:600px;"></div>
            </div>
        </div>
    </div>
</div>

@php
// Video call state and meeting ID setup (ensure this is at the top of the file)
$videoCallStarted = session('video_call_started') || ($appointment['video_call_started'] ?? false);
$meetingId = 'astro-' . $appointment['id'];
@endphp

<!-- <div class="mb-3">
    <button id="astrologerJoinMeetingBtn" class="btn btn-primary" aria-haspopup="dialog" aria-controls="astrologerVideoConsultationModal">
        <i class="fa-solid fa-video me-1"></i> Start Video Call
    </button>
</div> -->



<!-- @if($videoCallStarted)
    <div class="mb-3">
       
        <div class="alert alert-info mt-2">
            Share this link with the customer to join: <a href="https://app.videosdk.live/rooms/{{ $meetingId }}" target="_blank">https://app.videosdk.live/rooms/{{ $meetingId }}</a>
        </div>
    </div>
@endif -->
<!-- <a href="{{ route('astrologer.appointment.video', ['id' => $appointment['id']]) }}" target="_blank" class="btn btn-primary" aria-haspopup="dialog">
            <i class="fa-solid fa-video me-1"></i> Join Video Consultation
        </a> -->
<!-- Video Consultation Modal -->
<!-- <div class="modal fade" id="astrologerVideoConsultationModal" tabindex="-1" aria-labelledby="astrologerVideoConsultationModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="astrologerVideoConsultationModalLabel">Video Consultation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0" style="min-height: 600px;">
        <div id="astrologer-videosdk-meeting-root" style="width:100%;height:600px;"></div>
      </div>
    </div>
  </div>
</div> -->

@if($videoCallStarted)
<div class="mb-3">
    <a href="{{ route('astrologer.appointment.video', ['id' => $appointment['id']]) }}" target="_blank" class="btn btn-primary" aria-haspopup="dialog">
        <i class="fa-solid fa-video me-1"></i> Join Video Consultation
    </a>
    <div class="alert alert-info mt-2">
        <div><b>Customer Join Link:</b></div>
        <div class="input-group mb-2" style="max-width: 500px;">
            <input type="text" class="form-control" id="customerJoinLink" value="{{ route('customer.consultation.video', ['meetingId' => $meetingId]) }}" readonly>
            <button class="btn btn-outline-secondary" type="button" onclick="navigator.clipboard.writeText(document.getElementById('customerJoinLink').value)"><i class="fa-regular fa-copy"></i> Copy</button>
        </div>
        <small>Share this link with the customer. They will join the video consultation in a branded, secure page—no app install required.</small>
        @if(!empty($appointment['customer_email']))
        <form method="POST" action="{{ route('astrologer.appointment.sendLink', ['id' => $appointment['id']]) }}" class="mt-2">
            @csrf
            <button type="submit" class="btn btn-outline-success">
                <i class="fa-solid fa-envelope me-1"></i> Email Link to Customer
            </button>
        </form>
        @endif
    </div>
</div>
@endif

<div class="container" style="max-width: 900px; margin: 40px auto;">
    @if(isset($appointment))
    <div class="booking-header-custom">
        <div style="font-size:1.45rem;font-weight:700;letter-spacing:0.01em;"><i class="fa-solid fa-calendar-check me-2"></i>Appointment Details</div>
        <div style="font-size:1.01rem;opacity:0.95;">
            Booking ID : <b id="bookingId">BKNG{{ $appointment['id'] }}</b>
            <button class="btn btn-sm btn-outline-light ms-2 py-0 px-2" style="font-size:0.95em;vertical-align:middle;" onclick="navigator.clipboard.writeText('BKNG{{ $appointment['id'] }}')"><i class="fa-regular fa-copy"></i></button>
        </div>
        <span class="booking-status-badge {{ $appointment['status'] }}">
            <i class="fa-solid {{ $appointment['status'] === 'confirmed' ? 'fa-circle-check text-success' : ($appointment['status'] === 'pending' ? 'fa-hourglass-half text-warning' : 'fa-circle-xmark text-danger') }} me-1"></i>
            {{ ucfirst($appointment['status']) }}
        </span>
    </div>
    <div class="booking-section">
        <div class="booking-details-row">
            <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                <a href="{{ route('astrologer.appointments') }}" class="btn btn-light border">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Appointments
                </a>
                <!-- <form method="POST" action="{{ route('astrologer.appointment.start', ['id' => $appointment['id']]) }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="fa-solid fa-play me-1"></i> Start Consultation
                            </button>
                        </form> -->
                <!-- <form method="POST" action="{{ route('astrologer.appointment.startVideo', ['id' => $appointment['id']]) }}" style="display:inline;">
                            @csrf -->
                <!-- <button id="astrologerJoinMeetingBtn" class="btn btn-primary" aria-haspopup="dialog" aria-controls="astrologerVideoConsultationModal">
        <i class="fa-solid fa-video me-1"></i> Start Video Call
    </button> -->

                <!-- </form> -->
                <!-- <a href="#suggest-products" class="btn btn-warning">
                            <i class="fa-solid fa-gift me-1"></i> Suggest Products
                        </a> -->
                <form method="POST" action="{{ route('astrologer.appointment.cancel', ['id' => $appointment['id']]) }}" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Are you sure you want to cancel this appointment?');" title="Cancel this appointment">
                        <i class="fa-solid fa-xmark me-1"></i> Cancel Appointment
                    </button>
                </form>
                @if(($appointment['status'] ?? '') !== 'completed')
                    <a href="{{ route('astrologer.appointment.video', ['id' => $appointment['id']]) }}" target="_blank" class="btn btn-primary float-end" aria-haspopup="dialog">
                        <i class="fa-solid fa-video me-1"></i> Start Video Consultation
                    </a>
                @else
                    <span class="alert alert-info mb-0" style="font-size:1em;display:inline-block;vertical-align:middle;">This appointment is completed. Video consultation is no longer available.</span>
                @endif
            </div>


            <table class="booking-table-summary">
                <thead>
                    <tr>
                        <th>Consultation Type</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Duration</th>
                        <th>Rate</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ ucfirst($appointment['consultation_type'] ?? 'Consultation') }}</td>
                        <td>{{ \Carbon\Carbon::parse($appointment['scheduled_at'])->format('d F Y') }}</td>
                        <td>
                            {{ \Carbon\Carbon::parse($appointment['scheduled_at'])->format('h:i A') }}
                            @if(isset($appointment['end_time']))
                            - {{ \Carbon\Carbon::parse($appointment['end_time'])->format('h:i A') }}
                            @endif
                        </td>
                        <td>
                            @if(isset($appointment['duration']) && is_numeric($appointment['duration']))
                            @php
                            $duration = intval($appointment['duration']);
                            $hours = intdiv($duration, 60);
                            $minutes = $duration % 60;
                            @endphp
                            @if($hours > 0)
                            {{ $hours }} hr{{ $hours > 1 ? 's' : '' }}
                            @endif
                            @if($minutes > 0)
                            {{ $hours > 0 ? ' ' : '' }}{{ $minutes }} min
                            @endif
                            @if($hours == 0 && $minutes == 0)
                            0 min
                            @endif
                            @else
                            -
                            @endif
                        </td>
                        <td>₹{{ $appointment['rate'] ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="booking-details-row" style="margin-bottom:0;">
            <div class="booking-details-col">
                <div class="booking-details-label mb-2"><i class="fa-solid fa-credit-card me-1"></i> Payment Info</div>
                <div class="booking-details-value"><b>Payment Method :</b> {{ ucfirst($appointment['payment_method'] ?? '-') }}</div>
                <div class="booking-details-value"><b>Transaction ID :</b> {{ $appointment['razorpay_payment_id'] ?? $appointment['transaction_id'] ?? '-' }}</div>
                <div class="booking-details-value"><b>Status :</b> <span style="color:#219150;font-weight:600;">Paid</span></div>
            </div>
            <div class="booking-details-col">
                <div class="booking-details-label mb-2"><i class="fa-solid fa-user me-1"></i> Customer Details</div>
                @if(isset($appointment['user']) && is_array($appointment['user']))
                    <div class="booking-details-value"><b>Name:</b> {{ trim(($appointment['user']['first_name'] ?? '') . ' ' . ($appointment['user']['last_name'] ?? '')) }}</div>
                    <div class="booking-details-value"><b>Email:</b> {{ $appointment['user']['email'] ?? '-' }}</div>
                    <div class="booking-details-value"><b>Mobile:</b> {{ $appointment['user']['mobile_no'] ?? '-' }}</div>
                    <div class="booking-details-value"><b>City:</b> {{ $appointment['user']['city'] ?? '-' }}</div>
                    <div class="booking-details-value"><b>User Code:</b> {{ $appointment['user']['user_code'] ?? '-' }}</div>
                @else
                    <div class="booking-details-value">No customer details found.</div>
                @endif
            </div>
            <div class="flex-grow-1 d-flex flex-column align-items-end justify-content-between">
                <div class="booking-details-label mb-2">Total Amount</div>
                <div class="booking-details-value" style="font-size:1.5rem;font-weight:700;color:#219150;">₹{{ $appointment['rate'] ?? '-' }}</div>
            </div>
        </div>

    </div>
    @else
    <div class="booking-section">
        <div class="alert alert-warning mt-3">Appointment details not found.</div>
    </div>
    @endif
</div>

@if(session('success'))
<div class="alert alert-success mt-3">
    {!! session('success') !!}
</div>
@endif
@if(session('error'))
<div class="alert alert-danger mt-3">
    {{ session('error') }}
</div>
@endif

@endsection