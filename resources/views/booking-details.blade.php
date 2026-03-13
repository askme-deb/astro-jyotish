@extends('layouts.app')

@section('content')
@php
    $rescheduleBlockedStatuses = config('booking.reschedule_blocked_statuses', []);
    $isRescheduleDisabled = isset($booking) && in_array($booking['status'] ?? null, $rescheduleBlockedStatuses, true);
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
                <div class="booking-details-label mb-2"><i class="fa-solid fa-note-sticky me-1"></i> Astrologer Note</div>
                <div class="booking-details-value mb-0" style="white-space:pre-line;">{{ (int) ($booking['final_confirmation_from_astrologer'] ?? 0) === 1 ? ($booking['astrologer_note'] ?? 'No note has been shared by the astrologer yet.') : 'No note has been shared by the astrologer yet.' }}</div>
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
        const bookingId = pageData.bookingId;
        const badge = document.getElementById('booking-status-badge');
        const joinBtn = document.getElementById('booking-join-consultation-btn');
        const invoiceBtn = document.getElementById('booking-download-invoice-btn');
        const scheduledDateCell = document.getElementById('booking-scheduled-date-cell');
        const scheduledSlotCell = document.getElementById('booking-scheduled-slot-cell');

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

        if (invoiceBtn) {
            const defaultInvoiceHtml = invoiceBtn.innerHTML;

            invoiceBtn.addEventListener('click', function(event) {
                let resetTimer;
                const downloadFrame = document.createElement('iframe');

                event.preventDefault();

                if (invoiceBtn.classList.contains('is-loading')) {
                    return;
                }

                invoiceBtn.classList.add('is-loading', 'disabled');
                invoiceBtn.setAttribute('aria-disabled', 'true');
                invoiceBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Preparing Invoice';

                downloadFrame.style.display = 'none';
                downloadFrame.src = invoiceBtn.href;
                document.body.appendChild(downloadFrame);

                function resetInvoiceButton() {
                    invoiceBtn.classList.remove('is-loading', 'disabled');
                    invoiceBtn.removeAttribute('aria-disabled');
                    invoiceBtn.innerHTML = defaultInvoiceHtml;

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

                    resetInvoiceButton();
                });

                resetTimer = window.setTimeout(function() {
                    resetInvoiceButton();
                }, 4000);
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

        function formatStatus(status) {
            return String(status || 'pending')
                .replace(/_/g, ' ')
                .replace(/\b\w/g, function(char) {
                    return char.toUpperCase();
                });
        }

        function applyStatus(status) {
            badge.className = 'booking-status-badge ' + status;

            if (status === 'pending') {
                badge.classList.add('pending');
            }

            badge.textContent = formatStatus(status);

            if (joinBtn) {
                joinBtn.classList.toggle('d-none', !['ready_to_start', 'in_progress'].includes(status));
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

        refreshStatus();
        setInterval(refreshStatus, 10000);
    });
    </script>
    @endif
@endsection
