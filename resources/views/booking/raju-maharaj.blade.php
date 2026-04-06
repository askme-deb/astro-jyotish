@extends('layouts.app')

@section('content')
<div class="rm-page">
    <div class="rm-shell">
        <aside class="rm-sidebar">
            <div class="rm-portrait-wrap">
                <img src="https://astrorajumaharaj.com/assets/images/about-3.png" alt="Raju Maharaj">
                <div class="rm-live-pill">
                    <span class="rm-live-dot"></span>
                    Accepting Bookings
                </div>
            </div>

            <div class="rm-sidebar-copy">
                {{-- <div class="rm-stars">★★★★★</div>
                <div class="rm-reviews">4.9 · 2300+ reviews</div> --}}
                <h1 class="rm-name">Raju Maharaj</h1>
                <div class="rm-role">Vedic Astrologer &amp; Spiritual Guide</div>
                <p class="rm-description">Renowned for precise birth-chart readings, Vastu counsel, and life-path guidance. Trusted by thousands across the globe.</p>
            </div>

            <div class="rm-stats">
                <div class="rm-stat">
                    <span class="rm-stat-value">25+</span>
                    <span class="rm-stat-label">Years Exp.</span>
                </div>
                <div class="rm-stat">
                    <span class="rm-stat-value">50K</span>
                    <span class="rm-stat-label">Clients</span>
                </div>
                <div class="rm-stat">
                    <span class="rm-stat-value">98%</span>
                    <span class="rm-stat-label">Satisfied</span>
                </div>
            </div>

            <div class="rm-trust-list">
                <div class="rm-trust-item">
                    <span class="rm-trust-icon"><i class="fa-solid fa-shield-halved"></i></span>
                    <span>100% Confidential Consultation</span>
                </div>
                <div class="rm-trust-item">
                    <span class="rm-trust-icon"><i class="fa-solid fa-lock"></i></span>
                    <span>Secure Payment Gateway</span>
                </div>
                <div class="rm-trust-item">
                    <span class="rm-trust-icon"><i class="fa-solid fa-headset"></i></span>
                    <span>24/7 Post-Consultation Support</span>
                </div>
            </div>
        </aside>

        <section class="rm-main">
            <div class="rm-pricing-block">
                <div class="rm-pricing-head">
                    <div>
                        <h2 class="rm-pricing-title">Consultation Pricing</h2>
                        <p class="rm-pricing-subtitle">Choose your preferred slot</p>
                    </div>
                    <div class="rm-pricing-mark">Rs</div>
                </div>

                <div class="rm-pricing-list">
                    <div class="rm-price-card" data-tier="urgent">
                        <div class="rm-price-icon rm-hot"><i class="fa-solid fa-fire"></i></div>
                        <div class="rm-price-meta">
                            <div class="rm-price-line">Within 2 Days <span class="rm-badge rm-badge-hot">High Demand</span></div>
                        </div>
                        <div class="rm-price-value rm-hot-text">₹21,000</div>
                    </div>

                    <div class="rm-price-card" data-tier="popular">
                        <div class="rm-price-icon rm-pop"><i class="fa-solid fa-star"></i></div>
                        <div class="rm-price-meta">
                            <div class="rm-price-line">Within 15 Days <span class="rm-badge rm-badge-pop">Popular</span></div>
                        </div>
                        <div class="rm-price-value rm-pop-text">₹11,000</div>
                    </div>

                    <div class="rm-price-card" data-tier="value">
                        <div class="rm-price-icon rm-best"><i class="fa-solid fa-thumbs-up"></i></div>
                        <div class="rm-price-meta">
                            <div class="rm-price-line">Within 45 Days <span class="rm-badge rm-badge-best">Best Value</span></div>
                        </div>
                        <div class="rm-price-value rm-best-text">₹5,000</div>
                    </div>

                    <div class="rm-price-card rm-disabled" data-tier="unavailable">
                        <div class="rm-price-icon rm-off"><i class="fa-solid fa-ban"></i></div>
                        <div class="rm-price-meta">
                            <div class="rm-price-line">After 45 Days <span class="rm-badge rm-badge-off">Closed</span></div>
                        </div>
                        <div class="rm-price-value rm-off-text">N/A</div>
                    </div>
                </div>
            </div>

            <div class="rm-divider"></div>

            <div class="rm-form-wrap">
                <div class="rm-section-copy">
                    <h2>Book Your Session</h2>
                    <p>Complete the form to confirm your consultation</p>
                </div>

                @if(session('success'))
                    <div class="rm-alert rm-alert-success">{{ session('success') }}</div>
                @endif

                @if($errors->any())
                    <div class="rm-alert rm-alert-danger">
                        <ul class="rm-error-list">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div id="stepper-error" class="rm-alert rm-alert-danger" style="display:none"></div>

                <div class="rm-step-row" id="stepIndicators">
                    <div class="rm-step-chip active" data-step="0"><span class="rm-step-chip-num">1</span><span>Personal</span></div>
                    <div class="rm-step-chip" data-step="1"><span class="rm-step-chip-num">2</span><span>Birth</span></div>
                    <div class="rm-step-chip" data-step="2"><span class="rm-step-chip-num">3</span><span>Session</span></div>
                    <div class="rm-step-chip" data-step="3"><span class="rm-step-chip-num">4</span><span>Payment</span></div>
                    <div class="rm-step-chip" data-step="4"><span class="rm-step-chip-num">5</span><span>Confirm</span></div>
                </div>

                <div class="rm-progress-track">
                    <div class="rm-progress-bar" id="progressFill" style="width:20%"></div>
                </div>

                <form id="raju-booking-form" novalidate>
                    @csrf
                    <input type="hidden" name="astrologer_id" value="15">
                    <input type="hidden" name="slot_id" id="slot_id">
                    <input type="hidden" name="rate" id="rate">

                    <div class="rm-step-panel active">
                        <h3>Personal Information</h3>
                        <div class="rm-field">
                            <label for="rm-name">Full Name</label>
                            <input id="rm-name" name="name" type="text" placeholder="e.g. Priya Sharma" required>
                        </div>
                        <div class="rm-grid-2">
                            <div class="rm-field">
                                <label for="rm-email">Email Address</label>
                                <input id="rm-email" name="user_email" type="email" placeholder="you@email.com" required>
                            </div>
                            <div class="rm-field">
                                <label for="rm-phone">Phone Number</label>
                                <input id="rm-phone" name="phone" type="tel" placeholder="+91 98765 43210" required>
                            </div>
                        </div>
                        <div class="rm-actions">
                            <button type="button" class="rm-btn rm-btn-primary next-btn">Continue <i class="fa-solid fa-arrow-right"></i></button>
                        </div>
                    </div>

                    <div class="rm-step-panel">
                        <h3>Birth Details</h3>
                        <div class="rm-grid-2">
                            <div class="rm-field">
                                <label for="rm-birth-date">Date of Birth</label>
                                <input id="rm-birth-date" name="birth_date" type="date" required>
                            </div>
                            <div class="rm-field">
                                <label for="rm-birth-time">Time of Birth</label>
                                <input id="rm-birth-time" name="birth_time" type="time" required>
                            </div>
                        </div>
                        <div class="rm-field">
                            <label for="rm-place">Place of Birth</label>
                            <input id="rm-place" name="place" type="text" placeholder="City, State, Country">
                        </div>
                        <div class="rm-actions rm-actions-split">
                            <button type="button" class="rm-btn rm-btn-secondary prev-btn"><i class="fa-solid fa-arrow-left"></i> Back</button>
                            <button type="button" class="rm-btn rm-btn-primary next-btn">Continue <i class="fa-solid fa-arrow-right"></i></button>
                        </div>
                    </div>

                    <div class="rm-step-panel">
                        <h3>Session Preference</h3>
                        <div class="rm-field">
                            <label>Consultation Mode</label>
                            <div class="rm-choice-list" data-choice-group="consultation_type">
                                <label class="rm-choice-item">
                                    <input type="radio" name="consultation_type" value="video" checked>
                                    <span class="rm-choice-icon"><i class="fa-solid fa-video"></i></span>
                                    <span>Video Call</span>
                                </label>
                                <label class="rm-choice-item">
                                    <input type="radio" name="consultation_type" value="phone">
                                    <span class="rm-choice-icon"><i class="fa-solid fa-phone"></i></span>
                                    <span>Phone Call</span>
                                </label>
                                <label class="rm-choice-item">
                                    <input type="radio" name="consultation_type" value="inperson">
                                    <span class="rm-choice-icon"><i class="fa-solid fa-building-columns"></i></span>
                                    <span>In-Person Visit</span>
                                </label>
                            </div>
                        </div>

                        <div class="rm-grid-2">
                            <div class="rm-field">
                                <label for="rm-duration">Session Duration</label>
                                <input id="rm-duration" name="duration" type="text" readonly>
                            </div>
                            <div class="rm-field">
                                <label for="consultation_date">Preferred Date</label>
                                <input id="consultation_date" name="scheduled_at" type="date" required>
                            </div>
                        </div>

                        <div class="rm-field">
                            <label>Available Slots</label>
                            <div id="slotGrid" class="rm-slot-grid"></div>
                            <div class="rm-slot-caption">Selected Slot: <strong id="slotText">None</strong></div>
                        </div>

                        <div class="rm-field">
                            <label for="rm-notes">Your Question / Concern</label>
                            <textarea id="rm-notes" name="notes" rows="4" placeholder="Briefly describe what you'd like guidance on..."></textarea>
                        </div>

                        <div class="rm-actions rm-actions-split">
                            <button type="button" class="rm-btn rm-btn-secondary prev-btn"><i class="fa-solid fa-arrow-left"></i> Back</button>
                            <button type="button" class="rm-btn rm-btn-primary next-btn">Continue <i class="fa-solid fa-arrow-right"></i></button>
                        </div>
                    </div>

                    <div class="rm-step-panel">
                        <h3>Payment Method</h3>
                        <div class="rm-choice-list" data-choice-group="payment_method">
                            <label class="rm-choice-item">
                                <input type="radio" name="payment_method" value="upi" checked>
                                <span class="rm-choice-icon"><i class="fa-solid fa-qrcode"></i></span>
                                <span>
                                    <strong>UPI / QR Code</strong>
                                    <small>PhonePe, GPay, Paytm and more</small>
                                </span>
                            </label>
                            <label class="rm-choice-item">
                                <input type="radio" name="payment_method" value="card">
                                <span class="rm-choice-icon"><i class="fa-solid fa-credit-card"></i></span>
                                <span>
                                    <strong>Debit / Credit Card</strong>
                                    <small>Visa, Mastercard, RuPay</small>
                                </span>
                            </label>
                            <label class="rm-choice-item">
                                <input type="radio" name="payment_method" value="netbanking">
                                <span class="rm-choice-icon"><i class="fa-solid fa-building-columns"></i></span>
                                <span>
                                    <strong>Net Banking</strong>
                                    <small>All major banks supported</small>
                                </span>
                            </label>
                        </div>

                        <div class="rm-actions rm-actions-split">
                            <button type="button" class="rm-btn rm-btn-secondary prev-btn"><i class="fa-solid fa-arrow-left"></i> Back</button>
                            <button type="button" class="rm-btn rm-btn-primary next-btn">Continue <i class="fa-solid fa-arrow-right"></i></button>
                        </div>
                    </div>

                    <div class="rm-step-panel">
                        <h3>Review &amp; Confirm</h3>
                        <div class="rm-summary-card">
                            <div class="rm-summary-title">Booking Summary</div>
                            <div class="rm-summary-row"><span>Consultant</span><span class="rm-summary-value">Raju Maharaj</span></div>
                            <div class="rm-summary-row"><span>Mode</span><span class="rm-summary-value" id="summaryMode">Video Call</span></div>
                            <div class="rm-summary-row"><span>Preferred Date</span><span class="rm-summary-value" id="summaryDate">Not selected</span></div>
                            <div class="rm-summary-row"><span>Slot</span><span class="rm-summary-value" id="summarySlot">None</span></div>
                            <div class="rm-summary-row"><span>Pricing Tier</span><span class="rm-summary-value" id="summaryTier">Not selected</span></div>
                            <div class="rm-summary-row"><span>Amount</span><span class="rm-summary-value rm-summary-amount" id="summaryAmount">₹0</span></div>
                            <div class="rm-summary-row"><span>Payment</span><span class="rm-summary-value" id="summaryPayment">UPI / QR Code</span></div>
                        </div>

                        <div class="rm-check-list">
                            <label class="rm-check-item">
                                <input type="checkbox" id="termsCheck">
                                <span>I agree to the <a href="#">Terms &amp; Conditions</a> and <a href="#">Privacy Policy</a></span>
                            </label>
                            <label class="rm-check-item">
                                <input type="checkbox" id="detailsConfirmCheck">
                                <span>I confirm all details provided are accurate</span>
                            </label>
                        </div>

                        <div class="rm-actions rm-actions-split">
                            <button type="button" class="rm-btn rm-btn-secondary prev-btn"><i class="fa-solid fa-arrow-left"></i> Back</button>
                            <button type="button" id="razorpay-pay-btn" class="rm-btn rm-btn-pay"><i class="fa-solid fa-lock"></i> Pay Securely - ₹0</button>
                        </div>

                        <div class="rm-security-note">
                            <i class="fa-solid fa-shield-halved"></i>
                            <span>256-bit SSL encrypted · Powered by Razorpay</span>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
