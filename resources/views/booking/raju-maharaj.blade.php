@extends('layouts.app')

@section('content')
<div class="rajumaharajDiv">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9">
            <div class="card shadow-lg border-0 mb-4" style="border-radius: 18px;">
                <div class="card-body p-4">
                    <div class="mb-3 text-center">
                        <span class="badge bg-warning text-dark fs-6 mb-2 px-3 py-2" style="border-radius: 12px;"><i class="fa-solid fa-star text-orange-500 me-1"></i> Premium Consultation with <span class="fw-bold">Raju Maharaj</span></span>
                        <h2 class="fw-bold mb-1" style="font-size:2rem; color:#f57c00;">Book Your Session</h2>
                        <p class="text-muted mb-0">Select a date and time slot for your premium consultation. Pricing is based on how soon you book.</p>
                    </div>
                    <div class="mb-4 p-3" style="background: linear-gradient(90deg, #fffbe6 60%, #fff1e6 100%); border-radius: 12px; border: 1px solid #ffe082;">
                        <h5 class="fw-semibold mb-2"><i class="fa-solid fa-indian-rupee-sign text-warning me-2"></i>Pricing Tiers</h5>
                        <ul class="mb-0 ps-3" style="list-style: disc;">
                            <li><span class="fw-bold">Within 2 days:</span> <span class="text-danger fw-bold">₹21,000</span> <span class="badge bg-danger ms-2">Highest urgency!</span></li>
                            <li><span class="fw-bold">Within 15 days:</span> <span class="text-warning fw-bold">₹11,000</span> <span class="badge bg-warning text-dark ms-2">Book soon for better price</span></li>
                            <li><span class="fw-bold">Within 45 days:</span> <span class="text-success fw-bold">₹5,000</span></li>
                            <li><span class="fw-bold">More than 45 days:</span> <span class="text-secondary">Not allowed</span></li>
                        </ul>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success mb-3">{{ session('success') }}</div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger mb-3">
                            <ul class="mb-0 ps-3" style="list-style: disc;">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Always show the booking form, even after success --}}

                    <!-- Multi-step form matching consultation form -->
                    <div class="container py-2">
                        <div class="row">
                            <div class="col-md-12">
                                <!-- Stepper removed as requested -->
                                <div id="stepper-error" style="display:none" class="alert alert-danger"></div>
                                <form id="raju-booking-form">
                                    <!-- Step 1: Personal Info -->
                                    <div class="form-section">
                                        <h4>Personal Information</h4>
                                        <div class="mb-3"><label>Name</label><input type="text" class="form-control" name="name" required></div>
                                        <div class="mb-3"><label>Email</label><input type="email" class="form-control" name="user_email" required></div>
                                        <div class="mb-3"><label>Phone</label><input type="tel" class="form-control" name="phone" required></div>
                                        <button type="button" class="btn btn-next next btn theme-btn btn-lg">Next</button>
                                    </div>
                                    <!-- Step 2: Birth Details -->
                                    <div class="form-section">
                                        <h4>Birth Details</h4>
                                        <div class="mb-3"><label>Date of Birth</label><input type="date" class="form-control" name="birth_date" required></div>
                                        <div class="mb-3"><label>Time of Birth</label><input type="time" class="form-control" name="birth_time" required></div>
                                        <div class="mb-3"><label>Place of Birth</label><input type="text" class="form-control" name="place"></div>
                                        <button type="button" class="btn btn-prev prev btn theme-btn btn-lg">Previous</button>
                                        <button type="button" class="btn btn-next next btn theme-btn btn-lg">Next</button>
                                    </div>
                                    <!-- Step 3: Consultation -->
                                    <div class="form-section">
                                        <h4>Consultation</h4>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Consultation Type</label>
                                                <select class="form-select" name="consultation_type" id="consultation_type">
                                                    <option value="video">Video Call</option>
                                                    <option value="phone">Phone Call</option>
                                                    <option value="inperson">In-person</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Astrologer</label>
                                                <input type="text" class="form-control" value="Raju Maharaj" readonly>
                                                <input type="hidden" name="astrologer_id" id="astrologer_id" value="15">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Session Duration</label>
                                                <input type="text" class="form-control" id="duration" name="duration" readonly>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Preferred Date</label>
                                                <input type="date" class="form-control" name="scheduled_at" id="consultation_date">
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label fw-semibold">Available Slots</label>
                                                <div id="slotGrid" class="d-flex flex-wrap gap-2 mb-2"></div>
                                                <input type="hidden" name="slot_id" id="slot_id">
                                                <div class="mt-1 text-muted small">Selected Slot: <strong id="slotText">None</strong></div>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Notes / Questions</label>
                                                <textarea class="form-control" name="notes" rows="3" placeholder="Briefly describe your concern"></textarea>
                                            </div>
                                            <div class="col-12">
                                                <button type="button" class="btn btn-prev prev btn theme-btn btn-lg">Previous</button>
                                                <button type="button" class="btn btn-next next btn theme-btn btn-lg">Next</button>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Step 4: Payment -->
                                    <div class="form-section">
                                        <h4>Payment</h4>
                                        <div class="mb-3">
                                            <label>Select Payment Method</label>
                                            <div class="form-check"><input class="form-check-input" type="radio" name="payment_method" value="upi"><label class="form-check-label">UPI</label></div>
                                            <div class="form-check"><input class="form-check-input" type="radio" name="payment_method" value="card"><label class="form-check-label">Credit / Debit Card</label></div>
                                            <div class="form-check"><input class="form-check-input" type="radio" name="payment_method" value="netbanking"><label class="form-check-label">Net Banking</label></div>
                                        </div>
                                        <button type="button" class="btn btn-prev prev btn theme-btn btn-lg">Previous</button>
                                        <button type="button" class="btn btn-next next btn theme-btn btn-lg">Next</button>
                                    </div>
                                    <!-- Step 5: Terms -->
                                    <div class="form-section">
                                        <h4>Terms & Conditions</h4>
                                        <div class="mb-3" style="max-height:150px; overflow:auto; background:white; color:black; padding:10px; border-radius:10px;">
                                            <p>By booking this consultation, you agree that astrology guidance is based on belief systems and should not replace professional medical, legal, or financial advice. Payments are non-refundable once the consultation is completed.</p>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" id="termsCheck" required>
                                            <label class="form-check-label">I agree to Terms & Conditions</label>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center gap-3">
                                            <button type="button" class="btn btn-prev prev btn theme-btn btn-lg">Previous</button>
                                            <input type="hidden" name="rate" id="rate">
                                            <button type="button" id="razorpay-pay-btn" class="btn btn-next ms-auto btn theme-btn btn-lg">Pay with Razorpay</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>


