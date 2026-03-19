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
                    <form id="raju-booking-form" method="POST" action="{{ route('booking.raju-maharaj.submit') }}">
                        @csrf
                        <div class="row g-3 mb-2">
                            <div class="col-md-6">
                                <label for="selected_date" class="form-label fw-semibold">Select Date</label>
                                <input type="date" id="selected_date" name="selected_date" class="form-control" min="{{ now()->toDateString() }}" max="{{ now()->addDays(45)->toDateString() }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="slot_id" class="form-label fw-semibold">Select Time Slot</label>
                                <select id="slot_id" name="slot_id" class="form-select" required>
                                    <option value="">-- Select a slot --</option>
                                    <!-- Slots will be loaded dynamically -->
                                </select>
                            </div>
                        </div>
                        <div class="row g-3 mb-2">
                            <div class="col-md-6">
                                <label for="user_name" class="form-label fw-semibold">Your Name</label>
                                <input type="text" id="user_name" name="user_name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="user_phone" class="form-label fw-semibold">Phone Number</label>
                                <input type="text" id="user_phone" name="user_phone" class="form-control" required>
                            </div>
                        </div>
                        <div class="row g-3 mb-2">
                            <div class="col-md-12">
                                <label for="user_email" class="form-label fw-semibold">Email (optional)</label>
                                <input type="email" id="user_email" name="user_email" class="form-control">
                            </div>
                        </div>
                        <div class="row g-3 mb-3 align-items-center">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Price</label>
                                <div id="price-display" class="fs-4 fw-bold text-primary">--</div>
                                <div id="urgency-message" class="text-sm mt-1"></div>
                            </div>
                        </div>
                        <div class="d-grid mt-3">
                            <button type="submit" class="btn btn-warning text-white fw-bold py-2" style="background: linear-gradient(135deg, #ff9800, #f57c00); border: none; font-size: 1.1rem; border-radius: 10px; box-shadow: 0 2px 8px #ff98004d;">Book Now <i class="fa-solid fa-arrow-right ms-2"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const priceDisplay = document.getElementById('price-display');
    const urgencyMessage = document.getElementById('urgency-message');
    const dateInput = document.getElementById('selected_date');
    const slotSelect = document.getElementById('slot_id');
    const rajuMaharajId = 9999; // TODO: Set actual astrologer ID

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
        slotSelect.innerHTML = '<option value="">Loading...</option>';
        const date = dateInput.value;
        if (!date) return;
        fetch(`/api/astrologer/${rajuMaharajId}/slots?date=${date}`)
            .then(res => res.json())
            .then(data => {
                slotSelect.innerHTML = '<option value="">-- Select a slot --</option>';
                if (data && data.slots && data.slots.length) {
                    data.slots.forEach(slot => {
                        const opt = document.createElement('option');
                        opt.value = slot.id;
                        opt.textContent = slot.label || slot.time || slot.id;
                        slotSelect.appendChild(opt);
                    });
                } else {
                    slotSelect.innerHTML = '<option value="">No slots available</option>';
                }
            })
            .catch(() => {
                slotSelect.innerHTML = '<option value="">Error loading slots</option>';
            });
    }
    // Disable dates > 45 days in the future
    dateInput.setAttribute('max', new Date(Date.now() + 45*24*60*60*1000).toISOString().split('T')[0]);
    // Initial price update
    updatePrice();
</script>
@endsection
