@extends('layouts.app')

@section('title', 'Book a Consultation')

@section('content')

@push('styles')
<style>
    .form-section.active {
        border: 2px solid #0d6efd;
        box-shadow: 0 0 8px #0d6efd33;
    }

</style>
@endpush

@push('styles')
<style>

</style>
@endpush
<div class="container py-5 book_ing">
    <div class="form-container">

        <div class="row">

            <div class="col-md-4 dri">
                <img src="{{ asset('assets/images/login.jpg') }}" />
            </div>

            <div class="col-md-8 cdser">
                <div class="row">
                    <!-- LEFT STEPPER -->
                    <div class="col-md-4">
                        <div class="step-list">

                            <div class="step active" data-step="0">
                                <div class="step-circle"><i class="bi bi-file-earmark-person"></i></div>
                                <div class="step-title">
                                    <h6>Personal Info</h6>
                                    <small>Enter details</small>
                                </div>
                            </div>

                            <div class="step" data-step="1">
                                <div class="step-circle"><i class="bi bi-cake2"></i></div>
                                <div class="step-title">
                                    <h6>Birth Details</h6>
                                    <small>Your birth info</small>
                                </div>
                            </div>

                            <div class="step" data-step="2">
                                <div class="step-circle"><i class="bi bi-people"></i></div>
                                <div class="step-title">
                                    <h6>Consultation</h6>
                                    <small>Select service</small>
                                </div>
                            </div>

                            <div class="step" data-step="3">
                                <div class="step-circle"><i class="bi bi-credit-card"></i></div>
                                <div class="step-title">
                                    <h6>Payment</h6>
                                    <small>Choose method</small>
                                </div>
                            </div>

                            <div class="step" data-step="4">
                                <div class="step-circle"><i class="bi bi-check2-square"></i></div>
                                <div class="step-title">
                                    <h6>Complete</h6>
                                    <small>Submit form</small>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- RIGHT FORM -->
                    <div class="col-md-7">

                        <div id="stepper-error" style="display:none" class="alert alert-danger"></div>
                        <div id="stepper-debug" style="display:none; margin-bottom:10px;" class="alert alert-info"></div>
                        <form id="consultation-booking-form">
                            <!-- Step 1 -->
                            <div class="form-section">
                                <h4>Personal Information</h4>

                                <div class="mb-3">
                                    <label>Name</label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>

                                <div class="mb-3">
                                    <label>Email</label>
                                    <input type="email" class="form-control" name="user_email" required>
                                </div>

                                <div class="mb-3">
                                    <label>Phone</label>
                                    <input type="tel" class="form-control" name="phone" required>
                                </div>

                                <button type="button" class="btn btn-next next">Next</button>
                            </div>

                            <!-- Step 2 -->
                            <div class="form-section">
                                <h4>Birth Details</h4>

                                <div class="mb-3">
                                    <label>Date of Birth</label>
                                    <input type="date" class="form-control" name="birth_date" required>
                                </div>

                                <div class="mb-3">
                                    <label>Time of Birth</label>
                                    <input type="time" class="form-control" name="birth_time" required>
                                </div>

                                <div class="mb-3">
                                    <label>Place of Birth</label>
                                    <input type="text" class="form-control" name="place">
                                </div>

                                <button type="button" class="btn btn-secondary btn-prev prev">Previous</button>
                                <button type="button" class="btn btn-next next">Next</button>
                            </div>

                            <!-- Step 3 -->
                            <div class="form-section">
                                <h4>Consultation</h4>


                                <div class="row g-3">
                                    <!-- Consultation Type -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Consultation Type</label>
                                        <select class="form-select" name="consultation_type" id="consultation_type">
                                            <option value="video">Video Call</option>
                                            <option value="phone">Phone Call</option>
                                            <option value="inperson">In-person</option>
                                        </select>
                                    </div>

                                    <!-- Astrologer -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Choose Astrologer</label>

                                        <select class="form-select" name="astrologer_id" id="astrologer_id">
                                            <option value="">-- Select Astrologer --</option>
                                            @if(isset($astrologers['data']) && is_array($astrologers['data']))
                                                @foreach($astrologers['data'] as $ast)
                                                    @if(is_array($ast) || is_object($ast))
                                                        <option value="{{ is_array($ast) ? ($ast['id'] ?? '') : ($ast->id ?? '') }}" data-duration="{{ is_array($ast) ? ($ast['duration'] ?? '') : ($ast->duration ?? '') }}" data-rate="{{ is_array($ast) ? ($ast['rate'] ?? '') : ($ast->rate ?? '') }}">
                                                            {{ is_array($ast) ? ($ast['name'] ?? '') : ($ast->name ?? '') }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    <!-- Session Duration -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Session Duration</label>
                                        <input type="text" class="form-control" id="duration" name="duration" readonly>
                                    </div>

                                    <!-- Preferred Date -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Preferred Date</label>
                                        <input type="date" class="form-control" name="scheduled_at" id="consultation_date">
                                    </div>

                                    <!-- Slots -->
                                    <div class="col-md-12">
                                        <label class="form-label fw-semibold">Available Slots</label>

                                        <div id="slotGrid" class="d-flex flex-wrap gap-2 mb-2"></div>
                                        <input type="hidden" name="slot_id" id="slot_id">
                                        <div class="mt-1 text-muted small">
                                            Selected Slot:
                                            <strong id="slotText">None</strong>
                                        </div>

                                    </div>

                                    <!-- Notes -->
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Notes / Questions</label>
                                        <textarea class="form-control" name="notes" rows="3" placeholder="Briefly describe your concern"></textarea>
                                    </div>
                                    <div class="col-12">
                                        <button type="button" class="btn btn-secondary btn-prev prev">Previous</button>
                                        <button type="button" class="btn btn-next next">Next</button>
                                    </div>
                                </div>


                            </div>








                            <!-- Step 4 -->
                            <div class="form-section">
                                <h4>Payment</h4>

                                <div class="mb-3">
                                    <label>Select Payment Method</label>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" value="upi">
                                        <label class="form-check-label">UPI</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" value="card">
                                        <label class="form-check-label">Credit / Debit Card</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" value="netbanking">
                                        <label class="form-check-label">Net Banking</label>
                                    </div>

                                </div>

                                <button type="button" class="btn btn-secondary btn-prev prev">Previous</button>
                                <button type="button" class="btn btn-next next">Next</button>
                            </div>

                            <!-- Step 5 -->
                            <div class="form-section">
                                <h4>Terms & Conditions</h4>

                                <div class="mb-3" style="max-height:150px; overflow:auto; background:white; color:black; padding:10px; border-radius:10px;">
                                    <p>
                                        By booking this consultation, you agree that astrology guidance is based on belief systems and should not replace professional medical, legal, or financial advice.
                                        Payments are non-refundable once the consultation is completed.
                                    </p>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="termsCheck" required>
                                    <label class="form-check-label">I agree to Terms & Conditions</label>
                                </div>

                                <div class="d-flex justify-content-between align-items-center gap-3">
                                    <button type="button" class="btn btn-secondary btn-prev prev">Previous</button>
                                    <input type="hidden" name="rate" id="rate">
                                    <button type="button" id="razorpay-pay-btn" class="btn btn-next ms-auto">Pay with Razorpay</button>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>
            </div>


        </div>
    </div>
</div>


@endsection



@push('scripts')
<!-- Razorpay Checkout Script -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>

document.addEventListener('DOMContentLoaded', function () {
    // Razorpay payment integration
    const razorpayBtn = document.getElementById('razorpay-pay-btn');
    const bookingDetailsBaseUrl = "{{ url('/booking') }}";
    let paymentSuccess = false;
    let paymentDetails = {};

    function setRazorpayButtonLoading(isLoading) {
        if (!razorpayBtn) {
            return;
        }

        if (!razorpayBtn.dataset.defaultLabel) {
            razorpayBtn.dataset.defaultLabel = razorpayBtn.innerHTML;
        }

        razorpayBtn.disabled = isLoading;
        razorpayBtn.innerHTML = isLoading
            ? '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...'
            : razorpayBtn.dataset.defaultLabel;
    }

    function redirectToBookingDetails(bookingId) {
        if (!bookingId) {
            return;
        }

        window.location.href = bookingDetailsBaseUrl + '/' + encodeURIComponent(bookingId);
    }

    function resolveBookingId(payload) {
        if (!payload || typeof payload !== 'object') {
            return null;
        }

        return payload.booking_id
            || payload.id
            || payload.data?.id
            || payload.data?.booking_id
            || payload.data?.data?.id
            || payload.data?.data?.booking_id
            || null;
    }

    // Helper: Get Bearer token from localStorage or meta (adjust as needed)
    function getBearerToken() {
        // Example: from localStorage
        return localStorage.getItem('authToken') || '';
    }

    if (razorpayBtn) {
        razorpayBtn.addEventListener('click', function () {
            setRazorpayButtonLoading(true);
            const form = document.getElementById('consultation-booking-form');
            const formData = new FormData(form);
            // Prepare booking payload
            const bookingPayload = {
                name: formData.get('name'),
                phone: formData.get('phone'),
                email: formData.get('user_email'),
                user_email: formData.get('user_email'), // required by backend
                consultation_type: formData.get('consultation_type'),
                astrologer_id: formData.get('astrologer_id'),
                date: formData.get('scheduled_at'),
                scheduled_at: formData.get('scheduled_at'), // required by backend
                slot_id: formData.get('slot_id'),
                payment_method: 'razorpay',
                duration: formData.get('duration'), // required by backend
                type: formData.get('consultation_type') || 'Online', // required by backend
                rate: formData.get('rate'), // required by backend
                birth_date: formData.get('birth_date'),
                birth_time: formData.get('birth_time'),
                place: formData.get('place'),
                notes: formData.get('notes'),
            };
            // Validate required fields
            if (!bookingPayload.name || !bookingPayload.phone || !bookingPayload.email || !bookingPayload.consultation_type || !bookingPayload.astrologer_id || !bookingPayload.date || !bookingPayload.slot_id) {
                setRazorpayButtonLoading(false);
                toast('Please fill all required fields and select a slot.', true);
                return;
            }
            // Step 1: Create Booking
            fetch('/api/v1/bookings', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + getBearerToken()
                },
                body: JSON.stringify(bookingPayload)
            })
            .then(res => res.json())
            .then(resp => {
                // Updated success logic for new backend response
                if (!resp.success || !resp.data || !resp.data.data || !resp.data.data.id) {
                    setRazorpayButtonLoading(false);
                    toast(resp.message || 'Failed to create booking.', true);
                    return;
                }
                const bookingId = resp.data.data.id;
                const amount = resp.data.data.rate;
                const currency = resp.data.data.currency || 'INR';
                // Step 2: Create Razorpay Order
                fetch('/api/v1/razorpay/order', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + getBearerToken()
                    },
                    body: JSON.stringify({ booking_id: bookingId, amount: amount, currency: currency })
                })
                .then(res => res.json())
                .then(orderResp => {
                    if (!orderResp.status || !orderResp.data || !orderResp.data.razorpay_order_id) {
                        setRazorpayButtonLoading(false);
                        toast(orderResp.message || 'Failed to initiate payment.', true);
                        return;
                    }
                    const options = {
                        key: orderResp.data.key || 'rzp_test_3WmknLIqcUo9er', // Replace with your Razorpay key
                        amount: orderResp.data.amount,
                        currency: orderResp.data.currency,
                        name: 'Astro Jyotish',
                        description: 'Consultation Booking',
                        order_id: orderResp.data.razorpay_order_id,
                        handler: function (response) {
                            // Step 3: Verify Payment
                            fetch('/api/v1/razorpay/verify', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'Authorization': 'Bearer ' + getBearerToken()
                                },
                                body: JSON.stringify({
                                    booking_id: bookingId,
                                    razorpay_order_id: response.razorpay_order_id,
                                    razorpay_payment_id: response.razorpay_payment_id,
                                    razorpay_signature: response.razorpay_signature
                                })
                            })
                            .then(res => res.json())
                            .then(verifyResp => {
                                if (verifyResp.status) {
                                    // Step 4: Confirm Booking
                                    fetch('/api/v1/razorpay/booking', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json',
                                            'Authorization': 'Bearer ' + getBearerToken()
                                        },
                                        body: JSON.stringify({
                                            booking_id: bookingId,
                                            razorpay_order_id: response.razorpay_order_id,
                                            razorpay_payment_id: response.razorpay_payment_id,
                                            razorpay_signature: response.razorpay_signature
                                        })
                                    })
                                    .then(res => res.json())
                                    .then(confirmResp => {
                                        if (confirmResp.status) {
                                            setRazorpayButtonLoading(false);
                                            toast('Booking and payment successful!');
                                            redirectToBookingDetails(bookingId);
                                        } else {
                                            setRazorpayButtonLoading(false);
                                            toast(confirmResp.message || 'Booking confirmation failed.', true);
                                        }
                                    })
                                    .catch(() => {
                                        setRazorpayButtonLoading(false);
                                        toast('Error confirming booking.', true);
                                    });
                                } else {
                                    setRazorpayButtonLoading(false);
                                    toast(verifyResp.message || 'Payment verification failed.', true);
                                }
                            })
                            .catch(() => {
                                setRazorpayButtonLoading(false);
                                toast('Payment verification error.', true);
                            });
                        },
                        modal: {
                            ondismiss: function () {
                                setRazorpayButtonLoading(false);
                            }
                        },
                        prefill: {
                            name: bookingPayload.name,
                            email: bookingPayload.email,
                            contact: bookingPayload.phone
                        },
                        theme: {
                            color: '#0d6efd'
                        }
                    };
                    const rzp = new Razorpay(options);
                    rzp.open();
                })
                .catch(() => {
                    setRazorpayButtonLoading(false);
                    toast('Error initiating payment.', true);
                });
            })
            .catch(() => {
                setRazorpayButtonLoading(false);
                toast('Error creating booking.', true);
            });
        });
    }
    const sections = Array.from(document.querySelectorAll('.form-section'));
    const steps = Array.from(document.querySelectorAll('.step'));
    const nextBtns = document.querySelectorAll('.btn-next');
    const prevBtns = document.querySelectorAll('.btn-prev');
    let currentStep = 0;
    // Remove .active from all sections and steps, then set only the first as active
    sections.forEach(section => section.classList.remove('active'));
    steps.forEach(step => step.classList.remove('active'));
    if (sections.length > 0) sections[0].classList.add('active');
    if (steps.length > 0) steps[0].classList.add('active');

    function showStep(step) {
        // Defensive: clamp step to available sections
        if (step < 0) step = 0;
        if (step >= sections.length) step = sections.length - 1;
        // Show only the current section
        sections.forEach((section, idx) => {
            if (idx === step) {
                section.classList.add('active');
            } else {
                section.classList.remove('active');
            }
        });
        // Update stepper UI
        const errorDiv = document.getElementById('stepper-error');
        if (sections.length !== steps.length) {
            if (errorDiv) {
                errorDiv.textContent = 'Form stepper mismatch: ' + sections.length + ' sections, ' + steps.length + ' steps. Please contact support.';
                errorDiv.style.display = 'block';
            }
        } else if (errorDiv) {
            errorDiv.style.display = 'none';
        }
        steps.forEach((stepEl, idx) => {
            if (idx === step) {
                stepEl.classList.add('active');
            } else {
                stepEl.classList.remove('active');
            }
        });
        window.scrollTo({ top: 0, behavior: 'smooth' });
        currentStep = step;
    }

    // --- FORM VALIDATION ---
    function validateStep(step) {
        let valid = true;
        // Only clear errors for current step
        const currentSection = sections[step];
        if (currentSection) {
            currentSection.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            currentSection.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
        }
        if (step === 0) {
            // Personal Info
            const name = document.querySelector('[name="name"]');
            const email = document.querySelector('[name="user_email"]');
            const phone = document.querySelector('[name="phone"]');
            if (!name.value.trim()) { showError(name, 'Name is required'); valid = false; }
            if (!email.value.trim() || !email.checkValidity()) { showError(email, 'Valid email required'); valid = false; }
            if (!phone.value.trim()) { showError(phone, 'Phone is required'); valid = false; }
        }
        if (step === 1) {
            // Birth Details
            const birthDate = document.querySelector('[name="birth_date"]');
            const birthTime = document.querySelector('[name="birth_time"]');
            if (!birthDate.value) { showError(birthDate, 'Birth date is required'); valid = false; }
            if (!birthTime.value) { showError(birthTime, 'Birth time is required'); valid = false; }
        }
        if (step === 2) {
            // Consultation
            const consultationType = document.querySelector('[name="consultation_type"]');
            const astrologer = document.querySelector('[name="astrologer_id"]');
            const duration = document.querySelector('[name="duration"]');
            const scheduledAt = document.querySelector('[name="scheduled_at"]');
            const slotId = document.querySelector('[name="slot_id"]');
            if (!consultationType.value) { showError(consultationType, 'Select consultation type'); valid = false; }
            if (!astrologer.value) { showError(astrologer, 'Select astrologer'); valid = false; }
            if (!duration.value) { showError(duration, 'Duration required'); valid = false; }
            if (!scheduledAt.value) { showError(scheduledAt, 'Select date'); valid = false; }
            if (!slotId.value) { showError(slotId, 'Select a slot'); valid = false; }
        }
        if (step === 3) {
            // Payment
            const payment = document.querySelector('[name="payment_method"]:checked');
            if (!payment) {
                const radios = document.querySelectorAll('[name="payment_method"]');
                if (radios.length) showError(radios[0].closest('.form-check'), 'Select payment method');
                valid = false;
            }
        }
        if (step === 4) {
            // Terms
            const terms = document.getElementById('termsCheck');
            if (!terms.checked) { showError(terms, 'You must agree to continue'); valid = false; }
        }
        return valid;
    }
    function showError(input, message) {
        if (!input) return;
        input.classList.add('is-invalid');
        let feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        feedback.textContent = message;
        if (input.parentNode) {
            if (input.type === 'checkbox' || input.type === 'radio') {
                input.parentNode.appendChild(feedback);
            } else {
                input.parentNode.appendChild(feedback);
            }
        }
    }

    nextBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            if (validateStep(currentStep)) {
                if (currentStep < sections.length - 1) {
                    currentStep++;
                    showStep(currentStep);
                }
            }
        });
    });

    prevBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            if (currentStep > 0) {
                currentStep--;
                showStep(currentStep);
            }
        });
    });

    showStep(currentStep);

    // Populate session duration when astrologer is selected
    const astrologerSelect = document.getElementById('astrologer_id');
    const durationInput = document.getElementById('duration');
    const dateInput = document.getElementById('consultation_date');
    const slotSelect = document.getElementById('slot_id');

    if (astrologerSelect && durationInput) {
        astrologerSelect.addEventListener('change', function () {
            const astrologerId = astrologerSelect.value;
            const selectedOption = astrologerSelect.options[astrologerSelect.selectedIndex];
            // Set rate from astrologer option data-rate attribute
            const rateInput = document.getElementById('rate');
            if (selectedOption && rateInput) {
                const rate = selectedOption.getAttribute('data-rate') || '';
                rateInput.value = rate;
            }
            if (!astrologerId) {
                durationInput.value = '';
                if (slotSelect) slotSelect.innerHTML = '<option value="">Select Slot</option>';
                return;
            }
            fetch(`/consultation/session-duration?astrologer_id=${astrologerId}`)
                .then(res => res.json())
                .then(data => {
                    if (data && data.duration) {
                        durationInput.value = data.duration;
                    } else {
                        durationInput.value = '';
                    }
                })
                .catch(() => {
                    durationInput.value = '';
                });
            // Clear slots when astrologer changes
            if (slotSelect) slotSelect.innerHTML = '<option value="">Select Slot</option>';
        });
    }

    // Fetch slots when both astrologer and date are selected
    const slotGrid = document.getElementById('slotGrid');
    const slotIdInput = document.getElementById('slot_id');
    const slotText = document.getElementById('slotText');
    function clearSlotGrid() {
        if (slotGrid) slotGrid.innerHTML = '';
        if (slotIdInput) slotIdInput.value = '';
        if (slotText) slotText.textContent = 'None';
    }
    function showSlotSkeletons(count = 5) {
        if (!slotGrid) return;
        slotGrid.innerHTML = '';
        for (let i = 0; i < count; i++) {
            const skel = document.createElement('div');
            skel.className = 'slot-skeleton';
            slotGrid.appendChild(skel);
        }
    }
    if (astrologerSelect && dateInput && slotGrid && slotIdInput && slotText) {
        function fetchSlots() {
            const astrologerId = astrologerSelect.value;
            const date = dateInput.value;
            clearSlotGrid();
            if (!astrologerId || !date) {
                return;
            }
            showSlotSkeletons(5);
            fetch(`/consultation/slots?astrologer_id=${astrologerId}&date=${date}`)
                .then(res => res.json())
                .then(data => {
                    slotGrid.innerHTML = '';
                    if (data.success && Array.isArray(data.slots) && data.slots.length > 0) {
                        data.slots.forEach(slot => {
                            const badge = document.createElement('span');
                            badge.className = 'slot-badge';
                            badge.tabIndex = 0;
                            badge.textContent = `${slot.start_time} - ${slot.end_time}`;
                            badge.dataset.slotId = slot.slot_id;
                            badge.setAttribute('role', 'button');
                            badge.setAttribute('aria-pressed', 'false');
                            badge.addEventListener('click', function () {
                                slotGrid.querySelectorAll('.slot-badge').forEach(b => {
                                    b.classList.remove('active');
                                    b.setAttribute('aria-pressed', 'false');
                                });
                                badge.classList.add('active');
                                badge.setAttribute('aria-pressed', 'true');
                                slotIdInput.value = slot.slot_id;
                                slotText.textContent = `${slot.start_time} - ${slot.end_time}`;
                            });
                            badge.addEventListener('keydown', function(e) {
                                if (e.key === 'Enter' || e.key === ' ') {
                                    e.preventDefault();
                                    badge.click();
                                }
                            });
                            slotGrid.appendChild(badge);
                        });
                    } else {
                        slotGrid.innerHTML = '<span class="text-danger">No slots available</span>';
                    }
                })
                .catch(() => {
                    slotGrid.innerHTML = '<span class="text-danger">Error loading slots</span>';
                });
        }
        astrologerSelect.addEventListener('change', fetchSlots);
        dateInput.addEventListener('change', fetchSlots);
    }

    // --- FINAL FORM SUBMIT VALIDATION ---
    const form = document.getElementById('consultation-booking-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!validateStep(0) || !validateStep(1) || !validateStep(2) || !validateStep(3) || !validateStep(4)) {
                showStep(0);
                return false;
            }
            // Prepare data
            const formData = new FormData(form);
            // Map frontend fields to API fields (match controller validation)
            const data = {
                name: formData.get('name'),
                phone: formData.get('phone'),
                user_email: formData.get('user_email'), // must match backend validation
                consultation_type: formData.get('consultation_type'),
                astrologer_id: formData.get('astrologer_id'),
                duration: formData.get('duration'),
                scheduled_at: formData.get('scheduled_at'),
                slot_id: formData.get('slot_id'),
                birth_date: formData.get('birth_date'),
                birth_time: formData.get('birth_time'),
                place: formData.get('place'),
                notes: formData.get('notes'),
                payment_method: formData.get('payment_method'),
                type: formData.get('consultation_type') || 'Online', // required by backend
                rate: formData.get('rate'),
            };
            // Show loading state
            const errorDiv = document.getElementById('stepper-error');
            if (errorDiv) {
                errorDiv.style.display = 'none';
            }
            // CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            fetch('/consultation/book', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json', // Ensure Laravel returns JSON on validation errors
                    ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {})
                },
                body: JSON.stringify(data)
            })
            .then(async response => {
                let resp;
                let rawText = '';
                try {
                    rawText = await response.text();
                    resp = JSON.parse(rawText);
                } catch (err) {
                    resp = { message: 'Invalid JSON response', error: err, raw: rawText };
                }
                if (response.ok && resp.success) {
                    const bookingId = resolveBookingId(resp);

                    if (bookingId) {
                        redirectToBookingDetails(bookingId);
                        return;
                    }

                    alert('Consultation booked successfully!');
                    form.reset();
                    showStep(0);
                } else {
                    if (errorDiv) {
                        let msg = (resp && resp.message ? resp.message : 'Booking failed.');
                        // Show Laravel validation errors if present
                        if (resp && resp.errors && typeof resp.errors === 'object') {
                            msg += '\n';
                            for (const [field, errors] of Object.entries(resp.errors)) {
                                if (Array.isArray(errors)) {
                                    errors.forEach(e => { msg += `- ${e}\n`; });
                                } else {
                                    msg += `- ${errors}\n`;
                                }
                            }
                        }
                        if (resp && resp.error) msg += ' (JSON error: ' + resp.error + ')';
                        if (resp && resp.raw) msg += '\nRaw response: ' + resp.raw.substring(0, 300);
                        errorDiv.textContent = msg;
                        errorDiv.style.display = 'block';
                    } else {
                        alert((resp && resp.message ? resp.message : 'Booking failed.'));
                    }
                    // Log the full response for debugging
                    console.error('Booking error response:', resp, response.status, response.statusText);
                }
            })
            .catch((err) => {
                if (errorDiv) {
                    errorDiv.textContent = 'An error occurred while booking. ' + (err && err.message ? err.message : '');
                    errorDiv.style.display = 'block';
                } else {
                    alert('An error occurred while booking. ' + (err && err.message ? err.message : ''));
                }
                // Log the error for debugging
                console.error('Booking AJAX error:', err);
            });
        });
    }
});