@push('styles')
<style>
    .theme-btn {
        background: linear-gradient(135deg, #ff9800, #f57c00) !important;
        color: #fff !important;
        border: none !important;
        font-weight: 600;
        border-radius: 10px !important;
        box-shadow: 0 2px 8px #ff98004d;
        transition: background 0.2s;
        padding: 0.75rem 2rem !important;
        font-size: 1.15rem !important;
        display: inline-block;
        min-width: 120px;
        text-align: center;
    }
    .theme-btn:hover, .theme-btn:focus {
        background: linear-gradient(135deg, #f57c00, #ff9800) !important;
        color: #fff !important;
    }
    .slot-badge {
        display: inline-block;
        min-width: 110px;
        padding: 8px 0;
        text-align: center;
        border: 1px solid #ffc107;
        border-radius: 8px;
        background: #fffbe6;
        color: #333;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.15s;
        box-shadow: 0 1px 4px #ffc10722;
        user-select: none;
    }
    .slot-badge.active {
        background: linear-gradient(135deg, #ff9800, #f57c00);
        color: #fff;
        border-color: #f57c00;
        font-weight: 600;
        box-shadow: 0 2px 8px #ff98004d;
    }
    .slot-badge:focus {
        outline: 2px solid #ffc107;
        outline-offset: 2px;
    }
    .form-section { display: none; }
    .form-section.active { display: block; }
</style>
@endpush

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- STEPPER LOGIC ---
    const sections = Array.from(document.querySelectorAll('.form-section'));
    const steps = Array.from(document.querySelectorAll('.step'));
    const nextBtns = document.querySelectorAll('.btn-next');
    const prevBtns = document.querySelectorAll('.btn-prev');
    let currentStep = 0;
    sections.forEach(section => section.classList.remove('active'));
    steps.forEach(step => step.classList.remove('active'));
    if (sections.length > 0) sections[0].classList.add('active');
    if (steps.length > 0) steps[0].classList.add('active');
    function showStep(step) {
        if (step < 0) step = 0;
        if (step >= sections.length) step = sections.length - 1;
        sections.forEach((section, idx) => {
            if (idx === step) {
                section.classList.add('active');
                section.style.display = 'block';
            } else {
                section.classList.remove('active');
                section.style.display = 'none';
            }
        });
        steps.forEach((stepEl, idx) => {
            if (idx === step) stepEl.classList.add('active');
            else stepEl.classList.remove('active');
        });
        window.scrollTo({ top: 0, behavior: 'smooth' });
        currentStep = step;
    }
    function validateStep(step) {
        let valid = true;
        const currentSection = sections[step];
        if (currentSection) {
            currentSection.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            currentSection.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
        }
        if (step === 0) {
            const name = document.querySelector('[name="name"]');
            const email = document.querySelector('[name="user_email"]');
            const phone = document.querySelector('[name="phone"]');
            if (!name.value.trim()) { showError(name, 'Name is required'); valid = false; }
            if (!email.value.trim() || !email.checkValidity()) { showError(email, 'Valid email required'); valid = false; }
            if (!phone.value.trim()) { showError(phone, 'Phone is required'); valid = false; }
        }
        if (step === 1) {
            const birthDate = document.querySelector('[name="birth_date"]');
            const birthTime = document.querySelector('[name="birth_time"]');
            if (!birthDate.value) { showError(birthDate, 'Birth date is required'); valid = false; }
            if (!birthTime.value) { showError(birthTime, 'Birth time is required'); valid = false; }
        }
        if (step === 2) {
            const consultationType = document.querySelector('[name="consultation_type"]');
            const duration = document.querySelector('[name="duration"]');
            const scheduledAt = document.querySelector('[name="scheduled_at"]');
            const slotId = document.querySelector('[name="slot_id"]');
            if (!consultationType.value) { showError(consultationType, 'Select consultation type'); valid = false; }
            if (!duration.value) { showError(duration, 'Duration required'); valid = false; }
            if (!scheduledAt.value) { showError(scheduledAt, 'Select date'); valid = false; }
            if (!slotId.value) { showError(slotId, 'Select a slot'); valid = false; }
        }
        if (step === 3) {
            const payment = document.querySelector('[name="payment_method"]:checked');
            if (!payment) {
                const radios = document.querySelectorAll('[name="payment_method"]');
                if (radios.length) showError(radios[0].closest('.form-check'), 'Select payment method');
                valid = false;
            }
        }
        if (step === 4) {
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
        if (input.parentNode) input.parentNode.appendChild(feedback);
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

    // --- DURATION FETCH ---
    const durationInput = document.getElementById('duration');
    if (durationInput) {
        fetch('/consultation/session-duration?astrologer_id=15')
            .then(res => res.json())
            .then(data => { if (data && data.duration) durationInput.value = data.duration; });
    }

    // --- SLOT FETCH ---
    const dateInput = document.getElementById('consultation_date');
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
    function fetchSlots() {
        const date = dateInput.value;
        clearSlotGrid();
        if (!date) return;
        showSlotSkeletons(5);
        fetch(`/api/astrologer/15/slots?date=${date}`)
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
    if (dateInput && slotGrid && slotIdInput && slotText) {
        dateInput.addEventListener('change', fetchSlots);
    }


    // --- PRICE LOGIC ---
    const rateInput = document.getElementById('rate');
    // Add price display UI
    let priceDisplay = document.getElementById('price-display');
    if (!priceDisplay) {
        priceDisplay = document.createElement('div');
        priceDisplay.id = 'price-display';
        priceDisplay.className = 'fs-4 fw-bold text-primary mb-2';
        const consultSection = sections[2];
        if (consultSection) consultSection.insertBefore(priceDisplay, consultSection.querySelector('.row.g-3'));
    }
    let urgencyMessage = document.getElementById('urgency-message');
    if (!urgencyMessage) {
        urgencyMessage = document.createElement('div');
        urgencyMessage.id = 'urgency-message';
        urgencyMessage.className = 'text-sm mt-1 mb-2';
        priceDisplay.parentNode.insertBefore(urgencyMessage, priceDisplay.nextSibling);
    }
    function updatePrice() {
        const today = new Date();
        const selected = new Date(dateInput.value);
        const diffTime = selected - today;
        const days = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        let price = null;
        let urgency = '';
        let color = 'text-primary';
        if (days < 0 || days > 45) {
            priceDisplay.textContent = 'Not allowed';
            priceDisplay.className = 'fs-4 fw-bold text-secondary mb-2';
            urgencyMessage.textContent = 'Booking is only allowed within 45 days.';
            price = '';
        } else if (days <= 2) {
            price = 21000;
            priceDisplay.textContent = '₹21,000';
            priceDisplay.className = 'fs-4 fw-bold text-danger mb-2';
            urgencyMessage.textContent = 'Highest urgency!';
        } else if (days <= 15) {
            price = 11000;
            priceDisplay.textContent = '₹11,000';
            priceDisplay.className = 'fs-4 fw-bold text-warning mb-2';
            urgencyMessage.textContent = 'Book soon for better price!';
        } else if (days <= 45) {
            price = 5000;
            priceDisplay.textContent = '₹5,000';
            priceDisplay.className = 'fs-4 fw-bold text-success mb-2';
            urgencyMessage.textContent = '';
        }
        if (rateInput) rateInput.value = price || '';
    }
    if (dateInput) dateInput.addEventListener('change', updatePrice);
    // Initial price update
    updatePrice();

    // --- RAZORPAY PAYMENT ---
    const razorpayBtn = document.getElementById('razorpay-pay-btn');
    function setRazorpayButtonLoading(isLoading) {
        if (!razorpayBtn) return;
        if (!razorpayBtn.dataset.defaultLabel) razorpayBtn.dataset.defaultLabel = razorpayBtn.innerHTML;
        razorpayBtn.disabled = isLoading;
        razorpayBtn.innerHTML = isLoading
            ? '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...'
            : razorpayBtn.dataset.defaultLabel;
    }
    function getBearerToken() {
        return localStorage.getItem('authToken') || '';
    }
    function toast(message, isError = false) {
        let toastEl = document.getElementById('checkout-toast');
        if (!toastEl) {
            toastEl = document.createElement('div');
            toastEl.id = 'checkout-toast';
            toastEl.style.position = 'fixed';
            toastEl.style.left = '50%';
            toastEl.style.bottom = '24px';
            toastEl.style.transform = 'translateX(-50%) translateY(20px)';
            toastEl.style.zIndex = '9999';
            toastEl.style.padding = '12px 18px';
            toastEl.style.borderRadius = '6px';
            toastEl.style.fontSize = '14px';
            toastEl.style.fontWeight = '500';
            toastEl.style.color = '#fff';
            toastEl.style.boxShadow = '0 6px 16px rgba(0,0,0,0.25)';
            toastEl.style.maxWidth = '90%';
            toastEl.style.textAlign = 'center';
            toastEl.style.opacity = '0';
            toastEl.style.transition = 'all 0.3s ease';
            document.body.appendChild(toastEl);
        }
        toastEl.textContent = message;
        toastEl.style.backgroundColor = isError ? '#e53935' : '#2e7d32';
        toastEl.style.opacity = '1';
        toastEl.style.transform = 'translateX(-50%) translateY(0)';
        clearTimeout(toastEl._hideTimer);
        toastEl._hideTimer = setTimeout(() => {
            toastEl.style.opacity = '0';
            toastEl.style.transform = 'translateX(-50%) translateY(20px)';
        }, 3000);
    }
    function redirectToBookingDetails(bookingId) {
        if (!bookingId) return;
        window.location.href = '/booking/' + encodeURIComponent(bookingId);
    }
    function resolveBookingId(payload) {
        if (!payload || typeof payload !== 'object') return null;
        return payload.booking_id || payload.id || payload.data?.id || payload.data?.booking_id || payload.data?.data?.id || payload.data?.data?.booking_id || null;
    }
    if (razorpayBtn) {
        razorpayBtn.addEventListener('click', function () {
            setRazorpayButtonLoading(true);
            const form = document.getElementById('raju-booking-form');
            const formData = new FormData(form);
            const bookingPayload = {
                name: formData.get('name'),
                phone: formData.get('phone'),
                email: formData.get('user_email'),
                user_email: formData.get('user_email'),
                consultation_type: formData.get('consultation_type'),
                astrologer_id: 15,
                date: formData.get('scheduled_at'),
                scheduled_at: formData.get('scheduled_at'),
                slot_id: formData.get('slot_id'),
                payment_method: 'razorpay',
                duration: formData.get('duration'),
                type: formData.get('consultation_type') || 'Online',
                rate: formData.get('rate'),
                birth_date: formData.get('birth_date'),
                birth_time: formData.get('birth_time'),
                place: formData.get('place'),
                notes: formData.get('notes'),
            };
            // Validate rate
            const rate = parseInt(bookingPayload.rate, 10);
            if (!bookingPayload.name || !bookingPayload.phone || !bookingPayload.email || !bookingPayload.consultation_type || !bookingPayload.astrologer_id || !bookingPayload.date || !bookingPayload.slot_id) {
                setRazorpayButtonLoading(false);
                toast('Please fill all required fields and select a slot.', true);
                return;
            }
            if (!rate || isNaN(rate) || rate < 100) {
                setRazorpayButtonLoading(false);
                toast('Invalid or missing price. Please select a valid date.', true);
                return;
            }
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
                if (!resp.success || !resp.data || !resp.data.data || !resp.data.data.id) {
                    setRazorpayButtonLoading(false);
                    toast(resp.message || 'Failed to create booking.', true);
                    return;
                }
                const bookingId = resp.data.data.id;
                // Razorpay expects amount in paise (multiply by 100)
                const amount = rate;
                const currency = resp.data.data.currency || 'INR';
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
                        key: orderResp.data.key || 'rzp_test_3WmknLIqcUo9er',
                        amount: orderResp.data.amount,
                        currency: orderResp.data.currency,
                        name: 'Astro Jyotish',
                        description: 'Consultation Booking',
                        order_id: orderResp.data.razorpay_order_id,
                        handler: function (response) {
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
                            ondismiss: function () { setRazorpayButtonLoading(false); }
                        },
                        prefill: {
                            name: bookingPayload.name,
                            email: bookingPayload.email,
                            contact: bookingPayload.phone
                        },
                        theme: { color: '#0d6efd' }
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
});
</script>
@endpush
@endsection
