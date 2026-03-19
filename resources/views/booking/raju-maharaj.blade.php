@extends('layouts.app')

@section('content')
<div class="container max-w-2xl mx-auto py-8">
    <div class="mb-6">
        <span class="inline-block bg-yellow-400 text-black px-3 py-1 rounded-full font-semibold text-sm mb-2">Premium Consultation with Raju Maharaj</span>
        <h2 class="text-2xl font-bold mb-2">Book Your Session</h2>
        <p class="text-gray-700">Select a date and time slot for your premium consultation. Pricing is based on how soon you book.</p>
    </div>

    <div class="mb-4 p-4 bg-blue-50 rounded">
        <h3 class="font-semibold mb-2">Pricing Tiers</h3>
        <ul class="space-y-1">
            <li><span class="font-bold">Within 2 days:</span> <span class="text-red-600 font-bold">₹21,000</span> <span class="ml-2 text-xs text-red-500">(Highest urgency!)</span></li>
            <li><span class="font-bold">Within 15 days:</span> <span class="text-orange-600 font-bold">₹11,000</span> <span class="ml-2 text-xs text-orange-500">(Book soon for better price)</span></li>
            <li><span class="font-bold">Within 45 days:</span> <span class="text-green-600 font-bold">₹5,000</span></li>
            <li><span class="font-bold">More than 45 days:</span> <span class="text-gray-500">Not allowed</span></li>
        </ul>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Always show the booking form, even after success --}}
    <form id="raju-booking-form" method="POST" action="{{ route('booking.raju-maharaj.submit') }}" class="space-y-4">
        @csrf
        <div>
            <label for="selected_date" class="block font-semibold">Select Date</label>
            <input type="date" id="selected_date" name="selected_date" class="form-input mt-1 block w-full" min="{{ now()->toDateString() }}" max="{{ now()->addDays(45)->toDateString() }}" required>
        </div>
        <div>
            <label for="slot_id" class="block font-semibold">Select Time Slot</label>
            <select id="slot_id" name="slot_id" class="form-select mt-1 block w-full" required>
                <option value="">-- Select a slot --</option>
                <!-- Slots will be loaded dynamically -->
            </select>
        </div>
        <div>
            <label for="user_name" class="block font-semibold">Your Name</label>
            <input type="text" id="user_name" name="user_name" class="form-input mt-1 block w-full" required>
        </div>
        <div>
            <label for="user_phone" class="block font-semibold">Phone Number</label>
            <input type="text" id="user_phone" name="user_phone" class="form-input mt-1 block w-full" required>
        </div>
        <div>
            <label for="user_email" class="block font-semibold">Email (optional)</label>
            <input type="email" id="user_email" name="user_email" class="form-input mt-1 block w-full">
        </div>
        <div>
            <label class="block font-semibold">Price</label>
            <div id="price-display" class="text-xl font-bold text-blue-700">--</div>
            <div id="urgency-message" class="text-sm mt-1"></div>
        </div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Book Now</button>
    </form>
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