</div>
@endsection

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=DM+Sans:wght@300;400;500;600;700&display=swap');

    .rm-page,
    .rm-page *,
    .rm-page *::before,
    .rm-page *::after {
        box-sizing: border-box;
    }

    .rm-page {
        --rm-saffron: #ff9800;
        --rm-saffron-deep: #f06a00;
        --rm-saffron-dark: #cb4300;
        --rm-soft-bg: #f6efe6;
        --rm-card-bg: #f8f2eb;
        --rm-white: #ffffff;
        --rm-border: #eddcc7;
        --rm-border-soft: #f3e6d8;
        --rm-text: #4a3722;
        --rm-text-strong: #2c2012;
        --rm-text-muted: #baa28b;
        --rm-danger: #e85b5b;
        --rm-warning: #f2a630;
        --rm-success: #54a062;
        --rm-shadow: 0 24px 70px rgba(202, 145, 54, 0.18);
        min-height: calc(100vh - 40px);
        padding: 40px 16px;
        background:
            radial-gradient(circle at left center, rgba(255, 191, 73, 0.24), transparent 32%),
            radial-gradient(circle at right center, rgba(255, 193, 112, 0.18), transparent 28%),
            linear-gradient(180deg, #f7f0e7 0%, #f5eee6 100%);
        font-family: 'DM Sans', sans-serif;
        color: var(--rm-text);
    }

    .rm-shell {
        width: 100%;
        max-width: 808px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 304px minmax(0, 1fr);
        border-radius: 26px;
        overflow: hidden;
        background: var(--rm-card-bg);
        box-shadow: var(--rm-shadow);
    }

    .rm-sidebar {
        position: relative;
        padding: 32px 24px 28px;
        background: linear-gradient(180deg, #ff9800 0%, #f06a00 54%, #cb4300 100%);
        color: var(--rm-white);
        overflow: hidden;
    }

    .rm-sidebar::before {
        content: '';
        position: absolute;
        right: -70px;
        top: 120px;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
    }

    .rm-sidebar::after {
        content: '';
        position: absolute;
        left: -54px;
        bottom: -42px;
        width: 190px;
        height: 190px;
        border-radius: 50%;
        background: rgba(140, 33, 0, 0.24);
    }

    .rm-portrait-wrap,
    .rm-sidebar-copy,
    .rm-stats,
    .rm-trust-list {
        position: relative;
        z-index: 1;
    }

    .rm-portrait-wrap {
        position: relative;
        margin-bottom: 18px;
        overflow: hidden;
        border-radius: 16px;
        border: 2px solid rgba(255, 255, 255, 0.26);
        box-shadow: 0 16px 30px rgba(111, 40, 0, 0.24);
    }

    .rm-portrait-wrap img {
        display: block;
        width: 100%;
        height: 220px;
        object-fit: cover;
    }

    .rm-live-pill {
        position: absolute;
        top: 12px;
        left: 12px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 10px;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, 0.35);
        background: rgba(189, 191, 167, 0.62);
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        backdrop-filter: blur(10px);
    }

    .rm-live-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #7bffad;
        box-shadow: 0 0 0 3px rgba(123, 255, 173, 0.2);
    }

    .rm-stars {
        color: #ffcc42;
        letter-spacing: 2px;
        font-size: 11px;
        margin-bottom: 2px;
    }

    .rm-reviews {
        font-size: 11px;
        font-weight: 600;
        opacity: 0.88;
        margin-bottom: 8px;
    }

    .rm-name {
        margin: 0 0 4px;
        font-family: 'Cormorant Garamond', serif;
        font-size: 34px;
        font-weight: 700;
        line-height: 1.02;
    }

    .rm-role {
        margin-bottom: 10px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .rm-description {
        margin: 0;
        font-size: 13px;
        line-height: 1.62;
        max-width: 220px;
    }

    .rm-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        margin: 18px 0;
    }

    .rm-stat {
        padding: 12px 8px 10px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.08);
        text-align: center;
    }

    .rm-stat-value {
        display: block;
        margin-bottom: 3px;
        font-family: 'Cormorant Garamond', serif;
        font-size: 28px;
        font-weight: 700;
        line-height: 1;
    }

    .rm-stat-label {
        font-size: 10px;
        opacity: 0.9;
    }

    .rm-trust-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 8px;
    }

    .rm-trust-item {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 12px;
        font-weight: 500;
    }

    .rm-trust-icon {
        width: 20px;
        height: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 7px;
        background: rgba(255, 255, 255, 0.15);
        font-size: 10px;
        flex-shrink: 0;
    }

    .rm-main {
        padding: 28px 32px 30px;
        background: #fbf7f2;
    }

    .rm-pricing-block {
        margin-bottom: 26px;
    }

    .rm-pricing-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 14px;
    }

    .rm-pricing-title,
    .rm-section-copy h2,
    .rm-step-panel h3 {
        margin: 0;
        font-family: 'Cormorant Garamond', serif;
        color: var(--rm-text-strong);
    }

    .rm-pricing-title {
        font-size: 18px;
        font-weight: 700;
    }

    .rm-pricing-subtitle {
        margin: 2px 0 0;
        color: var(--rm-text-muted);
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .rm-pricing-mark {
        color: var(--rm-saffron);
        font-size: 13px;
        font-weight: 700;
        padding-top: 7px;
    }

    .rm-pricing-list {
        display: flex;
        flex-direction: column;
        gap: 9px;
    }

    .rm-price-card {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 14px;
        border: 1px solid var(--rm-border-soft);
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.85);
        transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
    }

    .rm-price-card.is-selected {
        border-color: rgba(240, 106, 0, 0.35);
        box-shadow: 0 10px 20px rgba(240, 106, 0, 0.08);
    }

    .rm-price-card:not(.rm-disabled):hover {
        transform: translateY(-1px);
    }

    .rm-disabled {
        opacity: 0.58;
        background: rgba(244, 241, 237, 0.9);
    }

    .rm-price-icon {
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 9px;
        font-size: 12px;
        flex-shrink: 0;
    }

    .rm-hot { background: #ffeaea; color: #f15353; }
    .rm-pop { background: #fff4dc; color: #f1a516; }
    .rm-best { background: #e9f6eb; color: #4f9b5c; }
    .rm-off { background: #efefef; color: #a5a5a5; }
    .rm-hot-text { color: #ef5454; }
    .rm-pop-text { color: #efa11c; }
    .rm-best-text { color: #4f9b5c; }
    .rm-off-text { color: #a7a7a7; }

    .rm-price-meta { flex: 1; }

    .rm-price-line {
        font-size: 14px;
        font-weight: 700;
        color: var(--rm-text);
    }

    .rm-badge {
        display: inline-block;
        margin-left: 6px;
        padding: 2px 7px;
        border-radius: 999px;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        vertical-align: middle;
    }

    .rm-badge-hot { background: #ffe8e8; color: #ef5656; }
    .rm-badge-pop { background: #fff3dd; color: #f1a516; }
    .rm-badge-best { background: #e9f6eb; color: #4f9b5c; }
    .rm-badge-off { background: #efefef; color: #a7a7a7; }

    .rm-price-value {
        font-family: 'Cormorant Garamond', serif;
        font-size: 28px;
        font-weight: 700;
        line-height: 1;
    }

    .rm-divider {
        height: 1px;
        margin-bottom: 18px;
        background: linear-gradient(90deg, transparent, #eadccf, transparent);
    }

    .rm-section-copy {
        margin-bottom: 10px;
    }

    .rm-section-copy h2 {
        font-size: 20px;
        font-weight: 700;
    }

    .rm-section-copy p {
        margin: 2px 0 0;
        color: var(--rm-text-muted);
        font-size: 12px;
    }

    .rm-alert {
        margin-bottom: 14px;
        padding: 12px 14px;
        border-radius: 12px;
        font-size: 13px;
    }

    .rm-alert-success {
        background: #eef8ef;
        border: 1px solid #d9efdd;
        color: #3f8d4e;
    }

    .rm-alert-danger {
        background: #fff0f0;
        border: 1px solid #f4d6d6;
        color: #c55151;
    }

    .rm-error-list {
        margin: 0;
        padding-left: 18px;
    }

    .rm-step-row {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 10px;
    }

    .rm-step-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 9px;
        border: 1px solid #eadbc9;
        border-radius: 999px;
        background: #fff;
        color: var(--rm-text-muted);
        font-size: 10px;
        font-weight: 700;
    }

    .rm-step-chip.active {
        background: linear-gradient(180deg, #ffb129, #ff8b00);
        border-color: transparent;
        color: #fff;
        box-shadow: 0 8px 16px rgba(255, 145, 0, 0.18);
    }

    .rm-step-chip.done {
        background: #eef8ef;
        border-color: #d4e7d7;
        color: #4d9258;
    }

    .rm-step-chip-num {
        width: 14px;
        height: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: currentColor;
        color: #fff;
        font-size: 9px;
        line-height: 1;
    }

    .rm-step-chip:not(.active):not(.done) .rm-step-chip-num {
        background: #d5c1ab;
    }

    .rm-progress-track {
        height: 4px;
        margin-bottom: 18px;
        border-radius: 999px;
        background: #eadcca;
        overflow: hidden;
    }

    .rm-progress-bar {
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, #ffab20 0%, #f26a00 100%);
        transition: width 0.35s ease;
    }

    .rm-step-panel {
        display: none;
    }

    .rm-step-panel.active {
        display: block;
    }

    .rm-step-panel h3 {
        margin-bottom: 12px;
        font-size: 17px;
        font-weight: 700;
    }

    .rm-grid-2 {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .rm-field {
        margin-bottom: 12px;
    }

    .rm-field label {
        display: block;
        margin-bottom: 6px;
        color: #917b66;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .rm-page input,
    .rm-page textarea,
    .rm-page select {
        width: 100%;
        min-width: 0;
        padding: 11px 12px;
        border: 1px solid #eadcca;
        border-radius: 10px;
        background: #fff;
        color: var(--rm-text);
        font: inherit;
        font-size: 13px;
        outline: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .rm-page input:focus,
    .rm-page textarea:focus,
    .rm-page select:focus {
        border-color: rgba(240, 106, 0, 0.45);
        box-shadow: 0 0 0 3px rgba(240, 106, 0, 0.08);
    }

    .rm-page textarea {
        resize: vertical;
        min-height: 96px;
    }

    .rm-invalid {
        border-color: rgba(197, 81, 81, 0.45) !important;
        box-shadow: 0 0 0 3px rgba(197, 81, 81, 0.08) !important;
    }

    .rm-choice-list,
    .rm-check-list {
        display: flex;
        flex-direction: column;
        gap: 9px;
    }

    .rm-choice-item,
    .rm-check-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 13px;
        border: 1px solid #eadcca;
        border-radius: 12px;
        background: #fff;
        cursor: pointer;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
        font-size: 13px;
    }

    .rm-choice-item.is-selected,
    .rm-check-item.is-selected {
        border-color: rgba(240, 106, 0, 0.45);
        box-shadow: 0 10px 22px rgba(240, 106, 0, 0.07);
    }

    .rm-choice-item strong,
    .rm-choice-item small {
        display: block;
    }

    .rm-choice-item small {
        color: var(--rm-text-muted);
        font-size: 11px;
        margin-top: 1px;
    }

    .rm-choice-item input,
    .rm-check-item input {
        width: 15px;
        height: 15px;
        accent-color: var(--rm-saffron-deep);
        flex-shrink: 0;
    }

    .rm-choice-icon {
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: #fff2e3;
        color: var(--rm-saffron-deep);
        font-size: 12px;
        flex-shrink: 0;
    }

    .rm-slot-grid {
        min-height: 58px;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding: 10px;
        border: 1px solid #eadcca;
        border-radius: 12px;
        background: #fff;
    }

    .rm-slot-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 122px;
        padding: 9px 12px;
        border: 1px solid #f2d4a7;
        border-radius: 999px;
        background: #fff8ec;
        color: var(--rm-text);
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .rm-slot-badge.active {
        border-color: transparent;
        background: linear-gradient(180deg, #ffb129, #ff8b00);
        color: #fff;
        box-shadow: 0 8px 16px rgba(255, 145, 0, 0.2);
    }

    .rm-slot-skeleton {
        width: 122px;
        height: 36px;
        border-radius: 999px;
        background: linear-gradient(90deg, #f6eee3 20%, #fff8f1 50%, #f6eee3 80%);
        background-size: 300% 100%;
        animation: rmShimmer 1.2s linear infinite;
    }

    @keyframes rmShimmer {
        from { background-position: 100% 0; }
        to { background-position: -100% 0; }
    }

    .rm-slot-state,
    .rm-slot-caption {
        color: var(--rm-text-muted);
        font-size: 12px;
    }

    .rm-slot-caption {
        margin-top: 8px;
    }

    .rm-actions {
        display: flex;
        gap: 10px;
        margin-top: 18px;
    }

    .rm-actions-split {
        justify-content: space-between;
    }

    .rm-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        border: 0;
        border-radius: 10px;
        cursor: pointer;
        font: inherit;
        font-size: 13px;
        font-weight: 700;
        transition: transform 0.15s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .rm-btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .rm-btn-primary,
    .rm-btn-pay {
        flex: 1;
        padding: 13px 18px;
        color: #fff;
        background: linear-gradient(90deg, #ffb129 0%, #ff8b00 42%, #f26800 100%);
        box-shadow: 0 10px 22px rgba(242, 106, 0, 0.2);
    }

    .rm-btn-secondary {
        padding: 13px 16px;
        border: 1px solid #eadcca;
        background: #fff;
        color: var(--rm-text);
    }

    .rm-btn:hover:not(:disabled) {
        transform: translateY(-1px);
    }

    .rm-summary-card {
        padding: 15px;
        border: 1px solid #eadcca;
        border-radius: 14px;
        background: #fff;
        margin-bottom: 14px;
    }

    .rm-summary-title {
        margin-bottom: 10px;
        color: var(--rm-text-muted);
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }

    .rm-summary-row {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        padding: 7px 0;
        border-bottom: 1px solid #f0e5d8;
        font-size: 13px;
    }

    .rm-summary-row:last-child {
        border-bottom: 0;
    }

    .rm-summary-row span:first-child {
        color: var(--rm-text-muted);
    }

    .rm-summary-value {
        font-weight: 700;
        text-align: right;
    }

    .rm-summary-amount {
        color: var(--rm-saffron-dark);
    }

    .rm-check-list {
        margin-bottom: 14px;
    }

    .rm-check-item {
        align-items: flex-start;
    }

    .rm-check-item a {
        color: var(--rm-saffron-deep);
        font-weight: 700;
        text-decoration: none;
    }

    .rm-security-note {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-top: 10px;
        color: var(--rm-text-muted);
        font-size: 11px;
    }

    .rm-security-note i {
        color: var(--rm-success);
    }

    @media (max-width: 920px) {
        .rm-shell {
            max-width: 880px;
        }
    }

    @media (max-width: 860px) {
        .rm-shell {
            grid-template-columns: 1fr;
        }

        .rm-main {
            padding: 24px;
        }
    }

    @media (max-width: 640px) {
        .rm-page {
            padding: 20px 12px;
        }

        .rm-sidebar,
        .rm-main {
            padding: 22px 18px;
        }

        .rm-grid-2,
        .rm-stats {
            grid-template-columns: 1fr;
        }

        .rm-actions,
        .rm-actions-split {
            flex-direction: column;
        }

        .rm-summary-row {
            flex-direction: column;
            align-items: flex-start;
        }

        .rm-summary-value {
            text-align: left;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('raju-booking-form');
    const steps = Array.from(document.querySelectorAll('.rm-step-panel'));
    const stepDots = Array.from(document.querySelectorAll('.rm-step-chip'));
    const progressFill = document.getElementById('progressFill');
    const errorBox = document.getElementById('stepper-error');
    const dateInput = document.getElementById('consultation_date');
    const durationInput = document.getElementById('rm-duration');
    const slotGrid = document.getElementById('slotGrid');
    const slotIdInput = document.getElementById('slot_id');
    const slotText = document.getElementById('slotText');
    const rateInput = document.getElementById('rate');
    const pricingRows = Array.from(document.querySelectorAll('.rm-price-card'));
    const razorpayBtn = document.getElementById('razorpay-pay-btn');
    const summaryMode = document.getElementById('summaryMode');
    const summaryDate = document.getElementById('summaryDate');
    const summarySlot = document.getElementById('summarySlot');
    const summaryTier = document.getElementById('summaryTier');
    const summaryAmount = document.getElementById('summaryAmount');
    const summaryPayment = document.getElementById('summaryPayment');
    let currentStep = 0;

    function formatCurrency(amount) {
        const parsed = Number(amount || 0);
        return parsed > 0 ? '₹' + parsed.toLocaleString('en-IN') : '₹0';
    }

    function buildHeaders() {
        const token = localStorage.getItem('authToken') || '';
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };

        if (token) {
            headers.Authorization = 'Bearer ' + token;
        }

        return headers;
    }

    function showToast(message, isError) {
        let toast = document.getElementById('rm-toast');

        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'rm-toast';
            toast.style.position = 'fixed';
            toast.style.left = '50%';
            toast.style.bottom = '24px';
            toast.style.transform = 'translateX(-50%) translateY(12px)';
            toast.style.zIndex = '9999';
            toast.style.padding = '12px 18px';
            toast.style.borderRadius = '10px';
            toast.style.color = '#fff';
            toast.style.fontSize = '13px';
            toast.style.fontWeight = '700';
            toast.style.boxShadow = '0 14px 30px rgba(0,0,0,0.18)';
            toast.style.opacity = '0';
            toast.style.transition = 'all 0.25s ease';
            document.body.appendChild(toast);
        }

        toast.textContent = message;
        toast.style.background = isError ? '#cf5a5a' : '#4e9a5b';
        toast.style.opacity = '1';
        toast.style.transform = 'translateX(-50%) translateY(0)';

        clearTimeout(toast._timer);
        toast._timer = setTimeout(function () {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(-50%) translateY(12px)';
        }, 3000);
    }

    function clearErrors() {
        errorBox.style.display = 'none';
        errorBox.textContent = '';
        form.querySelectorAll('.rm-invalid').forEach(function (element) {
            element.classList.remove('rm-invalid');
        });
    }

    function showStepError(message, fields) {
        errorBox.textContent = message;
        errorBox.style.display = 'block';
        (fields || []).forEach(function (field) {
            if (field) {
                field.classList.add('rm-invalid');
            }
        });
    }

    function updateSelectableStates() {
        document.querySelectorAll('.rm-choice-item').forEach(function (item) {
            const input = item.querySelector('input[type="radio"]');
            item.classList.toggle('is-selected', Boolean(input && input.checked));
        });

        document.querySelectorAll('.rm-check-item').forEach(function (item) {
            const input = item.querySelector('input[type="checkbox"]');
            item.classList.toggle('is-selected', Boolean(input && input.checked));
        });
    }

    function showStep(index) {
        steps.forEach(function (step, stepIndex) {
            step.classList.toggle('active', stepIndex === index);
        });

        stepDots.forEach(function (dot, dotIndex) {
            dot.classList.remove('active', 'done');
            if (dotIndex < index) {
                dot.classList.add('done');
            }
            if (dotIndex === index) {
                dot.classList.add('active');
            }
        });

        progressFill.style.width = ((index + 1) / steps.length * 100) + '%';
        currentStep = index;
        clearErrors();
        updateSelectableStates();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function getModeLabel() {
        const input = document.querySelector('[name="consultation_type"]:checked');
        const labels = {
            video: 'Video Call',
            phone: 'Phone Call',
            inperson: 'In-Person Visit'
        };
        return input ? (labels[input.value] || input.value) : 'Not selected';
    }

    function getPaymentLabel() {
        const input = document.querySelector('[name="payment_method"]:checked');
        const labels = {
            upi: 'UPI / QR Code',
            card: 'Debit / Credit Card',
            netbanking: 'Net Banking'
        };
        return input ? (labels[input.value] || input.value) : 'Not selected';
    }

    function getTierMeta() {
        if (!dateInput.value) {
            return { amount: 0, tier: 'none', label: 'Not selected' };
        }

        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const selected = new Date(dateInput.value + 'T00:00:00');
        const diffDays = Math.ceil((selected - today) / 86400000);

        if (diffDays < 0 || diffDays > 45) {
            return { amount: 0, tier: 'unavailable', label: 'Unavailable' };
        }
        if (diffDays <= 2) {
            return { amount: 21000, tier: 'urgent', label: 'Within 2 Days' };
        }
        if (diffDays <= 15) {
            return { amount: 11000, tier: 'popular', label: 'Within 15 Days' };
        }
        return { amount: 5000, tier: 'value', label: 'Within 45 Days' };
    }

    function updatePricing() {
        const tierMeta = getTierMeta();
        const payLabel = '<i class="fa-solid fa-lock"></i> Pay Securely - ' + formatCurrency(tierMeta.amount);

        pricingRows.forEach(function (row) {
            const isUnavailable = tierMeta.tier === 'unavailable' && row.dataset.tier === 'unavailable';
            row.classList.toggle('is-selected', row.dataset.tier === tierMeta.tier || isUnavailable);
        });

        rateInput.value = tierMeta.amount || '';
        summaryTier.textContent = tierMeta.label;
        summaryAmount.textContent = formatCurrency(tierMeta.amount);
        razorpayBtn.innerHTML = payLabel;
        razorpayBtn.dataset.defaultLabel = payLabel;
    }

    function updateSummary() {
        summaryMode.textContent = getModeLabel();
        summaryPayment.textContent = getPaymentLabel();
        summaryDate.textContent = dateInput.value || 'Not selected';
        summarySlot.textContent = slotText.textContent || 'None';
        updatePricing();
    }

    function clearSlots(message) {
        slotGrid.innerHTML = '<div class="rm-slot-state">' + message + '</div>';
        slotIdInput.value = '';
        slotText.textContent = 'None';
        updateSummary();
    }

    function showSlotSkeletons(count) {
        slotGrid.innerHTML = '';
        for (let i = 0; i < count; i += 1) {
            const item = document.createElement('div');
            item.className = 'rm-slot-skeleton';
            slotGrid.appendChild(item);
        }
    }

    function fetchDuration() {
        fetch('/consultation/session-duration?astrologer_id=15')
            .then(function (response) { return response.json(); })
            .then(function (payload) {
                if (payload && payload.duration) {
                    durationInput.value = payload.duration;
                }
            })
            .catch(function () {
                durationInput.value = '';
            });
    }

    function fetchSlots() {
        clearSlots('Select a preferred date to load available slots.');

        if (!dateInput.value) {
            return;
        }

        showSlotSkeletons(5);

        fetch('/api/astrologer/15/slots?date=' + encodeURIComponent(dateInput.value))
            .then(function (response) { return response.json(); })
            .then(function (payload) {
                slotGrid.innerHTML = '';

                if (!payload.success || !Array.isArray(payload.slots) || payload.slots.length === 0) {
                    slotGrid.innerHTML = '<div class="rm-slot-state">No slots available for the selected date.</div>';
                    return;
                }

                payload.slots.forEach(function (slot) {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'rm-slot-badge';
                    button.textContent = slot.start_time + ' - ' + slot.end_time;

                    button.addEventListener('click', function () {
                        slotGrid.querySelectorAll('.rm-slot-badge').forEach(function (badge) {
                            badge.classList.remove('active');
                        });

                        button.classList.add('active');
                        slotIdInput.value = slot.slot_id;
                        slotText.textContent = slot.start_time + ' - ' + slot.end_time;
                        slotGrid.classList.remove('rm-invalid');
                        updateSummary();
                    });

                    slotGrid.appendChild(button);
                });
            })
            .catch(function () {
                slotGrid.innerHTML = '<div class="rm-slot-state">Error loading slots. Please try again.</div>';
            });
    }

    function validateStep(step) {
        clearErrors();

        if (step === 0) {
            if (!form.elements.name.value.trim()) {
                showStepError('Please enter your full name.', [form.elements.name]);
                return false;
            }
            if (!form.elements.user_email.value.trim() || !form.elements.user_email.checkValidity()) {
                showStepError('Please enter a valid email address.', [form.elements.user_email]);
                return false;
            }
            if (!form.elements.phone.value.trim()) {
                showStepError('Please enter your phone number.', [form.elements.phone]);
                return false;
            }
        }

        if (step === 1) {
            if (!form.elements.birth_date.value) {
                showStepError('Please select your birth date.', [form.elements.birth_date]);
                return false;
            }
            if (!form.elements.birth_time.value) {
                showStepError('Please select your birth time.', [form.elements.birth_time]);
                return false;
            }
        }

        if (step === 2) {
            const consultationGroup = document.querySelector('[data-choice-group="consultation_type"]');
            if (!document.querySelector('[name="consultation_type"]:checked')) {
                showStepError('Please select a consultation mode.', [consultationGroup]);
                return false;
            }
            if (!durationInput.value) {
                showStepError('Session duration could not be loaded. Refresh and try again.', [durationInput]);
                return false;
            }
            if (!dateInput.value) {
                showStepError('Please select your preferred consultation date.', [dateInput]);
                return false;
            }
            if (!rateInput.value) {
                showStepError('Booking is only allowed for dates within 45 days.', [dateInput]);
                return false;
            }
            if (!slotIdInput.value) {
                showStepError('Please select an available time slot.', [slotGrid]);
                return false;
            }
        }

        if (step === 3) {
            const paymentGroup = document.querySelector('[data-choice-group="payment_method"]');
            if (!document.querySelector('[name="payment_method"]:checked')) {
                showStepError('Please choose a payment method.', [paymentGroup]);
                return false;
            }
        }

        if (step === 4) {
            const termsCheck = document.getElementById('termsCheck');
            const checkList = document.querySelector('.rm-check-list');
            if (!termsCheck.checked) {
                showStepError('You must accept the terms and privacy policy before paying.', [checkList]);
                return false;
            }
        }

        return true;
    }

    function setPayLoading(isLoading) {
        if (!razorpayBtn.dataset.defaultLabel) {
            razorpayBtn.dataset.defaultLabel = razorpayBtn.innerHTML;
        }

        razorpayBtn.disabled = isLoading;
        razorpayBtn.innerHTML = isLoading
            ? '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...'
            : razorpayBtn.dataset.defaultLabel;
    }

    function redirectToBookingDetails(bookingId) {
        if (bookingId) {
            window.location.href = '/booking/' + encodeURIComponent(bookingId);
        }
    }

    document.querySelectorAll('.next-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            if (validateStep(currentStep) && currentStep < steps.length - 1) {
                updateSummary();
                showStep(currentStep + 1);
            }
        });
    });

    document.querySelectorAll('.prev-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            if (currentStep > 0) {
                showStep(currentStep - 1);
            }
        });
    });

    form.querySelectorAll('input, textarea, select').forEach(function (element) {
        element.addEventListener('input', function () {
            element.classList.remove('rm-invalid');
            clearErrors();
            updateSelectableStates();
            updateSummary();
        });

        element.addEventListener('change', function () {
            element.classList.remove('rm-invalid');
            clearErrors();
            updateSelectableStates();
            updateSummary();
        });
    });

    dateInput.addEventListener('change', function () {
        updatePricing();
        fetchSlots();
    });

    razorpayBtn.addEventListener('click', function () {
        if (!validateStep(4)) {
            return;
        }

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
            type: formData.get('consultation_type') || 'video',
            rate: formData.get('rate'),
            birth_date: formData.get('birth_date'),
            birth_time: formData.get('birth_time'),
            place: formData.get('place'),
            notes: formData.get('notes')
        };

        const rate = parseInt(bookingPayload.rate, 10);
        if (!bookingPayload.name || !bookingPayload.phone || !bookingPayload.email || !bookingPayload.consultation_type || !bookingPayload.date || !bookingPayload.slot_id) {
            showToast('Please complete all required fields and select a slot.', true);
            return;
        }
        if (!rate || Number.isNaN(rate) || rate < 100) {
            showToast('Invalid or missing price. Please select a valid date.', true);
            return;
        }

        setPayLoading(true);

        fetch('/api/v1/bookings', {
            method: 'POST',
            headers: buildHeaders(),
            body: JSON.stringify(bookingPayload)
        })
            .then(function (response) { return response.json(); })
            .then(function (bookingResponse) {
                if (!bookingResponse.success || !bookingResponse.data || !bookingResponse.data.data || !bookingResponse.data.data.id) {
                    setPayLoading(false);
                    showToast(bookingResponse.message || 'Failed to create booking.', true);
                    return;
                }

                const bookingId = bookingResponse.data.data.id;
                const currency = bookingResponse.data.data.currency || 'INR';

                fetch('/api/v1/razorpay/order', {
                    method: 'POST',
                    headers: buildHeaders(),
                    body: JSON.stringify({ booking_id: bookingId, amount: rate, currency: currency })
                })
                    .then(function (response) { return response.json(); })
                    .then(function (orderResponse) {
                        if (!orderResponse.status || !orderResponse.data || !orderResponse.data.razorpay_order_id) {
                            setPayLoading(false);
                            showToast(orderResponse.message || 'Failed to initiate payment.', true);
                            return;
                        }

                        const razorpay = new Razorpay({
                            key: orderResponse.data.key || 'rzp_test_3WmknLIqcUo9er',
                            amount: orderResponse.data.amount,
                            currency: orderResponse.data.currency,
                            name: 'Astro Jyotish',
                            description: 'Consultation Booking',
                            order_id: orderResponse.data.razorpay_order_id,
                            prefill: {
                                name: bookingPayload.name,
                                email: bookingPayload.email,
                                contact: bookingPayload.phone
                            },
                            theme: { color: '#f06a00' },
                            modal: {
                                ondismiss: function () {
                                    setPayLoading(false);
                                }
                            },
                            handler: function (response) {
                                fetch('/api/v1/razorpay/verify', {
                                    method: 'POST',
                                    headers: buildHeaders(),
                                    body: JSON.stringify({
                                        booking_id: bookingId,
                                        razorpay_order_id: response.razorpay_order_id,
                                        razorpay_payment_id: response.razorpay_payment_id,
                                        razorpay_signature: response.razorpay_signature
                                    })
                                })
                                    .then(function (verifyResponse) { return verifyResponse.json(); })
                                    .then(function (verifyPayload) {
                                        if (!verifyPayload.status) {
                                            setPayLoading(false);
                                            showToast(verifyPayload.message || 'Payment verification failed.', true);
                                            return;
                                        }

                                        fetch('/api/v1/razorpay/booking', {
                                            method: 'POST',
                                            headers: buildHeaders(),
                                            body: JSON.stringify({
                                                booking_id: bookingId,
                                                razorpay_order_id: response.razorpay_order_id,
                                                razorpay_payment_id: response.razorpay_payment_id,
                                                razorpay_signature: response.razorpay_signature
                                            })
                                        })
                                            .then(function (confirmResponse) { return confirmResponse.json(); })
                                            .then(function (confirmPayload) {
                                                setPayLoading(false);
                                                if (!confirmPayload.status) {
                                                    showToast(confirmPayload.message || 'Booking confirmation failed.', true);
                                                    return;
                                                }
                                                showToast('Booking and payment successful!', false);
                                                redirectToBookingDetails(bookingId);
                                            })
                                            .catch(function () {
                                                setPayLoading(false);
                                                showToast('Error confirming booking.', true);
                                            });
                                    })
                                    .catch(function () {
                                        setPayLoading(false);
                                        showToast('Payment verification error.', true);
                                    });
                            }
                        });

                        razorpay.open();
                    })
                    .catch(function () {
                        setPayLoading(false);
                        showToast('Error initiating payment.', true);
                    });
            })
            .catch(function () {
                setPayLoading(false);
                showToast('Error creating booking.', true);
            });
    });

    clearSlots('Select a preferred date to load available slots.');
    fetchDuration();
    updateSelectableStates();
    updateSummary();
    showStep(0);
});
</script>
@endpush
