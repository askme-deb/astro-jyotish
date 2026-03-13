@extends('layouts.app')

@section('content')
@php
dd($appointment);
    $rescheduleBlockedStatuses = config('booking.reschedule_blocked_statuses', []);
    $isRescheduleDisabled = isset($appointment) && in_array($appointment['status'] ?? null, $rescheduleBlockedStatuses, true);
    $isNoteFinalized = isset($appointment)
        && ((bool) ($appointment['final_confirmation_from_astrologer'] ?? false)
        || (($appointment['astrologer_note_status'] ?? null) === 'finalized'));
    $appointmentRoot = isset($appointment) && is_array($appointment) ? $appointment : [];
    $astrologerSources = array_values(array_filter([
        is_array(data_get($appointmentRoot, 'astrologer')) ? data_get($appointmentRoot, 'astrologer') : null,
        is_array(data_get($appointmentRoot, 'assigned_astrologer')) ? data_get($appointmentRoot, 'assigned_astrologer') : null,
        is_array(data_get($appointmentRoot, 'consultant')) ? data_get($appointmentRoot, 'consultant') : null,
        is_array(data_get($appointmentRoot, 'astrologer.user')) ? data_get($appointmentRoot, 'astrologer.user') : null,
        is_array(data_get($appointmentRoot, 'assigned_astrologer.user')) ? data_get($appointmentRoot, 'assigned_astrologer.user') : null,
    ], function ($source) {
        return is_array($source) && $source !== [];
    }));
    $resolveRootValue = function (array $paths, $default = null) use ($appointmentRoot) {
        foreach ($paths as $path) {
            $value = data_get($appointmentRoot, $path);

            if (is_string($value)) {
                $value = trim($value);
            }

            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return $default;
    };
    $resolveAstrologerValue = function (array $paths, $default = null) use ($astrologerSources) {
        foreach ($astrologerSources as $source) {
            foreach ($paths as $path) {
                $value = data_get($source, $path);

                if (is_string($value)) {
                    $value = trim($value);
                }

                if ($value !== null && $value !== '') {
                    return $value;
                }
            }
        }

        return $default;
    };
    $formatAppointmentList = function ($value) {
        if (!is_array($value)) {
            return trim((string) $value);
        }

        return collect($value)->map(function ($item) {
            if (is_array($item)) {
                return $item['name'] ?? $item['title'] ?? $item['label'] ?? $item['value'] ?? null;
            }

            return $item;
        })->filter(function ($item) {
            return $item !== null && trim((string) $item) !== '';
        })->join(', ');
    };
    $astrologerName = trim((string) ($resolveRootValue([
        'astrologer_name',
        'astrologer_full_name',
    ]) ?? $resolveAstrologerValue([
        'name',
        'full_name',
        'display_name',
    ], '')));
    if ($astrologerName === '') {
        $astrologerName = trim(
            ((string) ($resolveRootValue(['astrologer_first_name']) ?? $resolveAstrologerValue(['first_name'], '')))
            . ' '
            . ((string) ($resolveRootValue(['astrologer_last_name']) ?? $resolveAstrologerValue(['last_name'], '')))
        );
    }
    $astrologerEmail = trim((string) ($resolveRootValue([
        'astrologer_email',
    ]) ?? $resolveAstrologerValue([
        'email',
        'user.email',
    ], '')));
    $astrologerPhone = trim((string) ($resolveRootValue([
        'astrologer_mobile_no',
        'astrologer_phone',
        'astrologer_contact_no',
    ]) ?? $resolveAstrologerValue([
        'mobile_no',
        'phone',
        'contact_no',
        'user.mobile_no',
        'user.phone',
        'user.contact_no',
    ], '')));
    $astrologerExperience = $resolveRootValue([
        'astrologer_experience',
    ]) ?? $resolveAstrologerValue([
        'experience',
        'exp_in_years',
    ]);
    $astrologerLanguages = $formatAppointmentList($resolveRootValue([
        'astrologer_languages',
    ]) ?? $resolveAstrologerValue([
        'languages',
        'language',
    ], []));
    $astrologerSkills = $formatAppointmentList($resolveRootValue([
        'astrologer_skills',
        'astrologer_specializations',
    ]) ?? $resolveAstrologerValue([
        'skills',
        'specializations',
        'specialisations',
    ], []));
    $astrologerDesignation = trim((string) ($resolveRootValue([
        'astrologer_designation',
        'astrologer_qualification',
    ]) ?? $resolveAstrologerValue([
        'designation',
        'qualification',
        'title',
    ], '')));
    $bookingDetailsPageData = isset($appointment)
        ? [
            'bookingId' => $appointment['id'],
            'customerJoinUrl' => route('customer.consultation.video', ['meetingId' => 'astro-' . $appointment['id']]),
            'astrologerId' => (int) ($appointment['astrologer_id'] ?? data_get($appointment, 'astrologer.id') ?? data_get($appointment, 'assigned_astrologer_id') ?? 0),
            'currentDate' => !empty($appointment['scheduled_at']) ? \Carbon\Carbon::parse($appointment['scheduled_at'])->format('Y-m-d') : null,
            'slotsUrl' => route('consultation.slots'),
            'rescheduleUrl' => route('astrologer.appointment.reschedule', ['id' => $appointment['id']]),
            'canReschedule' => ! $isRescheduleDisabled,
            'suggestProductUrl' => route('astrologer.appointment.suggestProduct', ['id' => $appointment['id']]),
            'addSuggestedProductUrl' => route('astrologer.appointment.addSuggestedProduct', ['id' => $appointment['id']]),
            'removeSuggestedProductUrl' => route('astrologer.appointment.removeSuggestedProduct', ['id' => $appointment['id']]),
            'finalizeNotesUrl' => route('astrologer.appointment.finalizeNotes', ['id' => $appointment['id']]),
            'isNoteFinalized' => $isNoteFinalized,
        ]
        : null;
@endphp
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

    .booking-status-badge.ready_to_start {
        background: #d1ecf1;
        color: #0c5460;
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

    .note-editor {
        width: 100%;
        min-height: 150px;
        resize: vertical;
        border: 1px solid #d7dee7;
        border-radius: 10px;
        padding: 0.9rem 1rem;
        font-size: 1rem;
        line-height: 1.5;
        color: #222;
        background: #fff;
    }

    .note-editor:focus {
        outline: none;
        border-color: #f98700;
        box-shadow: 0 0 0 3px rgba(249, 135, 0, 0.12);
    }

    .note-document-paper {
        display: none;
        width: 100%;
        max-width: 794px;
        min-height: 1123px;
        margin: 0 auto;
        padding: 56px 52px;
        background: #fff;
        border: 1px solid #d9d9d9;
        border-radius: 10px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
        color: #1f2937;
    }

    .note-document-heading {
        margin-bottom: 1.75rem;
        border-bottom: 2px solid #f3e1c7;
    }

    .note-document-pad {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1.5rem;
        padding-bottom: 1rem;
    }

    .note-document-brand {
        flex: 1 1 auto;
    }

    .note-document-title {
        font-size: 1.55rem;
        font-weight: 700;
        color: #111827;
    }

    .note-document-doctor {
        font-size: 1rem;
        font-weight: 600;
        color: #f98700;
        margin-top: 0.3rem;
    }

    .note-document-subtitle {
        font-size: 0.95rem;
        color: #6b7280;
    }

    .note-document-contact {
        margin-top: 0.8rem;
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem 1.25rem;
        font-size: 0.95rem;
        color: #374151;
    }

    .note-document-contact strong {
        color: #111827;
    }

    .note-document-meta {
        flex: 0 0 220px;
        text-align: right;
        font-size: 0.95rem;
        color: #4b5563;
    }

    .note-document-meta strong {
        display: block;
        color: #111827;
        font-size: 1rem;
        margin-bottom: 0.2rem;
    }

    .note-document-specialties {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.85rem 1rem;
        padding: 1rem 0 1.25rem;
        border-top: 1px solid #f6ead9;
    }

    .note-document-specialty-label {
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        color: #9a3412;
        margin-bottom: 0.2rem;
    }

    .note-document-specialty-value {
        font-size: 0.95rem;
        color: #374151;
    }

    .note-document-body {
        font-size: 1rem;
        line-height: 1.85;
        white-space: normal;
        word-break: break-word;
    }

    .note-document-body p {
        margin-bottom: 1rem;
    }

    .note-document-empty {
        color: #6b7280;
        font-style: italic;
    }

    @media (max-width: 991.98px) {
        .note-document-paper {
            min-height: auto;
            padding: 28px 22px;
        }

        .note-document-pad {
            flex-direction: column;
        }

        .note-document-meta {
            flex-basis: auto;
            text-align: left;
        }

        .note-document-specialties {
            grid-template-columns: 1fr;
        }
    }

    .note-feedback {
        min-height: 1.5rem;
    }

    .note-feedback .alert {
        margin-bottom: 0;
    }

    .btn-save-note {
        background: #f98700;
        color: #fff;
        border: none;
    }

    .btn-save-note:hover,
    .btn-save-note:focus {
        background: #d97706;
        color: #fff;
    }

    .btn-finalize-note {
        border-color: #198754;
        color: #198754;
        background: #fff;
    }

    .btn-finalize-note:hover,
    .btn-finalize-note:focus {
        background: #198754;
        color: #fff;
    }

    .suggested-products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1rem;
    }

    .suggested-product-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1rem;
        border: 1px solid #ececec;
        position: relative;
    }

    .suggested-product-card img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 8px;
        margin-bottom: 0.75rem;
        background: #fff;
    }

    .suggested-product-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.45rem;
        margin-top: 0.75rem;
    }

    .suggested-product-meta .badge {
        font-weight: 500;
    }

    .suggested-product-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.75rem;
        align-items: flex-end;
    }

    .suggested-product-remove {
        margin-left: auto;
    }

    .btn-outline-theme {
        color: #f98700;
        border-color: #f98700;
        background: #fff;
    }

    .btn-outline-theme:hover,
    .btn-outline-theme:focus {
        color: #fff;
        background: #f98700;
        border-color: #f98700;
    }

    .product-search-results .list-group-item {
        border-radius: 10px;
        margin-bottom: 0.75rem;
        border: 1px solid #ececec;
    }

    .collapsible-section-toggle {
        width: 100%;
        border: 0;
        background: transparent;
        padding: 0;
        text-align: left;
        display: flex;
        align-items: center;
        justify-content: space-between;
        color: inherit;
    }

    .collapsible-section-toggle:focus {
        outline: none;
    }

    .collapsible-section-toggle-icon {
        transition: transform 0.2s ease;
    }

    .collapsible-section-toggle[aria-expanded="false"] .collapsible-section-toggle-icon {
        transform: rotate(-90deg);
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
            <i class="fa-solid {{ $appointment['status'] === 'confirmed' ? 'fa-circle-check text-success' : (($appointment['status'] ?? null) === 'ready_to_start' ? 'fa-circle-play text-info' : ($appointment['status'] === 'pending' ? 'fa-hourglass-half text-warning' : ($appointment['status'] === 'in_progress' ? 'fa-video text-success' : 'fa-circle-xmark text-danger'))) }} me-1"></i>
            {{ str_replace('_', ' ', ucfirst($appointment['status'])) }}
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
                <button type="button" id="booking-reschedule-btn" class="btn btn-outline-warning{{ $isRescheduleDisabled ? ' disabled' : '' }}" @if($isRescheduleDisabled) disabled aria-disabled="true" title="Completed or in-progress appointments cannot be rescheduled." @endif>
                    <i class="fa-solid fa-calendar-days me-1"></i> Reschedule Appointment
                </button>

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
                        <td id="booking-scheduled-date-cell">{{ \Carbon\Carbon::parse($appointment['scheduled_at'])->format('d F Y') }}</td>
                        <td id="booking-scheduled-slot-cell">
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

        <div class="booking-details-col mt-4" style="max-width:none;">
            <div class="booking-details-label mb-2"><i class="fa-solid fa-note-sticky me-1"></i> Astrologer Note</div>
            <form id="save-notes-form" method="POST" action="{{ route('astrologer.appointment.saveNotes', ['id' => $appointment['id']]) }}">
                @csrf
                <textarea
                    id="astrologer-note-textarea"
                    name="astrologer_note"
                    class="note-editor"
                    placeholder="Write consultation notes for this appointment..."
                    @readonly($isNoteFinalized)
                    aria-readonly="{{ $isNoteFinalized ? 'true' : 'false' }}"
                    style="display:{{ $isNoteFinalized ? 'none' : 'block' }};"
                >{{ old('astrologer_note', $appointment['astrologer_note'] ?? '') }}</textarea>
                <div id="astrologer-note-document" class="note-document-paper" style="display:{{ $isNoteFinalized ? 'block' : 'none' }};">
                    <div class="note-document-heading">
                        <div class="note-document-pad">
                            <div class="note-document-brand">
                                <div class="note-document-title">Astrologer Consultation Notes</div>
                                <div class="note-document-doctor">
                                    {{ $astrologerName !== '' ? $astrologerName : 'Astrologer Consultant' }}
                                    @if($astrologerDesignation !== '')
                                        <span class="note-document-subtitle d-block mt-1">{{ $astrologerDesignation }}</span>
                                    @endif
                                </div>
                                <div class="note-document-contact">
                                    @if($astrologerEmail !== '')
                                        <span><strong>Email:</strong> {{ $astrologerEmail }}</span>
                                    @endif
                                    @if($astrologerPhone !== '')
                                        <span><strong>Phone:</strong> {{ $astrologerPhone }}</span>
                                    @endif
                                    @if($astrologerEmail === '' && $astrologerPhone === '')
                                        <span class="note-document-subtitle">Professional consultation summary</span>
                                    @endif
                                </div>
                            </div>
                            <div class="note-document-meta">
                                <strong>Booking ID: BKNG{{ $appointment['id'] }}</strong>
                                <div>{{ !empty($appointment['scheduled_at']) ? \Carbon\Carbon::parse($appointment['scheduled_at'])->format('d M Y') : now()->format('d M Y') }}</div>
                                <div>{{ !empty($appointment['scheduled_at']) ? \Carbon\Carbon::parse($appointment['scheduled_at'])->format('h:i A') : '' }}</div>
                            </div>
                        </div>
                        <div class="note-document-specialties">
                            <div>
                                <div class="note-document-specialty-label">Experience</div>
                                <div class="note-document-specialty-value">{{ ($astrologerExperience !== null && $astrologerExperience !== '') ? $astrologerExperience . ' years' : 'Not specified' }}</div>
                            </div>
                            <div>
                                <div class="note-document-specialty-label">Languages</div>
                                <div class="note-document-specialty-value">{{ $astrologerLanguages !== '' ? $astrologerLanguages : 'Not specified' }}</div>
                            </div>
                            <div style="grid-column: 1 / -1;">
                                <div class="note-document-specialty-label">Specializations</div>
                                <div class="note-document-specialty-value">{{ $astrologerSkills !== '' ? $astrologerSkills : 'Not specified' }}</div>
                            </div>
                        </div>
                    </div>
                    <div id="astrologer-note-document-body" class="note-document-body">
                        @php
                            $finalizedNote = trim((string) old('astrologer_note', $appointment['astrologer_note'] ?? ''));
                        @endphp
                        @if($finalizedNote !== '')
                            {!! nl2br(e($finalizedNote)) !!}
                        @else
                            <p class="note-document-empty mb-0">No astrologer note was provided for this appointment.</p>
                        @endif
                    </div>
                </div>
                @error('astrologer_note')
                    <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
                <div id="notes-feedback" class="note-feedback mt-2"></div>
                <div class="d-flex justify-content-end gap-2 mt-3">
                    <button type="submit" id="save-notes-btn" class="btn btn-save-note">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Draft Note
                    </button>
                    <button type="button" id="finalize-notes-btn" class="btn btn-finalize-note">
                        <i class="fa-solid fa-circle-check me-1"></i> Close &amp; Finalize
                    </button>
                </div>
            </form>
        </div>

        <div class="modal fade" id="finalize-notes-confirmation-modal" tabindex="-1" aria-labelledby="finalize-notes-confirmation-label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="finalize-notes-confirmation-label">Finalize Consultation Notes</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2">Once finalized, your note and suggested products will be locked.</p>
                        <p class="mb-0">Are you sure you want to close and finalize this appointment?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="confirm-finalize-notes-btn" class="btn btn-finalize-note">
                            <i class="fa-solid fa-circle-check me-1"></i> Confirm Finalize
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="booking-details-col mt-4" style="max-width:none;">
            <button type="button" class="collapsible-section-toggle" data-bs-toggle="collapse" data-bs-target="#suggest-products-collapse" aria-expanded="false" aria-controls="suggest-products-collapse">
                <span class="booking-details-label mb-0"><i class="fa-solid fa-filter me-1"></i> Find Products To Suggest</span>
                <i class="fa-solid fa-chevron-down collapsible-section-toggle-icon"></i>
            </button>
            <div id="suggest-products-collapse" class="collapse mt-3">
                <form id="suggest-products" method="POST" action="{{ route('astrologer.appointment.suggestProduct', ['id' => $appointment['id']]) }}">
                    @csrf
                    <div class="row g-2 mb-2 align-items-center">
                        <div class="col-md-6">
                            <select class="form-select" name="product_grade_id">
                                <option value="">Select Product Grade</option>
                                @foreach(($productGrades ?? []) as $productGrade)
                                    <option value="{{ $productGrade['id'] }}">{{ $productGrade['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <select class="form-select" name="category_id">
                                <option value="">Select Category</option>
                                @foreach(($productCategories ?? []) as $categoryGroup)
                                    @if(!empty($categoryGroup['options']))
                                        <optgroup label="{{ $categoryGroup['label'] }}">
                                            @foreach($categoryGroup['options'] as $category)
                                                <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <input type="text" class="form-control" name="q" placeholder="Search Product">
                        </div>
                        <div class="col-md-6">
                            <input type="number" min="0" step="0.01" class="form-control" name="ratti" placeholder="Ratti">
                        </div>
                        <div class="col-md-6">
                            <input type="number" min="0" step="0.01" class="form-control" name="carat" placeholder="Carat">
                        </div>
                        <div class="col-md-6">
                            <input type="number" min="0" step="1" class="form-control" name="min_price" placeholder="Min Price">
                        </div>
                        <div class="col-md-6">
                            <input type="number" min="0" step="1" class="form-control" name="max_price" placeholder="Max Price">
                        </div>
                        <div class="col-md-6">
                            <input type="number" min="1" max="100" step="1" class="form-control" name="per_page" value="20" placeholder="Per Page">
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="form-check ms-1">
                                <input class="form-check-input" type="checkbox" value="1" id="product-in-stock" name="in_stock" checked>
                                <label class="form-check-label" for="product-in-stock">In stock only</label>
                            </div>
                        </div>
                    </div>
                    <button id="search-products-btn" class="btn btn-outline-theme mt-2" type="submit">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Search Products
                    </button>
                    <div id="product-search-feedback" class="mt-2"></div>
                </form>
                <div class="mt-3">
                    <div id="product-search-empty-state" class="text-muted small">Use the filters above to search the product catalog.</div>
                    <div id="product-search-results" class="list-group product-search-results mt-2"></div>
                </div>
            </div>
        </div>

        <div class="booking-details-col mt-4" style="max-width:none;">
            <div class="booking-details-label mb-3"><i class="fa-solid fa-gem me-1"></i> Suggested Products</div>
            <div id="suggested-products-empty-state" class="booking-details-value mb-0" style="display:{{ !empty($suggestedProducts) ? 'none' : 'block' }};">No products have been suggested for this appointment yet.</div>
            <div id="suggested-products-grid" class="suggested-products-grid" style="display:{{ !empty($suggestedProducts) ? 'grid' : 'none' }};">
                @if(!empty($suggestedProducts))
                    @foreach($suggestedProducts as $product)
                        <div class="suggested-product-card" data-cart-id="{{ (int) ($product['id'] ?? 0) }}" data-suggested-key="{{ (int) ($product['product_id'] ?? 0) }}:{{ $product['variation_id'] ?? '' }}">
                            @if(!empty($product['image']))
                                <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}">
                            @endif
                            <div class="fw-semibold mb-1">{{ $product['name'] }}</div>
                            <div class="booking-details-value mb-2">
                                {{ ($product['currency_symbol'] ?? '₹') . number_format((float) ($product['price'] ?? $product['original_price'] ?? 0), 2) }}
                                @if(!empty($product['discount_rate']))
                                    <span class="badge bg-danger ms-2">{{ rtrim(rtrim(number_format((float) $product['discount_rate'], 2), '0'), '.') }}% off</span>
                                @endif
                            </div>
                            @if(!empty($product['original_price']) && !empty($product['discount_rate']))
                                <div class="text-muted small mb-2">Base Price: {{ ($product['currency_symbol'] ?? '₹') . number_format((float) $product['original_price'], 2) }}</div>
                            @endif
                            <div class="suggested-product-meta">
                                @if(!empty($product['grade']))
                                    <span class="badge bg-info text-dark">Grade: {{ $product['grade'] }}</span>
                                @endif
                                @if(!empty($product['ratti']))
                                    <span class="badge bg-primary">Ratti: {{ $product['ratti'] }}</span>
                                @endif
                                @if(!empty($product['carat']))
                                    <span class="badge bg-warning text-dark">Carat: {{ $product['carat'] }}</span>
                                @endif
                                <!-- <span class="badge bg-light text-dark border">Qty: {{ (int) ($product['quantity'] ?? 1) }}</span> -->
                                @if(!empty($product['variation_id']))
                                    <span class="badge bg-light text-dark border">Variation: {{ $product['variation_id'] }}</span>
                                @endif
                            </div>
                            <div class="suggested-product-actions">
                                @if(!empty($product['url']))
                                    <a href="{{ $product['url'] }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary" @if(!empty($product['slug'])) data-product-slug="{{ $product['slug'] }}" @endif>
                                        <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> View Product
                                    </a>
                                @endif
                                @if(!empty($product['id']))
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-suggested-product-btn suggested-product-remove" data-cart-id="{{ (int) $product['id'] }}">
                                        <i class="fa-solid fa-trash-can me-1"></i> Remove
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
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

@if($bookingDetailsPageData)
    @php
        $bookingRescheduleConfig = array_merge($bookingDetailsPageData, [
            'modalId' => 'booking-reschedule-modal',
            'triggerId' => 'booking-reschedule-btn',
            'dateInputId' => 'booking-reschedule-date',
            'slotInputId' => 'booking-reschedule-slot',
            'slotBadgesId' => 'booking-reschedule-slot-badges',
            'submitButtonId' => 'booking-reschedule-submit',
            'alertId' => 'booking-reschedule-alert',
            'slotStateId' => 'booking-reschedule-slot-state',
            'dataScriptId' => 'booking-reschedule-data',
            'successEventName' => 'booking-reschedule:success',
        ]);
    @endphp
    @include('partials.booking-reschedule-modal', ['bookingRescheduleConfig' => $bookingRescheduleConfig])
    <script id="booking-details-page-data" type="application/json">{!! json_encode($bookingDetailsPageData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const pageDataElement = document.getElementById('booking-details-page-data');
        const scheduledDateCell = document.getElementById('booking-scheduled-date-cell');
        const scheduledSlotCell = document.getElementById('booking-scheduled-slot-cell');
        const saveNotesForm = document.getElementById('save-notes-form');
        const saveNotesBtn = document.getElementById('save-notes-btn');
        const finalizeNotesBtn = document.getElementById('finalize-notes-btn');
        const finalizeNotesConfirmationModalElement = document.getElementById('finalize-notes-confirmation-modal');
        const confirmFinalizeNotesBtn = document.getElementById('confirm-finalize-notes-btn');
        const noteTextarea = document.getElementById('astrologer-note-textarea');
        const noteDocument = document.getElementById('astrologer-note-document');
        const noteDocumentBody = document.getElementById('astrologer-note-document-body');
        const notesFeedback = document.getElementById('notes-feedback');
        const suggestProductsForm = document.getElementById('suggest-products');
        const searchProductsBtn = document.getElementById('search-products-btn');
        const productSearchFeedback = document.getElementById('product-search-feedback');
        const productSearchResults = document.getElementById('product-search-results');
        const productSearchEmptyState = document.getElementById('product-search-empty-state');
        const suggestedProductsGrid = document.getElementById('suggested-products-grid');
        const suggestedProductsEmptyState = document.getElementById('suggested-products-empty-state');
        const noteAutosaveDelay = 1200;
        let noteSaveTimeout = null;
        let noteSaveInFlight = false;
        let noteFinalizeInFlight = false;
        let lastSavedNote = noteTextarea ? noteTextarea.value : '';

        if (!pageDataElement) {
            return;
        }

        const pageData = JSON.parse(pageDataElement.textContent || '{}');
        const bookingId = pageData.bookingId;
        const suggestProductUrl = pageData.suggestProductUrl;
        const addSuggestedProductUrl = pageData.addSuggestedProductUrl;
        const removeSuggestedProductUrl = pageData.removeSuggestedProductUrl;
        const finalizeNotesUrl = pageData.finalizeNotesUrl;
        const finalizeNotesConfirmationModal = finalizeNotesConfirmationModalElement && window.bootstrap
            ? new bootstrap.Modal(finalizeNotesConfirmationModalElement)
            : null;
        let isNoteFinalized = Boolean(pageData.isNoteFinalized);

        if (!bookingId) {
            return;
        }

        function escapeHtml(value) {
            return String(value === null || value === undefined ? '' : value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function renderNoteDocument(noteValue) {
            if (!noteDocumentBody) {
                return;
            }

            const normalizedValue = String(noteValue === null || noteValue === undefined ? '' : noteValue).trim();
            if (!normalizedValue) {
                noteDocumentBody.innerHTML = '<p class="note-document-empty mb-0">No astrologer note was provided for this appointment.</p>';
                return;
            }

            const paragraphs = normalizedValue
                .split(/\n{2,}/)
                .map(function(paragraph) {
                    return paragraph.trim();
                })
                .filter(Boolean)
                .map(function(paragraph) {
                    return '<p>' + escapeHtml(paragraph).replace(/\n/g, '<br>') + '</p>';
                });

            noteDocumentBody.innerHTML = paragraphs.join('');
        }

        function setButtonLoading(button, isLoading, loadingText, defaultHtml) {
            if (!button) {
                return;
            }

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

        function setFinalizeButtonState(finalized) {
            if (!finalizeNotesBtn) {
                return;
            }

            if (finalized) {
                finalizeNotesBtn.disabled = true;
                finalizeNotesBtn.classList.remove('btn-finalize-note');
                finalizeNotesBtn.classList.add('btn-success');
                finalizeNotesBtn.innerHTML = '<i class="fa-solid fa-lock me-1"></i> Finalized';
                return;
            }

            finalizeNotesBtn.disabled = false;
            finalizeNotesBtn.classList.remove('btn-success');
            finalizeNotesBtn.classList.add('btn-finalize-note');
            finalizeNotesBtn.innerHTML = '<i class="fa-solid fa-circle-check me-1"></i> Close &amp; Finalize';
        }

        function setProductsEditingDisabled(disabled) {
            if (suggestProductsForm) {
                suggestProductsForm.querySelectorAll('input, select, button').forEach(function(element) {
                    element.disabled = disabled;
                });
            }

            if (productSearchResults) {
                productSearchResults.querySelectorAll('.suggest-product-btn').forEach(function(button) {
                    button.disabled = disabled;
                });
            }

            if (suggestedProductsGrid) {
                suggestedProductsGrid.querySelectorAll('.remove-suggested-product-btn').forEach(function(button) {
                    button.disabled = disabled;
                });
            }
        }

        function applyFinalizedState(finalized) {
            isNoteFinalized = Boolean(finalized);

            if (noteTextarea) {
                noteTextarea.disabled = isNoteFinalized;
                noteTextarea.readOnly = isNoteFinalized;
                noteTextarea.style.display = isNoteFinalized ? 'none' : 'block';
            }

            if (noteDocument) {
                if (isNoteFinalized && noteTextarea) {
                    renderNoteDocument(noteTextarea.value);
                }

                noteDocument.style.display = isNoteFinalized ? 'block' : 'none';
            }

            if (saveNotesBtn) {
                saveNotesBtn.disabled = isNoteFinalized;
            }

            setFinalizeButtonState(isNoteFinalized);
            setProductsEditingDisabled(isNoteFinalized);
        }

        function showNotesFeedback(message, type) {
            if (!notesFeedback) {
                return;
            }

            if (!message) {
                notesFeedback.innerHTML = '';
                return;
            }

            notesFeedback.innerHTML = '<div class="alert alert-' + type + ' py-2 px-3 small">' + escapeHtml(message) + '</div>';
        }

        function showProductSearchFeedback(message, type) {
            if (!productSearchFeedback) {
                return;
            }

            if (!message) {
                productSearchFeedback.innerHTML = '';
                return;
            }

            productSearchFeedback.innerHTML = '<div class="alert alert-' + type + ' py-2 mb-0">' + escapeHtml(message) + '</div>';
        }

        function hasDisplayValue(value) {
            return value !== null && value !== undefined && String(value).trim() !== '';
        }

        function formatCurrency(amount, symbol) {
            if (amount === null || amount === undefined || amount === '') {
                return 'Price unavailable';
            }

            return (symbol || '₹') + Number(amount).toLocaleString('en-IN', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
            });
        }

        function setSuggestedProductButtonState(button, state, message) {
            if (!button) {
                return;
            }

            if (state === 'loading') {
                button.disabled = true;
                button.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Suggesting';
                return;
            }

            if (state === 'success') {
                button.disabled = true;
                button.classList.remove('btn-outline-theme');
                button.classList.add('btn-success');
                button.innerHTML = '<i class="fa-solid fa-check me-1"></i> Suggested';
                if (message) {
                    button.title = message;
                }
                return;
            }

            button.disabled = false;
            button.classList.remove('btn-success');
            button.classList.add('btn-outline-theme');
            button.innerHTML = '<i class="fa-solid fa-cart-plus me-1"></i> Suggest Product';
        }

        function renderSuggestedProductCard(product) {
            return '<div class="suggested-product-card" data-cart-id="' + escapeHtml(product.id || product.cart_id || '') + '" data-suggested-key="' + escapeHtml(String((product.product_id || product.id || '') + ':' + (product.variation_id || ''))) + '">'
                + (product.image ? '<img src="' + escapeHtml(product.image) + '" alt="' + escapeHtml(product.name) + '">' : '')
                + '<div class="fw-semibold mb-1">' + escapeHtml(product.name || 'Suggested Product') + '</div>'
                + '<div class="booking-details-value mb-2">' + escapeHtml(formatCurrency(product.price ?? product.original_price ?? 0, product.currency_symbol))
                + (hasDisplayValue(product.discount_rate) ? '<span class="badge bg-danger ms-2">' + escapeHtml(String(product.discount_rate).replace(/\.0+$/, '')) + '% off</span>' : '')
                + '</div>'
                + ((hasDisplayValue(product.original_price) && hasDisplayValue(product.discount_rate)) ? '<div class="text-muted small mb-2">Base Price: ' + escapeHtml(formatCurrency(product.original_price, product.currency_symbol)) + '</div>' : '')
                + '<div class="suggested-product-meta">'
                + (hasDisplayValue(product.grade) ? '<span class="badge bg-info text-dark">Grade: ' + escapeHtml(product.grade) + '</span>' : '')
                + (hasDisplayValue(product.ratti) ? '<span class="badge bg-primary">Ratti: ' + escapeHtml(product.ratti) + '</span>' : '')
                + (hasDisplayValue(product.carat) ? '<span class="badge bg-warning text-dark">Carat: ' + escapeHtml(product.carat) + '</span>' : '')
                + '<span class="badge bg-light text-dark border">Qty: ' + escapeHtml(product.quantity || 1) + '</span>'
                + (hasDisplayValue(product.variation_id) ? '<span class="badge bg-light text-dark border">Variation: ' + escapeHtml(product.variation_id) + '</span>' : '')
                + '</div>'
                + '<div class="suggested-product-actions">'
                + (product.url ? '<a href="' + escapeHtml(product.url) + '" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-arrow-up-right-from-square me-1"></i> View Product</a>' : '')
                + (!product.url && hasDisplayValue(product.slug) ? '<div class="text-muted small align-self-center">Product Slug: ' + escapeHtml(product.slug) + '</div>' : '')
                + (hasDisplayValue(product.id || product.cart_id) ? '<button type="button" class="btn btn-sm btn-outline-danger remove-suggested-product-btn suggested-product-remove" data-cart-id="' + escapeHtml(product.id || product.cart_id) + '"><i class="fa-solid fa-trash-can me-1"></i> Remove</button>' : '')
                + '</div>'
                + '</div>';
        }

        function prependSuggestedProduct(product) {
            if (!suggestedProductsGrid) {
                return;
            }

            const suggestedKey = String((product.product_id || product.id || '') + ':' + (product.variation_id || ''));
            const existing = suggestedProductsGrid.querySelector('[data-suggested-key="' + suggestedKey + '"]');
            if (existing) {
                return;
            }

            const wrapper = document.createElement('div');
            wrapper.innerHTML = renderSuggestedProductCard(product);
            suggestedProductsGrid.prepend(wrapper.firstChild);
            suggestedProductsGrid.style.display = '';
            if (suggestedProductsEmptyState) {
                suggestedProductsEmptyState.style.display = 'none';
            }
        }

        function toggleSuggestedProductsEmptyState() {
            if (!suggestedProductsGrid || !suggestedProductsEmptyState) {
                return;
            }

            if (suggestedProductsGrid.children.length === 0) {
                suggestedProductsGrid.style.display = 'none';
                suggestedProductsEmptyState.style.display = '';
            } else {
                suggestedProductsGrid.style.display = '';
                suggestedProductsEmptyState.style.display = 'none';
            }
        }

        function removeSuggestedProduct(button) {
            if (!button || !removeSuggestedProductUrl) {
                return Promise.resolve();
            }

            const cartId = button.dataset.cartId;
            const card = button.closest('.suggested-product-card');

            if (!hasDisplayValue(cartId) || !card) {
                showProductSearchFeedback('This suggested product cannot be removed.', 'danger');
                return Promise.resolve();
            }

            setButtonLoading(button, true, '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Removing');

            return fetch(removeSuggestedProductUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    cart_id: cartId
                })
            })
            .then(function(response) {
                return response.json().then(function(data) {
                    return { ok: response.ok, status: response.status, data: data };
                });
            })
            .then(function(result) {
                if (result.ok && result.data.success) {
                    const suggestedKey = card.dataset.suggestedKey || '';
                    card.remove();
                    toggleSuggestedProductsEmptyState();

                    if (suggestedKey) {
                        const productId = suggestedKey.split(':')[0] || '';
                        const variationId = suggestedKey.split(':')[1] || '';
                        const selector = '.suggest-product-btn[data-product-id="' + productId.replace(/"/g, '&quot;') + '"]';
                        document.querySelectorAll(selector).forEach(function(suggestButton) {
                            if ((suggestButton.dataset.variationId || '') === variationId) {
                                setSuggestedProductButtonState(suggestButton, 'default');
                            }
                        });
                    }

                    showProductSearchFeedback(result.data.message || 'Suggested product removed successfully.', 'success');
                    return;
                }

                var errorMessage = (result.data && result.data.message) ? result.data.message : 'Failed to remove suggested product.';
                if (result.status === 422 && result.data && result.data.errors) {
                    var firstErrorKey = Object.keys(result.data.errors)[0];
                    if (firstErrorKey && result.data.errors[firstErrorKey] && result.data.errors[firstErrorKey][0]) {
                        errorMessage = result.data.errors[firstErrorKey][0];
                    }
                }

                showProductSearchFeedback(errorMessage, 'danger');
            })
            .catch(function(error) {
                showProductSearchFeedback(error.message || 'Failed to remove suggested product.', 'danger');
            })
            .finally(function() {
                setButtonLoading(button, false);
            });
        }

        function suggestProductForBooking(button) {
            if (!button) {
                return Promise.resolve();
            }

            var productId = button.dataset.productId;
            var variationId = button.dataset.variationId;

            if (!hasDisplayValue(productId)) {
                showProductSearchFeedback('This product is missing an ID and cannot be suggested.', 'danger');
                return Promise.resolve();
            }

            showProductSearchFeedback('', 'success');
            setSuggestedProductButtonState(button, 'loading');

            var payload = {
                product_id: productId,
                quantity: 1
            };

            if (hasDisplayValue(variationId)) {
                payload.variation_id = variationId;
            }

            return fetch(addSuggestedProductUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(function(response) {
                return response.json().then(function(data) {
                    return { ok: response.ok, status: response.status, data: data };
                });
            })
            .then(function(result) {
                if (result.ok && result.data.success) {
                    var responseData = result.data.data || {};
                    var nestedData = responseData.data || {};
                    var cartId = responseData.id || responseData.cart_id || nestedData.id || nestedData.cart_id || '';

                    setSuggestedProductButtonState(button, 'success', result.data.message || 'Product suggested successfully.');
                    prependSuggestedProduct({
                        id: cartId,
                        product_id: button.dataset.productId,
                        variation_id: button.dataset.variationId,
                        name: button.dataset.productName,
                        price: button.dataset.productPrice,
                        original_price: button.dataset.productOriginalPrice,
                        discount_rate: button.dataset.productDiscountRate,
                        currency_symbol: button.dataset.productCurrencySymbol,
                        image: button.dataset.productImage,
                        grade: button.dataset.productGrade,
                        ratti: button.dataset.productRatti,
                        carat: button.dataset.productCarat,
                        url: button.dataset.productUrl,
                        slug: button.dataset.productSlug,
                        quantity: 1
                    });
                    showProductSearchFeedback(result.data.message || 'Product suggested successfully.', 'success');
                    return;
                }

                var errorMessage = (result.data && result.data.message) ? result.data.message : 'Failed to suggest product.';
                if (result.status === 422 && result.data && result.data.errors) {
                    var firstErrorKey = Object.keys(result.data.errors)[0];
                    if (firstErrorKey && result.data.errors[firstErrorKey] && result.data.errors[firstErrorKey][0]) {
                        errorMessage = result.data.errors[firstErrorKey][0];
                    }
                }

                setSuggestedProductButtonState(button, 'default');
                showProductSearchFeedback(errorMessage, 'danger');
            })
            .catch(function(error) {
                setSuggestedProductButtonState(button, 'default');
                showProductSearchFeedback(error.message || 'Failed to suggest product.', 'danger');
            });
        }

        function renderProductResults(products) {
            if (!productSearchResults || !productSearchEmptyState) {
                return;
            }

            productSearchResults.innerHTML = '';

            if (!products || !products.length) {
                productSearchEmptyState.style.display = '';
                productSearchEmptyState.textContent = 'No matching products found for the selected filters.';
                return;
            }

            productSearchEmptyState.style.display = 'none';

            products.forEach(function(product) {
                var wrapper = document.createElement('div');
                wrapper.className = 'list-group-item suggested-product-item';

                var stockBadgeClass = product.in_stock ? 'bg-success' : 'bg-secondary';
                var metaParts = [];
                if (hasDisplayValue(product.category)) metaParts.push(product.category);
                if (hasDisplayValue(product.brand)) metaParts.push(product.brand);
                var detailBadges = [];
                if (hasDisplayValue(product.grade)) {
                    detailBadges.push('<span class="badge bg-info text-dark">Grade: ' + escapeHtml(product.grade) + '</span>');
                } else if (hasDisplayValue(product.product_grade_id)) {
                    detailBadges.push('<span class="badge bg-info text-dark">Grade ID: ' + escapeHtml(product.product_grade_id) + '</span>');
                }
                if (hasDisplayValue(product.ratti)) detailBadges.push('<span class="badge bg-primary">Ratti: ' + escapeHtml(product.ratti) + '</span>');
                if (hasDisplayValue(product.carat)) detailBadges.push('<span class="badge bg-warning text-dark">Carat: ' + escapeHtml(product.carat) + '</span>');

                var metaHtml = metaParts.length
                    ? '<div class="text-muted small">' + escapeHtml(metaParts.join(' • ')) + '</div>'
                    : '';

                wrapper.innerHTML =
                    '<div class="d-flex gap-3 align-items-start">' +
                        (product.image ? '<img src="' + escapeHtml(product.image) + '" alt="' + escapeHtml(product.name) + '" style="width:64px;height:64px;object-fit:cover;border-radius:10px;flex-shrink:0;">' : '<div style="width:64px;height:64px;border-radius:10px;background:#f6efe4;display:flex;align-items:center;justify-content:center;flex-shrink:0;color:#f98700;"><i class="fa-solid fa-gem"></i></div>') +
                        '<div class="flex-grow-1">' +
                            '<div class="d-flex flex-wrap justify-content-between gap-2 align-items-start">' +
                                '<div>' +
                                    '<div class="fw-bold">' + escapeHtml(product.name) + '</div>' +
                                    metaHtml +
                                '</div>' +
                                '<div class="text-end">' +
                                    '<div class="fw-bold text-success">' + escapeHtml(formatCurrency(product.price, product.currency_symbol)) + '</div>' +
                                    '<span class="badge ' + stockBadgeClass + '">' + (product.in_stock ? 'In Stock' : 'Out of Stock') + '</span>' +
                                '</div>' +
                            '</div>' +
                            '<div class="mt-2 d-flex flex-wrap gap-2">' +
                                detailBadges.join('') +
                                (product.stock_quantity !== null ? '<span class="badge bg-light text-dark border">Qty: ' + escapeHtml(product.stock_quantity) + '</span>' : '') +
                                (hasDisplayValue(product.variation_id) ? '<span class="badge bg-light text-dark border">Variation: ' + escapeHtml(product.variation_id) + '</span>' : '') +
                                (hasDisplayValue(product.id) ? '<span class="badge bg-light text-dark border">ID: ' + escapeHtml(product.id) + '</span>' : '') +
                            '</div>' +
                            '<div class="mt-2 d-flex flex-wrap gap-2">' +
                                (hasDisplayValue(product.id) ? '<button type="button" class="btn btn-sm btn-outline-theme suggest-product-btn" data-product-id="' + escapeHtml(product.id) + '"' +
                                    (hasDisplayValue(product.variation_id) ? ' data-variation-id="' + escapeHtml(product.variation_id) + '"' : '') +
                                    ' data-product-name="' + escapeHtml(product.name) + '" data-product-price="' + escapeHtml(product.price) + '" data-product-original-price="' + escapeHtml(product.original_price || product.price) + '" data-product-discount-rate="' + escapeHtml(product.discount_rate || '') + '" data-product-currency-symbol="' + escapeHtml(product.currency_symbol || '₹') + '" data-product-image="' + escapeHtml(product.image || '') + '" data-product-grade="' + escapeHtml(product.grade || '') + '" data-product-ratti="' + escapeHtml(product.ratti || '') + '" data-product-carat="' + escapeHtml(product.carat || '') + '" data-product-url="' + escapeHtml(product.url || '') + '" data-product-slug="' + escapeHtml(product.slug || '') + '"><i class="fa-solid fa-cart-plus me-1"></i> Suggest Product</button>' : '') +
                                (product.url ? '<a class="btn btn-sm btn-outline-theme" href="' + escapeHtml(product.url) + '" target="_blank" rel="noopener noreferrer"><i class="fa-solid fa-arrow-up-right-from-square me-1"></i> View Product</a>' : '') +
                            '</div>' +
                        '</div>' +
                    '</div>';

                productSearchResults.appendChild(wrapper);
            });
        }

        function buildProductSearchPayload(form) {
            var formData = new FormData(form);

            return {
                q: (formData.get('q') || '').trim(),
                category_id: (formData.get('category_id') || '').trim(),
                product_grade_id: (formData.get('product_grade_id') || '').trim(),
                ratti: (formData.get('ratti') || '').trim(),
                carat: (formData.get('carat') || '').trim(),
                min_price: (formData.get('min_price') || '').trim(),
                max_price: (formData.get('max_price') || '').trim(),
                in_stock: formData.get('in_stock') ? 1 : 0,
                per_page: (formData.get('per_page') || '').trim()
            };
        }

        function submitNotes(options) {
            const config = Object.assign({
                force: false
            }, options || {});

            if (!saveNotesForm || !saveNotesBtn || !noteTextarea) {
                return Promise.resolve({ success: false });
            }

            if (isNoteFinalized) {
                return Promise.resolve({ success: false, finalized: true });
            }

            const noteValue = noteTextarea.value;

            if (!config.force && noteValue === lastSavedNote) {
                return Promise.resolve({ success: true, unchanged: true });
            }

            if (noteSaveInFlight) {
                return Promise.resolve({ success: false, inFlight: true });
            }

            if (noteSaveTimeout) {
                clearTimeout(noteSaveTimeout);
                noteSaveTimeout = null;
            }

            noteSaveInFlight = true;
            showNotesFeedback('Saving notes...', 'info');
            setButtonLoading(saveNotesBtn, true, '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Saving');

            return fetch(saveNotesForm.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    astrologer_note: noteValue
                })
            })
            .then(function(response) {
                return response.json().then(function(data) {
                    return { ok: response.ok, status: response.status, data: data };
                });
            })
            .then(function(result) {
                if (result.ok && result.data.success) {
                    lastSavedNote = noteValue;
                    showNotesFeedback(result.data.message || 'All changes saved.', 'success');
                    return { success: true, data: result.data };
                }

                var errorMessage = (result.data && result.data.message) ? result.data.message : 'Failed to save notes.';
                if (result.status === 422 && result.data && result.data.errors && result.data.errors.astrologer_note) {
                    errorMessage = result.data.errors.astrologer_note[0];
                }

                showNotesFeedback(errorMessage, 'danger');
                return { success: false, data: result.data };
            })
            .catch(function(error) {
                showNotesFeedback(error.message || 'Failed to save notes.', 'danger');
                return { success: false, error: error };
            })
            .finally(function() {
                noteSaveInFlight = false;
                setButtonLoading(saveNotesBtn, false);

                if (isNoteFinalized) {
                    applyFinalizedState(true);
                }

                if (noteTextarea.value !== lastSavedNote) {
                    scheduleNotesAutoSave();
                }
            });
        }

        function finalizeNotes() {
            if (!finalizeNotesBtn || !finalizeNotesUrl || noteFinalizeInFlight || isNoteFinalized) {
                return Promise.resolve({ success: isNoteFinalized });
            }

            noteFinalizeInFlight = true;

            return submitNotes({ force: true })
                .then(function(saveResult) {
                    if (!saveResult || !saveResult.success) {
                        noteFinalizeInFlight = false;
                        return { success: false };
                    }

                    showNotesFeedback('Finalizing notes...', 'info');
                    setButtonLoading(finalizeNotesBtn, true, '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Finalizing');

                    return fetch(finalizeNotesUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({})
                    })
                    .then(function(response) {
                        return response.json().then(function(data) {
                            return { ok: response.ok, status: response.status, data: data };
                        });
                    })
                    .then(function(result) {
                        if (result.ok && result.data.success) {
                            applyFinalizedState(true);
                            showNotesFeedback(result.data.message || 'Notes finalized successfully.', 'success');
                            return { success: true, data: result.data };
                        }

                        var errorMessage = (result.data && result.data.message) ? result.data.message : 'Failed to finalize notes.';
                        showNotesFeedback(errorMessage, 'danger');
                        return { success: false, data: result.data };
                    })
                    .catch(function(error) {
                        showNotesFeedback(error.message || 'Failed to finalize notes.', 'danger');
                        return { success: false, error: error };
                    })
                    .finally(function() {
                        noteFinalizeInFlight = false;
                        setButtonLoading(finalizeNotesBtn, false);
                        applyFinalizedState(isNoteFinalized);
                    });
                });
        }

        function scheduleNotesAutoSave() {
            if (!saveNotesForm || !noteTextarea) {
                return;
            }

            if (noteSaveTimeout) {
                clearTimeout(noteSaveTimeout);
            }

            if (noteTextarea.value === lastSavedNote) {
                showNotesFeedback('All changes saved.', 'success');
                return;
            }

            showNotesFeedback('Changes detected. Saving shortly...', 'secondary');
            noteSaveTimeout = window.setTimeout(function() {
                noteSaveTimeout = null;
                submitNotes();
            }, noteAutosaveDelay);
        }

        function formatDisplayDate(value) {
            const parts = String(value || '').split('-');
            if (parts.length !== 3) {
                return value || '-';
            }

            const date = new Date(parts[0], Number(parts[1]) - 1, parts[2]);
            if (Number.isNaN(date.getTime())) {
                return value || '-';
            }

            return date.toLocaleDateString('en-GB', {
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            });
        }

        window.addEventListener('booking-reschedule:success', function(event) {
            if (!event.detail || Number(event.detail.bookingId) !== Number(bookingId)) {
                return;
            }

            if (scheduledDateCell) {
                scheduledDateCell.textContent = formatDisplayDate(event.detail.date);
            }

            if (scheduledSlotCell) {
                scheduledSlotCell.textContent = event.detail.slotLabel || '-';
            }

            pageData.currentDate = event.detail.date;
        });

        if (saveNotesForm && saveNotesBtn && noteTextarea) {
            saveNotesForm.addEventListener('submit', function(event) {
                event.preventDefault();
                submitNotes({ force: true });
            });

            noteTextarea.addEventListener('input', function() {
                scheduleNotesAutoSave();
            });

            noteTextarea.addEventListener('blur', function() {
                submitNotes();
            });
        }

        if (finalizeNotesBtn) {
            finalizeNotesBtn.addEventListener('click', function() {
                if (isNoteFinalized || noteFinalizeInFlight) {
                    return;
                }

                if (finalizeNotesConfirmationModal) {
                    finalizeNotesConfirmationModal.show();
                    return;
                }

                if (window.confirm('Once finalized, the astrologer note and suggested products will be locked. Do you want to continue?')) {
                    finalizeNotes();
                }
            });
        }

        if (confirmFinalizeNotesBtn) {
            confirmFinalizeNotesBtn.addEventListener('click', function() {
                if (finalizeNotesConfirmationModal) {
                    finalizeNotesConfirmationModal.hide();
                }

                finalizeNotes();
            });
        }

        if (suggestProductsForm && searchProductsBtn) {
            suggestProductsForm.addEventListener('submit', function(event) {
                event.preventDefault();
                showProductSearchFeedback('', 'success');
                setButtonLoading(searchProductsBtn, true, '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Searching');

                fetch(suggestProductUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(buildProductSearchPayload(suggestProductsForm))
                })
                .then(function(response) {
                    return response.json().then(function(data) {
                        return { ok: response.ok, status: response.status, data: data };
                    });
                })
                .then(function(result) {
                    if (result.ok && result.data.success) {
                        renderProductResults(result.data.products || []);
                        showProductSearchFeedback('', 'success');
                        return;
                    }

                    var errorMessage = (result.data && result.data.message) ? result.data.message : 'Failed to search products.';
                    if (result.status === 422 && result.data && result.data.errors) {
                        var firstErrorKey = Object.keys(result.data.errors)[0];
                        if (firstErrorKey && result.data.errors[firstErrorKey] && result.data.errors[firstErrorKey][0]) {
                            errorMessage = result.data.errors[firstErrorKey][0];
                        }
                    }
                    renderProductResults([]);
                    showProductSearchFeedback(errorMessage, 'danger');
                })
                .catch(function(error) {
                    renderProductResults([]);
                    showProductSearchFeedback(error.message || 'Failed to search products.', 'danger');
                })
                .finally(function() {
                    setButtonLoading(searchProductsBtn, false);
                });
            });
        }

        if (productSearchResults) {
            productSearchResults.addEventListener('click', function(event) {
                var button = event.target.closest('.suggest-product-btn');

                if (!button) {
                    return;
                }

                event.preventDefault();
                suggestProductForBooking(button);
            });
        }

        if (suggestedProductsGrid) {
            suggestedProductsGrid.addEventListener('click', function(event) {
                var button = event.target.closest('.remove-suggested-product-btn');

                if (!button) {
                    return;
                }

                event.preventDefault();
                removeSuggestedProduct(button);
            });
        }

        applyFinalizedState(isNoteFinalized);
    });
    </script>
@endif

@endsection
