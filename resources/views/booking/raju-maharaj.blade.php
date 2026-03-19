@extends('layouts.app')

@section('content')

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
                                <div class="step-list d-flex justify-content-center mb-4">
                                    <div class="step active" data-step="0"><div class="step-circle"><i class="bi bi-file-earmark-person"></i></div><div class="step-title"><h6>Personal Info</h6></div></div>
                                    <div class="step" data-step="1"><div class="step-circle"><i class="bi bi-cake2"></i></div><div class="step-title"><h6>Birth Details</h6></div></div>
                                    <div class="step" data-step="2"><div class="step-circle"><i class="bi bi-people"></i></div><div class="step-title"><h6>Consultation</h6></div></div>
                                    <div class="step" data-step="3"><div class="step-circle"><i class="bi bi-credit-card"></i></div><div class="step-title"><h6>Payment</h6></div></div>
                                    <div class="step" data-step="4"><div class="step-circle"><i class="bi bi-check2-square"></i></div><div class="step-title"><h6>Complete</h6></div></div>
                                </div>
                                <div id="stepper-error" style="display:none" class="alert alert-danger"></div>
                                <form id="raju-booking-form">
                                    <!-- Step 1: Personal Info -->
                                    <div class="form-section">
                                        <h4>Personal Information</h4>
                                        <div class="mb-3"><label>Name</label><input type="text" class="form-control" name="name" required></div>
                                        <div class="mb-3"><label>Email</label><input type="email" class="form-control" name="user_email" required></div>
                                        <div class="mb-3"><label>Phone</label><input type="tel" class="form-control" name="phone" required></div>
                                        <button type="button" class="btn btn-next next">Next</button>
                                    </div>
                                    <!-- Step 2: Birth Details -->
                                    <div class="form-section">
                                        <h4>Birth Details</h4>
                                        <div class="mb-3"><label>Date of Birth</label><input type="date" class="form-control" name="birth_date" required></div>
                                        <div class="mb-3"><label>Time of Birth</label><input type="time" class="form-control" name="birth_time" required></div>
                                        <div class="mb-3"><label>Place of Birth</label><input type="text" class="form-control" name="place"></div>
                                        <button type="button" class="btn btn-secondary btn-prev prev">Previous</button>
                                        <button type="button" class="btn btn-next next">Next</button>
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
                                                <button type="button" class="btn btn-secondary btn-prev prev">Previous</button>
                                                <button type="button" class="btn btn-next next">Next</button>
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
                                        <button type="button" class="btn btn-secondary btn-prev prev">Previous</button>
                                        <button type="button" class="btn btn-next next">Next</button>
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
    </div>
</div>

@push('styles')
<style>
    #slot-list {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 0.5rem;
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
    .slot-badge.active, .slot-badge.btn-primary {
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
</style>
@endpush

<script>
    const priceDisplay = document.getElementById('price-display');
    const urgencyMessage = document.getElementById('urgency-message');
    const dateInput = document.getElementById('selected_date');
    const slotList = document.getElementById('slot-list');
    const slotInput = document.getElementById('slot_id');
    const slotError = document.getElementById('slot-error');
    const rajuMaharajId = 15; // TODO: Set actual astrologer ID

    function getPrice(days) {
        if (days < 0 || days > 45) return null;
        if (days <= 2) return 21000;
        if (days <= 15) return 11000;
        if (days <= 45) return 5000;
        return null;
    }
    function getUrgencyMsg(days) {
        if (days <= 2) return 'Highest urgency!';
        if (days <= 15) return 'Book soon for better price!';
        if (days <= 45) return '';
        return '';
    }
    function updatePrice() {
        const today = new Date();
        const selected = new Date(dateInput.value);
        const diffTime = selected - today;
        const days = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        const price = getPrice(days);
        if (price === null) {
            priceDisplay.textContent = 'Not allowed';
            priceDisplay.className = 'text-gray-500 font-bold';
            urgencyMessage.textContent = 'Booking is only allowed within 45 days.';
        } else {
            priceDisplay.textContent = '₹' + price.toLocaleString();
            if (days <= 2) priceDisplay.className = 'text-red-600 font-bold text-xl';
            else if (days <= 15) priceDisplay.className = 'text-orange-600 font-bold text-xl';
            else priceDisplay.className = 'text-green-600 font-bold text-xl';
            urgencyMessage.textContent = getUrgencyMsg(days);
        }
    }
    dateInput.addEventListener('change', function() {
        updatePrice();
        loadSlots();
    });
    function loadSlots() {
        slotList.innerHTML = '<div class="text-muted small">Loading...</div>';
        slotInput.value = '';
        document.getElementById('slotText').textContent = 'None';
        const date = dateInput.value;
        if (!date) {
            slotList.innerHTML = '<div class="text-muted small">Select a date to view slots</div>';
            return;
        }
        fetch(`/api/astrologer/${rajuMaharajId}/slots?date=${date}`)
            .then(res => res.json())
            .then(data => {
                slotList.innerHTML = '';
                if (data.slots && Array.isArray(data.slots) && data.slots.length > 0) {
                    data.slots.forEach(slot => {
                        const badge = document.createElement('span');
                        badge.className = 'slot-badge btn btn-outline-primary';
                        badge.tabIndex = 0;
                        badge.textContent = slot.end_time ? `${slot.start_time} - ${slot.end_time}` : slot.start_time;
                        badge.dataset.slotId = slot.slot_id;
                        badge.setAttribute('role', 'button');
                        badge.setAttribute('aria-pressed', 'false');
                        badge.onclick = function() {
                            slotList.querySelectorAll('.slot-badge').forEach(b => {
                                b.classList.remove('active', 'btn-primary');
                                b.setAttribute('aria-pressed', 'false');
                            });
                            badge.classList.add('active', 'btn-primary');
                            badge.setAttribute('aria-pressed', 'true');
                            slotInput.value = slot.slot_id;
                            document.getElementById('slotText').textContent = badge.textContent;
                            slotError.style.display = 'none';
                        };
                        badge.onkeydown = function(e) {
                            if (e.key === 'Enter' || e.key === ' ') {
                                e.preventDefault();
                                badge.click();
                            }
                        };
                        slotList.appendChild(badge);
                    });
                } else {
                    slotList.innerHTML = '<div class="text-danger small">No slots available</div>';
                }
            })
            .catch(() => {
                slotList.innerHTML = '<div class="text-danger small">Error loading slots</div>';
            });
    }

    // Validate slot selection on submit
    document.getElementById('raju-booking-form').addEventListener('submit', function(e) {
        if (!slotInput.value) {
            slotError.style.display = 'block';
            e.preventDefault();
        } else {
            slotError.style.display = 'none';
        }
    });
    // Disable dates > 45 days in the future
    dateInput.setAttribute('max', new Date(Date.now() + 45*24*60*60*1000).toISOString().split('T')[0]);
    // Initial price update
    updatePrice();
</script>
@endsection