let toastEl = null;
function toast(message, isError = false) {
    if (!message) return;

    if (!toastEl) {
        toastEl = document.createElement("div");
        toastEl.id = "checkout-toast";

        toastEl.style.position = "fixed";
        toastEl.style.left = "50%";
        toastEl.style.bottom = "24px";
        toastEl.style.transform = "translateX(-50%) translateY(20px)";
        toastEl.style.zIndex = "9999";
        toastEl.style.padding = "12px 18px";
        toastEl.style.borderRadius = "6px";
        toastEl.style.fontSize = "14px";
        toastEl.style.fontWeight = "500";
        toastEl.style.color = "#fff";
        toastEl.style.boxShadow = "0 6px 16px rgba(0,0,0,0.25)";
        toastEl.style.maxWidth = "90%";
        toastEl.style.textAlign = "center";
        toastEl.style.opacity = "0";
        toastEl.style.transition = "all 0.3s ease";

        document.body.appendChild(toastEl);
    }

    toastEl.textContent = message;
    toastEl.style.backgroundColor = isError ? "#e53935" : "#2e7d32";

    // show animation
    toastEl.style.opacity = "1";
    toastEl.style.transform = "translateX(-50%) translateY(0)";

    clearTimeout(toastEl._hideTimer);

    toastEl._hideTimer = setTimeout(() => {
        toastEl.style.opacity = "0";
        toastEl.style.transform = "translateX(-50%) translateY(20px)";
    }, 3000);
}
</script>
@endpush



