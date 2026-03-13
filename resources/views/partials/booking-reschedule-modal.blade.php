@php
    $bookingRescheduleConfig = $bookingRescheduleConfig ?? null;
@endphp

@if(is_array($bookingRescheduleConfig))
    @php
        $modalId = $bookingRescheduleConfig['modalId'] ?? 'booking-reschedule-modal';
        $triggerId = $bookingRescheduleConfig['triggerId'] ?? 'booking-reschedule-btn';
        $dateInputId = $bookingRescheduleConfig['dateInputId'] ?? 'booking-reschedule-date';
        $slotInputId = $bookingRescheduleConfig['slotInputId'] ?? 'booking-reschedule-slot';
        $slotBadgesId = $bookingRescheduleConfig['slotBadgesId'] ?? 'booking-reschedule-slot-badges';
        $submitButtonId = $bookingRescheduleConfig['submitButtonId'] ?? 'booking-reschedule-submit';
        $alertId = $bookingRescheduleConfig['alertId'] ?? 'booking-reschedule-alert';
        $slotStateId = $bookingRescheduleConfig['slotStateId'] ?? 'booking-reschedule-slot-state';
        $dataScriptId = $bookingRescheduleConfig['dataScriptId'] ?? 'booking-reschedule-data';
        $title = $bookingRescheduleConfig['title'] ?? 'Reschedule Booking';
        $closeLabel = $bookingRescheduleConfig['closeLabel'] ?? 'Close';
        $submitLabel = $bookingRescheduleConfig['submitLabel'] ?? 'Confirm Reschedule';
    @endphp

    <style>
        .booking-reschedule-slot-state {
            font-size: 0.95rem;
            color: #6c757d;
        }
        .booking-reschedule-slot-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem;
            min-height: 2.5rem;
        }
        .booking-reschedule-slot-badge {
            border: 1px solid #f3c998;
            background: #fff8ef;
            color: #b45309;
            border-radius: 999px;
            padding: 0.45rem 0.85rem;
            font-size: 0.85rem;
            font-weight: 600;
            line-height: 1;
            cursor: pointer;
            transition: background-color 0.15s ease, border-color 0.15s ease, color 0.15s ease, transform 0.15s ease;
        }
        .booking-reschedule-slot-badge:hover,
        .booking-reschedule-slot-badge:focus {
            background: #ffedd5;
            border-color: #f59e0b;
            color: #9a3412;
            outline: none;
            transform: translateY(-1px);
        }
        .booking-reschedule-slot-badge.active {
            background: #f98700;
            border-color: #f98700;
            color: #fff;
            box-shadow: 0 6px 14px rgba(249, 135, 0, 0.22);
        }
        .booking-reschedule-slot-badge[disabled] {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
    </style>

    <div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa-solid fa-calendar-days me-2"></i>{{ $title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="{{ $alertId }}" class="alert d-none" role="alert"></div>
                    <div class="mb-3">
                        <label for="{{ $dateInputId }}" class="form-label">Select Date</label>
                        <input type="date" id="{{ $dateInputId }}" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label class="form-label d-block">Available Slot</label>
                        <input type="hidden" id="{{ $slotInputId }}" value="">
                        <div id="{{ $slotBadgesId }}" class="booking-reschedule-slot-badges" aria-live="polite">
                            <span class="badge text-bg-light border">Choose a date first</span>
                        </div>
                    </div>
                    <div id="{{ $slotStateId }}" class="booking-reschedule-slot-state">Select a new date to load available slots.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">{{ $closeLabel }}</button>
                    <button type="button" id="{{ $submitButtonId }}" class="btn btn-outline-danger" disabled>
                        <i class="fa-solid fa-calendar-check me-1"></i> {{ $submitLabel }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script id="{{ $dataScriptId }}" type="application/json">{!! json_encode($bookingRescheduleConfig, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const pageDataEl = document.getElementById(@json($dataScriptId));
        if (!pageDataEl) {
            return;
        }

        const pageData = JSON.parse(pageDataEl.textContent || '{}');
        const trigger = document.getElementById(pageData.triggerId || @json($triggerId));
        const modalEl = document.getElementById(pageData.modalId || @json($modalId));
        const dateInput = document.getElementById(pageData.dateInputId || @json($dateInputId));
        const slotInput = document.getElementById(pageData.slotInputId || @json($slotInputId));
        const slotBadges = document.getElementById(pageData.slotBadgesId || @json($slotBadgesId));
        const submitButton = document.getElementById(pageData.submitButtonId || @json($submitButtonId));
        const alertBox = document.getElementById(pageData.alertId || @json($alertId));
        const slotState = document.getElementById(pageData.slotStateId || @json($slotStateId));
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const modal = (window.bootstrap && modalEl) ? new bootstrap.Modal(modalEl) : null;
        let currentSlotRequestId = 0;

        if (!pageData.bookingId || !trigger || !modal || !dateInput || !slotInput || !slotBadges || !submitButton) {
            return;
        }

        function showAlert(type, message) {
            if (!alertBox) {
                return;
            }

            alertBox.className = 'alert alert-' + type;
            alertBox.textContent = message;
        }

        function hideAlert() {
            if (!alertBox) {
                return;
            }

            alertBox.className = 'alert d-none';
            alertBox.textContent = '';
        }

        function updateSubmitState() {
            submitButton.disabled = !dateInput.value || !slotInput.value;
        }

        function resetSlots(placeholder, stateText) {
            slotInput.value = '';
            slotBadges.innerHTML = '<span class="badge text-bg-light border">' + placeholder + '</span>';
            updateSubmitState();

            if (slotState) {
                slotState.textContent = stateText;
            }
        }

        function renderSlotBadges(slots) {
            slotInput.value = '';
            slotBadges.innerHTML = '';

            slots.forEach(function(slot) {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'booking-reschedule-slot-badge';
                button.textContent = slot.start_time + (slot.end_time ? ' - ' + slot.end_time : '');
                button.dataset.slotId = slot.slot_id;

                button.addEventListener('click', function() {
                    Array.prototype.forEach.call(slotBadges.querySelectorAll('.booking-reschedule-slot-badge'), function(badge) {
                        badge.classList.remove('active');
                    });

                    button.classList.add('active');
                    slotInput.value = slot.slot_id;
                    updateSubmitState();
                });

                slotBadges.appendChild(button);
            });
        }

        function loadSlots(dateValue) {
            const astrologerId = Number(pageData.astrologerId || 0);
            const requestId = ++currentSlotRequestId;

            hideAlert();

            if (!dateValue) {
                resetSlots('Choose a date first', 'Select a new date to load available slots.');
                return;
            }

            if (!astrologerId || !pageData.slotsUrl) {
                resetSlots('Slots unavailable', 'Unable to determine astrologer details for this booking.');
                showAlert('danger', 'Unable to load slots for this booking.');
                return;
            }

            resetSlots('Loading slots...', 'Fetching available slots...');

            fetch(pageData.slotsUrl + '?astrologer_id=' + encodeURIComponent(astrologerId) + '&date=' + encodeURIComponent(dateValue), {
                headers: { 'Accept': 'application/json' }
            })
            .then(function(res) {
                return res.json();
            })
            .then(function(data) {
                if (requestId !== currentSlotRequestId) {
                    return;
                }

                if (!data || !data.success || !Array.isArray(data.slots) || data.slots.length === 0) {
                    resetSlots('No slots available', 'No available slots were found for the selected date.');
                    return;
                }

                renderSlotBadges(data.slots);

                if (slotState) {
                    slotState.textContent = data.slots.length + ' slot(s) available for the selected date.';
                }

                updateSubmitState();
            })
            .catch(function() {
                if (requestId !== currentSlotRequestId) {
                    return;
                }

                resetSlots('Unable to load slots', 'An error occurred while loading slots.');
                showAlert('danger', 'Unable to load available slots right now. Please try again.');
            });
        }

        if (pageData.canReschedule) {
            trigger.addEventListener('click', function() {
                hideAlert();
                dateInput.value = pageData.currentDate || '';
                dateInput.min = new Date().toISOString().split('T')[0];
                resetSlots('Loading slots...', 'Fetching available slots...');
                modal.show();
                loadSlots(dateInput.value);
            });

            dateInput.addEventListener('change', function() {
                loadSlots(dateInput.value);
            });
        }

        const defaultSubmitHtml = submitButton.innerHTML;
        submitButton.addEventListener('click', function() {
            const payload = {
                booking_id: pageData.bookingId,
                date: dateInput.value,
                slot_id: slotInput.value
            };

            if (!payload.date || !payload.slot_id || !pageData.rescheduleUrl) {
                showAlert('danger', 'Please choose a new date and slot.');
                return;
            }

            hideAlert();
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Saving';

            fetch(pageData.rescheduleUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {})
                },
                body: JSON.stringify(payload)
            })
            .then(function(res) {
                return res.json().then(function(data) {
                    return { ok: res.ok, data: data };
                });
            })
            .then(function(result) {
                if (!result.ok || !result.data || !result.data.success) {
                    throw new Error(result.data && result.data.message ? result.data.message : 'Unable to reschedule booking.');
                }

                const selectedBadge = slotBadges.querySelector('.booking-reschedule-slot-badge.active');
                const slotLabel = selectedBadge ? selectedBadge.textContent : slotInput.value;
                const successEventName = pageData.successEventName || 'booking-reschedule:success';

                window.dispatchEvent(new CustomEvent(successEventName, {
                    detail: {
                        bookingId: pageData.bookingId,
                        date: payload.date,
                        slotId: payload.slot_id,
                        slotLabel: slotLabel,
                        message: result.data.message || 'Booking rescheduled successfully.'
                    }
                }));

                showAlert('success', result.data.message || 'Booking rescheduled successfully.');
                window.setTimeout(function() {
                    modal.hide();
                }, 900);
            })
            .catch(function(error) {
                showAlert('danger', error.message || 'Unable to reschedule booking right now.');
            })
            .finally(function() {
                submitButton.innerHTML = defaultSubmitHtml;
                updateSubmitState();
            });
        });
    });
    </script>
@endif
