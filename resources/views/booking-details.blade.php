@extends('layouts.app')

@section('content')
@php
    $rescheduleBlockedStatuses = config('booking.reschedule_blocked_statuses', []);
    $isRescheduleDisabled = isset($booking) && in_array($booking['status'] ?? null, $rescheduleBlockedStatuses, true);
    $isNoteFinalized = isset($booking)
        && ((bool) ($booking['final_confirmation_from_astrologer'] ?? false)
        || (($booking['astrologer_note_status'] ?? null) === 'finalized'));
    $bookingRoot = isset($booking) && is_array($booking) ? $booking : [];
    $astrologerSources = array_values(array_filter([
        is_array(data_get($bookingRoot, 'astrologer')) ? data_get($bookingRoot, 'astrologer') : null,
        is_array(data_get($bookingRoot, 'assigned_astrologer')) ? data_get($bookingRoot, 'assigned_astrologer') : null,
        is_array(data_get($bookingRoot, 'consultant')) ? data_get($bookingRoot, 'consultant') : null,
        is_array(data_get($bookingRoot, 'astrologer.user')) ? data_get($bookingRoot, 'astrologer.user') : null,
        is_array(data_get($bookingRoot, 'assigned_astrologer.user')) ? data_get($bookingRoot, 'assigned_astrologer.user') : null,
    ], function ($source) {
        return is_array($source) && $source !== [];
    }));
    $resolveBookingValue = function (array $paths, $default = null) use ($bookingRoot) {
        foreach ($paths as $path) {
            $value = data_get($bookingRoot, $path);

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
    $formatBookingList = function ($value) {
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
    $astrologerName = trim((string) ($resolveBookingValue([
        'astrologer_name',
        'astrologer_full_name',
    ]) ?? $resolveAstrologerValue([
        'name',
        'full_name',
        'display_name',
    ], '')));
    if ($astrologerName === '') {
        $astrologerName = trim(
            ((string) ($resolveBookingValue(['astrologer_first_name']) ?? $resolveAstrologerValue(['first_name'], '')))
            . ' '
            . ((string) ($resolveBookingValue(['astrologer_last_name']) ?? $resolveAstrologerValue(['last_name'], '')))
        );
    }
    $astrologerEmail = trim((string) ($resolveBookingValue([
        'astrologer_email',
    ]) ?? $resolveAstrologerValue([
        'email',
        'user.email',
    ], '')));
    $astrologerPhone = trim((string) ($resolveBookingValue([
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
    $astrologerExperience = $resolveBookingValue([
        'astrologer_experience',
    ]) ?? $resolveAstrologerValue([
        'experience',
        'exp_in_years',
    ]);
    $astrologerLanguages = $formatBookingList($resolveBookingValue([
        'astrologer_languages',
    ]) ?? $resolveAstrologerValue([
        'languages',
        'language',
    ], []));
    $astrologerSkills = $formatBookingList($resolveBookingValue([
        'astrologer_skills',
        'astrologer_specializations',
    ]) ?? $resolveAstrologerValue([
        'skills',
        'specializations',
        'specialisations',
    ], []));
    $astrologerDesignation = trim((string) ($resolveBookingValue([
        'astrologer_designation',
        'astrologer_qualification',
    ]) ?? $resolveAstrologerValue([
        'designation',
        'qualification',
        'title',
    ], '')));
    $astrologerDisplayName = $astrologerName !== '' ? $astrologerName : 'Astrologer Consultant';
    $customerDisplayName = trim((string) data_get($bookingRoot, 'user.first_name') . ' ' . (string) data_get($bookingRoot, 'user.last_name'));
    if ($customerDisplayName === '') {
        $customerDisplayName = trim((string) ($booking['name'] ?? ''));
    }
    $noteDocumentLogo = asset('assets/images/Logo.png');
    $bookingDetailsPageData = isset($booking)
        ? [
            'bookingId' => $booking['id'],
            'customerJoinUrl' => route('customer.consultation.video', ['meetingId' => 'astro-' . $booking['id']]),
            'astrologerId' => (int) ($booking['astrologer_id'] ?? data_get($booking, 'astrologer.id') ?? data_get($booking, 'assigned_astrologer_id') ?? 0),
            'currentDate' => !empty($booking['scheduled_at']) ? \Carbon\Carbon::parse($booking['scheduled_at'])->format('Y-m-d') : null,
            'slotsUrl' => route('consultation.slots'),
            'rescheduleUrl' => route('booking.reschedule', ['id' => $booking['id']]),
            'canReschedule' => ! $isRescheduleDisabled,
        ]
        : null;
@endphp
<!-- <div class="container mt-4 inner_back">
    <div class="banner">
        <img src="{{ asset('assets/images/consult.png') }}" alt="Astrology Banner">
    </div>
</div> -->


<style>
    .booking-header-custom {
        background: #f98700;
        color: #fff;
        border-radius: 10px 10px 0 0;
        padding: 1.2rem 1.5rem 1rem 1.5rem;
        margin-bottom: 0;
        position: relative;
    }
    .booking-status-badge {
        position: absolute;
        top: 1.2rem;
        right: 1.5rem;
        background: #f5f8ef;
        color: #f98700;
        font-weight: 600;
        border-radius: 6px;
        padding: 0.35em 1.2em;
        font-size: 1em;
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
        border-radius: 0 0 10px 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        padding: 2rem 1.5rem 1.5rem 1.5rem;
        margin-bottom: 1.5rem;
    }
    .booking-details-row {
        display: flex;
        gap: 2rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }
    .booking-details-col {
        flex: 1 1 320px;
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1.2rem 1.2rem 1rem 1.2rem;
        min-width: 260px;
    }
    .booking-details-label {
        font-weight: 600;
        color: #222;
    }
    .booking-details-value {
        color: #444;
        margin-bottom: 0.3rem;
    }
    .booking-table-summary th, .booking-table-summary td {
        text-align: left;
        padding: 0.5rem 1rem;
        border: 1px solid #e0e0e0;
    }
    .booking-table-summary th {
        background: #f4f6fb;
        font-weight: 600;
    }
    .booking-table-summary {
        width: 100%;
        margin-bottom: 1.5rem;
        border-radius: 8px;
        overflow: hidden;
        border-collapse: separate;
        border-spacing: 0;
    }
    .booking-payment-row {
        display: flex;
        gap: 2rem;
        flex-wrap: wrap;
        align-items: flex-start;
    }
    .booking-payment-col {
        flex: 1 1 320px;
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1.2rem 1.2rem 1rem 1.2rem;
        min-width: 260px;
    }
    .booking-total {
        font-size: 1.5rem;
        font-weight: 700;
        color: #219150;
        text-align: right;
        margin-top: 0.5rem;
    }
    .booking-actions {
        display: flex;
        gap: 1rem;
        margin-top: 1.5rem;
        justify-content: flex-end;
    }
    .btn-theme-orange {
        background: #f98700;
        color: #fff;
        border: none;
    }
    .btn-theme-orange:hover, .btn-theme-orange:focus {
        background: #d97706;
        color: #fff;
    }
    .btn-theme-orange.disabled,
    .btn-theme-orange.is-loading {
        pointer-events: none;
        opacity: 0.8;
    }
    .btn.is-loading {
        pointer-events: none;
        opacity: 0.8;
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
    .booking-actions .btn[disabled],
    .booking-actions .btn.disabled {
        pointer-events: none;
        opacity: 0.65;
    }
    .note-document-paper {
        position: relative;
        width: 100%;
        max-width: 794px;
        margin: 0 auto;
        padding: 32px 34px 40px;
        background: #fff;
        border: 1px solid #d9d9d9;
        border-radius: 10px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
        color: #1f2937;
    }
    .note-document-heading {
        margin-bottom: 1rem;
    }
    .note-document-letterhead {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 0.85rem;
        padding-bottom: 0.8rem;
        border-bottom: 2px solid #d8d8d8;
    }
    .note-document-mark {
        width: 54px;
        flex: 0 0 54px;
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding-top: 0.15rem;
    }
    .note-document-mark img {
        width: 40px;
        height: 40px;
        object-fit: contain;
    }
    .note-document-brand {
        flex: 1 1 auto;
        min-width: 0;
    }
    .note-document-title {
        font-size: 1.18rem;
        font-weight: 700;
        color: #111827;
        text-transform: uppercase;
    }
    .note-document-doctor {
        font-size: 1rem;
        font-weight: 700;
        color: #111827;
        margin-top: 0.18rem;
        line-height: 1.3;
    }
    .note-document-subtitle {
        font-size: 0.8rem;
        color: #6b7280;
    }
    .note-document-summary {
        margin-top: 0.22rem;
        max-width: 540px;
        line-height: 1.55;
    }
    .note-document-contact {
        margin-top: 0.45rem;
        display: flex;
        flex-wrap: wrap;
        gap: 0.2rem 0.9rem;
        font-size: 0.8rem;
        color: #374151;
    }
    .note-document-contact span {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }
    .note-document-contact strong {
        color: #111827;
    }
    .note-document-meta {
        flex: 0 0 210px;
        text-align: right;
        font-size: 0.76rem;
        color: #4b5563;
        padding-top: 0.15rem;
    }
    .note-document-meta strong {
        display: block;
        color: #111827;
        font-size: 0.86rem;
        margin-bottom: 0.4rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }
    .note-document-meta-grid {
        display: grid;
        gap: 0.35rem;
    }
    .note-document-meta-label {
        color: #6b7280;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-size: 0.62rem;
    }
    .note-document-meta-value {
        color: #111827;
        font-weight: 600;
        text-align: right;
    }
    .note-document-fields {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(0, 1fr) minmax(0, 1fr) minmax(0, 1fr);
        gap: 0.8rem;
        align-items: end;
        margin-top: 0.6rem;
        padding-bottom: 0.6rem;
        border-bottom: 1px dashed #c7cdd6;
    }
    .note-document-field-label {
        display: block;
        font-size: 0.67rem;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 0.15rem;
    }
    .note-document-field-value {
        display: block;
        min-height: 1.35rem;
        padding-bottom: 0.18rem;
        border-bottom: 1px dotted #9ca3af;
        font-size: 0.88rem;
        color: #111827;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .note-document-specialties {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.9rem;
        padding-top: 0.7rem;
        margin-top: 0.65rem;
        border-top: 1px solid #e5e7eb;
    }
    .note-document-specialty-card-full {
        grid-column: 1 / -1;
    }
    .note-document-specialty-label {
        display: block;
        font-size: 0.64rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #6b7280;
        margin-bottom: 0.28rem;
    }
    .note-document-specialty-value {
        display: block;
        font-size: 0.88rem;
        line-height: 1.45;
        color: #111827;
        font-weight: 600;
    }
    .note-document-writing-area {
        position: relative;
        min-height: 760px;
        padding: 0.2rem 0 0;
    }
    .note-document-watermark {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        opacity: 0.09;
        pointer-events: none;
        user-select: none;
        z-index: 0;
    }
    .note-document-watermark img {
        width: 180px;
        height: 180px;
        object-fit: contain;
        filter: grayscale(1);
    }
    .note-document-rx {
        position: relative;
        z-index: 1;
        font-family: Georgia, 'Times New Roman', serif;
        font-size: 1.8rem;
        font-weight: 700;
        color: #111827;
        line-height: 1;
        margin-bottom: 0.45rem;
    }
    .note-document-body {
        position: relative;
        z-index: 1;
        font-size: 0.98rem;
        line-height: 2.05;
        word-break: break-word;
        padding: 0.15rem 0 0;
        min-height: 700px;
        background-image: repeating-linear-gradient(
            to bottom,
            transparent 0,
            transparent 31px,
            #eef2f7 31px,
            #eef2f7 32px
        );
    }
    .note-document-body p {
        margin-bottom: 1.1rem;
        padding: 0 0.15rem;
        background: #fff;
        display: inline;
        box-shadow: 0 0 0 6px #fff;
    }
    .note-document-empty {
        color: #6b7280;
        font-style: italic;
    }
    .note-document-footer {
        position: relative;
        z-index: 1;
        margin-top: 1rem;
        display: flex;
        justify-content: flex-end;
    }
    .note-document-footer-card {
        min-width: 220px;
        max-width: 280px;
        text-align: right;
        font-size: 0.74rem;
        line-height: 1.5;
        color: #4b5563;
    }
    .note-document-footer-name {
        font-size: 0.84rem;
        font-weight: 700;
        color: #111827;
    }
    .note-document-footer-line {
        display: block;
    }
    @media (max-width: 991.98px) {
        .note-document-paper {
            padding: 24px 18px;
        }
        .note-document-letterhead {
            flex-direction: column;
        }
        .note-document-summary {
            max-width: none;
        }
        .note-document-meta {
            flex-basis: auto;
            text-align: left;
            width: 100%;
        }
        .note-document-meta-value {
            text-align: left;
        }
        .note-document-fields,
        .note-document-specialties {
            grid-template-columns: 1fr;
        }
        .note-document-watermark img {
            width: 132px;
            height: 132px;
        }
        .note-document-footer {
            justify-content: flex-start;
        }
        .note-document-footer-card {
            min-width: 0;
            text-align: left;
        }
    }
</style>

<div class="container" style="max-width: 900px; margin: 40px auto;">
    @if(isset($booking))
        <div class="booking-header-custom">
            <div style="font-size:1.3rem;font-weight:600;"><i class="fa-solid fa-clipboard-list me-2"></i>Astro Consultation Details</div>
            <div style="font-size:0.98rem;opacity:0.95;">Booking ID : BKNG{{ $booking['id'] }}</div>
            <span class="booking-status-badge {{ $booking['status'] }}" id="booking-status-badge">{{ str_replace('_', ' ', ucfirst($booking['status'])) }}</span>
        </div>
        <div class="booking-section">
            <div class="booking-details-row">
                <div class="booking-details-col">
                    <div class="booking-details-label mb-2"><i class="fa-solid fa-user-astronaut me-1"></i> Astrologer Details</div>
                    <div class="booking-details-value"><b>Name :</b> {{ $booking['astrologer']['name'] ?? '-' }}</div>
                    <div class="booking-details-value"><b>Experience :</b> {{ $booking['astrologer']['experience'] ?? '-' }} Years</div>
                    <div class="booking-details-value"><b>Skills :</b> {{ collect($booking['astrologer']['skills'] ?? [])->pluck('name')->implode(', ') ?: '-' }}</div>
                    <div class="booking-details-value"><b>Languages :</b> {{ collect($booking['astrologer']['languages'] ?? [])->pluck('name')->implode(', ') ?: '-' }}</div>
                </div>
                <div class="booking-details-col">
                    <div class="booking-details-label mb-2"><i class="fa-solid fa-user me-1"></i> Your Information</div>
                    <div class="booking-details-value"><b>Name :</b> {{ $booking['name'] ?? '-' }}</div>
                    <div class="booking-details-value"><b>Email :</b> {{ $booking['email'] ?? '-' }}</div>
                    <div class="booking-details-value"><b>Phone :</b> {{ $booking['phone'] ?? '-' }}</div>
                </div>
            </div>
            <div class="mb-3">
                <div class="booking-details-label mb-2"><i class="fa-solid fa-list-ul me-1"></i> Consultation Summary</div>
                <table class="booking-table-summary">
                    <thead>
                        <tr>
                            <th>Astrologer</th>
                            <th>Consultation Type</th>
                            <th>Date</th>
                            <th>Slot</th>
                            <th>Duration</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $booking['astrologer']['name'] ?? '-' }}</td>
                            <td>{{ ucfirst($booking['consultation_type']) }}</td>
                            <td id="booking-scheduled-date-cell">{{ \Carbon\Carbon::parse($booking['scheduled_at'])->format('d F Y') }}</td>
                            <td id="booking-scheduled-slot-cell">{{ \Carbon\Carbon::parse($booking['scheduled_at'])->format('h:i A') }}@if(isset($booking['end_time'])) - {{ \Carbon\Carbon::parse($booking['end_time'])->format('h:i A') }}@endif</td>
                            <td>{{ $booking['duration'] ?? '-' }}{{ is_numeric($booking['duration'] ?? null) ? (intval($booking['duration']) >= 60 ? ' min' : ' min') : '' }}</td>
                            <td>₹{{ $booking['rate'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="booking-payment-row">
                <div class="booking-payment-col">
                    <div class="booking-details-label mb-2"><i class="fa-solid fa-credit-card me-1"></i> Payment Details</div>
                    <div class="booking-details-value"><b>Payment Method :</b> {{ ucfirst($booking['payment_method']) }}</div>
                    <div class="booking-details-value"><b>Transaction ID :</b> {{ $booking['razorpay_payment_id'] ?? $booking['transaction_id'] ?? '-' }}</div>
                    <div class="booking-details-value"><b>Status :</b> <span style="color:#219150;font-weight:600;">Paid</span></div>
                </div>
                <div class="flex-grow-1 d-flex flex-column align-items-end justify-content-between">
                    <div class="booking-details-label mb-2">Total Amount</div>
                    <div class="booking-total">₹{{ $booking['rate'] }}</div>
                </div>
            </div>
            <div class="booking-details-col mt-4" style="max-width:none;">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                    <div class="booking-details-label mb-0"><i class="fa-solid fa-note-sticky me-1"></i> Astrologer Note</div>
                    @if($isNoteFinalized)
                        <a href="{{ route('booking.notesPdf', ['id' => $booking['id']]) }}" id="booking-download-note-pdf-btn" class="btn btn-outline-secondary btn-sm">
                            <i class="fa-solid fa-file-arrow-down me-1"></i> Download PDF
                        </a>
                    @endif
                </div>
                @if($isNoteFinalized)
                    <div class="note-document-paper">
                        <div class="note-document-heading">
                            <div class="note-document-letterhead">
                                <div class="note-document-mark">
                                    <img src="{{ $noteDocumentLogo }}" alt="{{ config('app.name') }} logo">
                                </div>
                                <div class="note-document-brand">
                                    <div class="note-document-title">Astrologer Consultation Notes</div>
                                    <div class="note-document-doctor">
                                        {{ $astrologerDisplayName }}
                                        @if($astrologerDesignation !== '')
                                            <span class="note-document-subtitle d-block mt-1">{{ $astrologerDesignation }}</span>
                                        @endif
                                    </div>
                                    <div class="note-document-subtitle note-document-summary">Consultation summary, remedies, and post-session guidance</div>
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
                                    <div class="note-document-specialties">
                                        <div class="note-document-specialty-card">
                                            <div class="note-document-specialty-label">Experience</div>
                                            <div class="note-document-specialty-value">{{ ($astrologerExperience !== null && $astrologerExperience !== '') ? $astrologerExperience . ' years' : 'Not specified' }}</div>
                                        </div>
                                        <div class="note-document-specialty-card">
                                            <div class="note-document-specialty-label">Languages</div>
                                            <div class="note-document-specialty-value">{{ $astrologerLanguages !== '' ? $astrologerLanguages : 'Not specified' }}</div>
                                        </div>
                                        <div class="note-document-specialty-card note-document-specialty-card-full">
                                            <div class="note-document-specialty-label">Specializations</div>
                                            <div class="note-document-specialty-value">{{ $astrologerSkills !== '' ? $astrologerSkills : 'Not specified' }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="note-document-meta">
                                    <strong>Consultation Record</strong>
                                    <div class="note-document-meta-grid">
                                        <div>
                                            <span class="note-document-meta-label">Booking ID</span>
                                            <span class="note-document-meta-value d-block">BKNG{{ $booking['id'] }}</span>
                                        </div>
                                        <div>
                                            <span class="note-document-meta-label">Date</span>
                                            <span class="note-document-meta-value d-block">{{ !empty($booking['scheduled_at']) ? \Carbon\Carbon::parse($booking['scheduled_at'])->format('d M Y') : now()->format('d M Y') }}</span>
                                        </div>
                                        <div>
                                            <span class="note-document-meta-label">Time</span>
                                            <span class="note-document-meta-value d-block">{{ !empty($booking['scheduled_at']) ? \Carbon\Carbon::parse($booking['scheduled_at'])->format('h:i A') : '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="note-document-fields">
                                <div class="note-document-field">
                                    <span class="note-document-field-label">Name</span>
                                    <span class="note-document-field-value">{{ $customerDisplayName !== '' ? $customerDisplayName : 'Not provided' }}</span>
                                </div>
                                <div class="note-document-field">
                                    <span class="note-document-field-label">Consultation</span>
                                    <span class="note-document-field-value">{{ ucfirst($booking['consultation_type'] ?? 'Consultation') }}</span>
                                </div>
                                <div class="note-document-field">
                                    <span class="note-document-field-label">Date</span>
                                    <span class="note-document-field-value">{{ !empty($booking['scheduled_at']) ? \Carbon\Carbon::parse($booking['scheduled_at'])->format('d M Y') : '-' }}</span>
                                </div>
                                <div class="note-document-field">
                                    <span class="note-document-field-label">Time</span>
                                    <span class="note-document-field-value">{{ !empty($booking['scheduled_at']) ? \Carbon\Carbon::parse($booking['scheduled_at'])->format('h:i A') : '-' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="note-document-writing-area">
                            <div class="note-document-watermark" aria-hidden="true">
                                <img src="{{ $noteDocumentLogo }}" alt="">
                            </div>
                            <div class="note-document-rx">Astrological Advice</div>
                            <div class="note-document-body">
                                @php
                                    $finalizedNote = trim((string) ($booking['astrologer_note'] ?? ''));
                                @endphp
                                @if($finalizedNote !== '')
                                    {!! nl2br(e($finalizedNote)) !!}
                                @else
                                    <p class="note-document-empty mb-0">No astrologer note was provided for this appointment.</p>
                                @endif
                            </div>
                        </div>
                        <div class="note-document-footer">
                            <div class="note-document-footer-card">
                                <span class="note-document-footer-name">{{ $astrologerDisplayName }}</span>
                                @if($astrologerPhone !== '')
                                    <span class="note-document-footer-line">Phone: {{ $astrologerPhone }}</span>
                                @endif
                                @if($astrologerEmail !== '')
                                    <span class="note-document-footer-line">Email: {{ $astrologerEmail }}</span>
                                @endif
                                <span class="note-document-footer-line">Reference: BKNG{{ $booking['id'] }}</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="booking-details-value mb-0">No note has been shared by the astrologer yet.</div>
                @endif
            </div>
            <div class="booking-details-col mt-4" style="max-width:none;">
                <div class="booking-details-label mb-3"><i class="fa-solid fa-gem me-1"></i> Astrologer Suggested Products</div>
                @if((int) ($booking['final_confirmation_from_astrologer'] ?? 0) === 1 && !empty($suggestedProducts))
                    <div class="suggested-products-grid">
                        @foreach($suggestedProducts as $product)
                            <div class="suggested-product-card">
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
                                    <span class="badge bg-light text-dark border">Qty: {{ (int) ($product['quantity'] ?? 1) }}</span>
                                    @if(!empty($product['variation_id']))
                                        <span class="badge bg-light text-dark border">Variation: {{ $product['variation_id'] }}</span>
                                    @endif
                                </div>
                                @if(!empty($product['url']))
                                    <div class="mt-3">
                                        <a href="{{ $product['url'] }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary" @if(!empty($product['slug'])) data-product-slug="{{ $product['slug'] }}" @endif>
                                            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> View Product
                                        </a>
                                    </div>
                                @elseif(!empty($product['slug']))
                                    <div class="mt-3 text-muted small">Product Slug: {{ $product['slug'] }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="booking-details-value mb-0">No products have been suggested by the astrologer yet.</div>
                @endif
            </div>
            <div class="booking-actions">
                <a href="{{ route('my-bookings') }}" class="btn btn-light border"><i class="fa-solid fa-arrow-left me-1"></i> Back to My Bookings</a>
                <a href="{{ route('customer.consultation.video', ['meetingId' => 'astro-' . $booking['id']]) }}" id="booking-join-consultation-btn" class="btn btn-success{{ in_array(($booking['status'] ?? null), ['ready_to_start', 'in_progress'], true) ? '' : ' d-none' }}"><i class="fa-solid fa-video me-1"></i> Join Consultation</a>
                <button type="button" id="booking-reschedule-btn" class="btn btn-outline-danger{{ $isRescheduleDisabled ? ' disabled' : '' }}" @if($isRescheduleDisabled) disabled aria-disabled="true" title="Completed or in-progress bookings cannot be rescheduled." @endif><i class="fa-solid fa-calendar-days me-1"></i> Reschedule Booking</button>
                <a href="{{ route('booking.invoice.download', ['id' => $booking['id']]) }}" id="booking-download-invoice-btn" class="btn btn-theme-orange"><i class="fa-solid fa-file-invoice me-1"></i> Download Invoice</a>
            </div>
        </div>
    @else
        <div class="booking-section">
            <div class="alert alert-warning mt-3">Booking details not found.</div>
        </div>
    @endif
</div>
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
        const pageData = JSON.parse(document.getElementById('booking-details-page-data').textContent || '{}');
        const realtimePageDataEl = document.getElementById('global-live-consultation-data');
        const realtimePageData = realtimePageDataEl ? JSON.parse(realtimePageDataEl.textContent || '{}') : {};
        const bookingId = pageData.bookingId;
        const badge = document.getElementById('booking-status-badge');
        const joinBtn = document.getElementById('booking-join-consultation-btn');
        const invoiceBtn = document.getElementById('booking-download-invoice-btn');
        const notePdfBtn = document.getElementById('booking-download-note-pdf-btn');
        const scheduledDateCell = document.getElementById('booking-scheduled-date-cell');
        const scheduledSlotCell = document.getElementById('booking-scheduled-slot-cell');
        let pollingTimer = null;
        let hasHealthySocket = false;

        if (!bookingId || !badge) {
            return;
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

        function attachDownloadHandler(button, loadingHtml) {
            if (!button) {
                return;
            }

            const defaultButtonHtml = button.innerHTML;

            button.addEventListener('click', function(event) {
                let resetTimer;
                const downloadFrame = document.createElement('iframe');

                event.preventDefault();

                if (button.classList.contains('is-loading')) {
                    return;
                }

                button.classList.add('is-loading', 'disabled');
                button.setAttribute('aria-disabled', 'true');
                button.innerHTML = loadingHtml;

                downloadFrame.style.display = 'none';
                downloadFrame.src = button.href;
                document.body.appendChild(downloadFrame);

                function resetDownloadButton() {
                    button.classList.remove('is-loading', 'disabled');
                    button.removeAttribute('aria-disabled');
                    button.innerHTML = defaultButtonHtml;

                    window.setTimeout(function() {
                        if (downloadFrame.parentNode) {
                            downloadFrame.parentNode.removeChild(downloadFrame);
                        }
                    }, 1000);
                }

                downloadFrame.addEventListener('load', function() {
                    if (resetTimer) {
                        window.clearTimeout(resetTimer);
                    }

                    resetDownloadButton();
                });

                resetTimer = window.setTimeout(function() {
                    resetDownloadButton();
                }, 4000);
            });
        }

        attachDownloadHandler(invoiceBtn, '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Preparing Invoice');
        attachDownloadHandler(notePdfBtn, '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Preparing PDF');

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

        function formatStatus(status) {
            return String(status || 'pending')
                .replace(/_/g, ' ')
                .replace(/\b\w/g, function(char) {
                    return char.toUpperCase();
                });
        }

        function applyStatus(status) {
            const normalizedStatus = status === 'live' ? 'in_progress' : (status === 'ended' ? 'completed' : status);

            badge.className = 'booking-status-badge ' + normalizedStatus;

            if (normalizedStatus === 'pending') {
                badge.classList.add('pending');
            }

            badge.textContent = formatStatus(normalizedStatus);

            if (joinBtn) {
                joinBtn.classList.toggle('d-none', !['ready_to_start', 'in_progress'].includes(normalizedStatus));
            }
        }

        function refreshStatus() {
            fetch('/astrologer/appointments/' + bookingId + '/ajax-status', {
                headers: { 'Accept': 'application/json' }
            })
            .then(function(res) {
                return res.json();
            })
            .then(function(data) {
                if (data && data.success && data.status) {
                    applyStatus(data.status);
                }
            })
            .catch(function() {
                // Ignore transient polling failures on the booking details page.
            });
        }

        function startPolling() {
            if (pollingTimer) {
                return;
            }

            pollingTimer = window.setInterval(refreshStatus, 10000);
        }

        function stopPolling() {
            if (!pollingTimer) {
                return;
            }

            window.clearInterval(pollingTimer);
            pollingTimer = null;
        }

        function setSocketHealthyState(isHealthy) {
            hasHealthySocket = isHealthy;

            if (hasHealthySocket) {
                stopPolling();
                return;
            }

            if (!document.hidden) {
                refreshStatus();
            }

            startPolling();
        }

        function subscribeToRealtimeStatus() {
            if (!window.Echo || !realtimePageData.userId) {
                setSocketHealthyState(false);
                return;
            }

            window.Echo.private('consultation.user.' + realtimePageData.userId)
                .listen('.consultation.status.updated', function(event) {
                    if (Number(event.bookingId) !== Number(bookingId)) {
                        return;
                    }

                    applyStatus(event.status);
                });

            const connection = window.Echo.connector && window.Echo.connector.pusher
                ? window.Echo.connector.pusher.connection
                : null;

            if (!connection) {
                setSocketHealthyState(false);
                return;
            }

            if (connection.state === 'connected') {
                setSocketHealthyState(true);
            }

            connection.bind('connected', function() {
                setSocketHealthyState(true);
            });
            connection.bind('unavailable', function() {
                setSocketHealthyState(false);
            });
            connection.bind('disconnected', function() {
                setSocketHealthyState(false);
            });
            connection.bind('error', function() {
                setSocketHealthyState(false);
            });
        }

        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                stopPolling();
                return;
            }

            if (!hasHealthySocket) {
                refreshStatus();
                startPolling();
            }
        });

        subscribeToRealtimeStatus();

        if (!window.Echo || !realtimePageData.userId) {
            refreshStatus();
            startPolling();
        }
    });
    </script>
    @endif
@endsection
