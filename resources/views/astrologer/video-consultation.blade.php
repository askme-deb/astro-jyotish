@extends('layouts.app')

@section('content')
@php
    $meetingId = 'astro-' . $appointment['id'];
    $videoConsultationPageData = [
        'status' => $appointment['status'] ?? 'pending',
        'endedAfterLeave' => (bool) session('video_consultation_ended'),
        'bookingId' => $appointment['id'],
        'leaveRedirectUrl' => route('astrologer.appointment.leaveVideo', ['id' => $appointment['id']]),
        'addSuggestedProductUrl' => route('astrologer.appointment.addSuggestedProduct', ['id' => $appointment['id']]),
        'apiKey' => config('services.videosdk.api_key'),
        'meetingId' => $meetingId,
        'participantName' => Auth::user()->name ?? 'Astrologer',
    ];
@endphp
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<style>
    .suggested-product-item:hover {background:#f9f5ef;transition:background .2s;}
    .toast-container {position:fixed;top:1.5rem;right:1.5rem;z-index:9999;}
</style>
<div class="toast-container"></div>
<div class="container" style="max-width: 1100px; margin: 40px auto;">
    <div class="card shadow-lg border-0 p-4" style="border-radius: 18px; background: #f8fafc;">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 pb-2 border-bottom" style="border-color:#f3f3f3!important;">
            <div>
                <h2 class="mb-1" style="font-weight:700;letter-spacing:0.01em;color:#f98700;">
                    <i class="fa-solid fa-video me-2 text-theme-orange"></i>Video Consultation
                </h2>
                <div class="text-muted" style="font-size:1.08rem;">Appointment #{{ $appointment['id'] }}</div>
            </div>
            <div class="text-end">
                <span id="session-state-badge" class="badge bg-secondary" style="font-size:1.1em;">{{ ucfirst($appointment['status'] ?? 'pending') }}</span>
                <span class="ms-3" id="session-timer" style="font-size:1.1em;color:#f98700;"><i class="fa-regular fa-clock me-1"></i>00:00</span>
                <button type="button" class="btn btn-link text-info p-0 ms-2" tabindex="0" data-bs-toggle="popover" data-bs-trigger="focus" title="Help" data-bs-content="If you face any issues, refresh the page or check your internet connection. For urgent support, contact admin."><i class="fa-solid fa-circle-question"></i></button>
            </div>
        </div>
      
        <div class="row g-4">
            <div class="col-lg-8 col-12">
                <!-- <div class="alert alert-info mb-3 p-4 d-flex align-items-start" style="font-size:1.08em; background: linear-gradient(90deg, #f9fafb 80%, #fbbf24 100%); border-left: 6px solid #f98700; border-radius: 12px; box-shadow: 0 2px 8px rgba(249,135,0,0.06);">
                    <div class="me-3" style="font-size:2em;color:#f98700;"><i class="fa-solid fa-circle-info"></i></div>
                    <div>
                        <div class="fw-bold mb-1" style="color:#d97706;font-size:1.13em;"><i class="fa-solid fa-bullhorn me-1"></i>Session Instructions</div>
                        <ul class="mb-0 ps-3" style="line-height:1.7;">
                            <li>Join from a quiet, well-lit environment.</li>
                            <li>Use a stable internet connection (WiFi preferred).</li>
                            <li>Headphones are recommended for best audio quality.</li>
                            <li>Keep your device charged or plugged in.</li>
                            <li>The session will begin once both you and the customer have joined.</li>
                        </ul>
                    </div>
                </div> -->
                <div id="astrologer-videosdk-meeting-root" style="width:100%;height:700px;border-radius:14px;overflow:hidden;background:#181c24;box-shadow:0 2px 12px rgba(0,0,0,0.08);"></div>
                <div id="consultation-ended-state" class="alert alert-success mt-3" style="display:none;">
                    <div class="fw-bold mb-1"><i class="fa-solid fa-circle-check me-1"></i> Consultation ended</div>
                    <div>This video consultation has been completed. The meeting room is now closed.</div>
                </div>
                <div class="mt-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <div class="text-muted" style="font-size:0.98em;">
                        <i class="fa-solid fa-shield-halved me-1"></i> Your session is encrypted and secure.
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <span id="session-status" class="badge bg-secondary" style="font-size:1em;">Status: <span id="session-status-text">{{ ucfirst($appointment['status'] ?? 'pending') }}</span></span>
                        <button id="join-consultation-btn" class="btn btn-primary" type="button"><i class="fa-solid fa-right-to-bracket me-1"></i> Join Consultation</button>
                        <button id="end-session-btn" class="btn btn-danger" type="button" style="display:none;"><i class="fa-solid fa-stop me-1"></i> End Session</button>
                        <button id="refresh-status-btn" class="btn btn-outline-primary" type="button"><i class="fa-solid fa-rotate-right me-1"></i> Refresh Status</button>
                        <a href="{{ route('astrologer.appointments') }}" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-arrow-left me-1"></i> Back to Appointments
                        </a>
                    </div>
                </div>
                <div id="session-feedback" class="mt-2"></div>
           
            </div>
            <div class="col-lg-4 col-12">
                <div class="mb-3">
                    <div class="p-3 bg-white rounded-3 shadow-sm mb-3 border border-1" style="border-color:#f3f3f3!important;">
                        <div class="fw-bold mb-1 text-theme-orange"><i class="fa-solid fa-user me-1"></i>Customer</div>
                        <div><b>Name:</b> {{ $appointment['name'] ?? '-' }}</div>
                        <div><b>Email:</b> {{ $appointment['email'] ?? '-' }}</div>
                        <div><b>Contact:</b> {{ $appointment['phone'] ?? '-' }}</div>
                    </div>
                    @if(isset($appointment['astrologer']) && is_array($appointment['astrologer']))
                    <!-- <div class="p-3 bg-white rounded-3 shadow-sm mb-3 border border-1" style="border-color:#f3f3f3!important;">
                        <div class="fw-bold mb-1 text-theme-orange"><i class="fa-solid fa-user-astronaut me-1"></i>Astrologer</div>
                        <div><b>Name:</b> {{ $appointment['astrologer']['name'] ?? '-' }}</div>
                        <div><b>Experience:</b> {{ $appointment['astrologer']['experience'] ?? '-' }} years</div>
                        <div><b>Languages:</b> {{ isset($appointment['astrologer']['languages']) ? collect($appointment['astrologer']['languages'])->pluck('name')->join(', ') : '-' }}</div>
                        <div><b>Skills:</b> {{ isset($appointment['astrologer']['skills']) ? collect($appointment['astrologer']['skills'])->pluck('name')->join(', ') : '-' }}</div>
                    </div> -->
                    @endif
                    <div class="p-3 bg-white rounded-3 shadow-sm mb-3 border border-1" style="border-color:#f3f3f3!important;">
                        <div class="fw-bold mb-1 text-theme-orange"><i class="fa-solid fa-calendar-day me-1"></i>Appointment</div>
                        <div><b>Date:</b> {{ isset($appointment['scheduled_at']) ? \Carbon\Carbon::parse($appointment['scheduled_at'])->format('d F Y') : '-' }}</div>
                        <div><b>Time:</b> {{ isset($appointment['scheduled_at']) ? \Carbon\Carbon::parse($appointment['scheduled_at'])->format('h:i A') : '-' }}</div>
                        <div><b>Duration:</b> {{ $appointment['duration'] ?? '-' }} min</div>
                    </div>
                </div>
                <div class="accordion mb-3 animate__animated animate__fadeInRight" id="sideAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingNotes">
                            <button class="accordion-button fw-bold text-theme-orange" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNotes" aria-expanded="true" aria-controls="collapseNotes">
                                <i class="fa-solid fa-note-sticky me-1"></i>Consultation Notes
                            </button>
                        </h2>
                        <div id="collapseNotes" class="accordion-collapse collapse show" aria-labelledby="headingNotes" data-bs-parent="#sideAccordion">
                            <div class="accordion-body">
                                <form id="save-notes-form" method="POST" action="{{ route('astrologer.appointment.saveNotes', ['id' => $appointment['id']]) }}">
                                    @csrf
                                    <textarea id="astrologer-note-textarea" class="form-control mb-2" rows="5" name="astrologer_note" placeholder="Write your notes or suggestions for the customer...">{{ $appointment['astrologer_note'] ?? ($appointment['notes'] ?? '') }}</textarea>
                                    <button id="save-notes-btn" class="btn btn-success w-100" type="submit">
                                        <i class="fa-solid fa-save me-1"></i> Save Notes
                                    </button>
                                    <div id="notes-feedback" class="mt-2"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingProduct">
                            <button class="accordion-button fw-bold text-theme-orange collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseProduct" aria-expanded="false" aria-controls="collapseProduct">
                                <i class="fa-solid fa-gem me-1"></i>Suggest Product
                            </button>
                        </h2>
                        <div id="collapseProduct" class="accordion-collapse collapse" aria-labelledby="headingProduct" data-bs-parent="#sideAccordion">
                            <div class="accordion-body">
                                <form id="suggest-products" method="POST" action="{{ route('astrologer.appointment.suggestProduct', ['id' => $appointment['id']]) }}">
                                    @csrf
                                    <div class="row g-2 mb-2 align-items-center">
                                        <div class="col-6">
                                            <select class="form-select" name="product_grade_id">
                                                <option value="">Select Product Grade</option>
                                                @foreach(($productGrades ?? []) as $productGrade)
                                                    <option value="{{ $productGrade['id'] }}">{{ $productGrade['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-6">
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
                                        <div class="col-12 mt-2">
                                            <input type="text" class="form-control" name="q" placeholder="Search Product">
                                        </div>
                                        <div class="col-6 mt-2">
                                            <input type="number" min="0" step="0.01" class="form-control" name="ratti" placeholder="Ratti">
                                        </div>
                                        <div class="col-6 mt-2">
                                            <input type="number" min="0" step="0.01" class="form-control" name="carat" placeholder="Carat">
                                        </div>
                                        <div class="col-6 mt-2">
                                            <input type="number" min="0" step="1" class="form-control" name="min_price" placeholder="Min Price">
                                        </div>
                                        <div class="col-6 mt-2">
                                            <input type="number" min="0" step="1" class="form-control" name="max_price" placeholder="Max Price">
                                        </div>
                                        <div class="col-6 mt-2">
                                            <input type="number" min="1" max="100" step="1" class="form-control" name="per_page" value="20" placeholder="Per Page">
                                        </div>
                                        <div class="col-6 mt-2 d-flex align-items-center">
                                            <div class="form-check ms-1">
                                                <input class="form-check-input" type="checkbox" value="1" id="product-in-stock" name="in_stock" checked>
                                                <label class="form-check-label" for="product-in-stock">In stock only</label>
                                            </div>
                                        </div>
                                    </div>
                                    <button id="search-products-btn" class="btn btn-warning w-100 mt-2" type="submit" style="transition:box-shadow .2s;box-shadow:0 1px 4px rgba(249,135,0,0.08);">
                                        <i class="fa-solid fa-magnifying-glass me-1"></i> Search Products
                                    </button>
                                    <div id="product-search-feedback" class="mt-2"></div>
                                </form>
                                <div class="mt-3">
                                    <div class="fw-bold mb-2 text-theme-orange"><i class="fa-solid fa-list me-1"></i>Suggested Products</div>
                                    <div id="product-search-empty-state" class="text-muted small">Use the filters above to search the product catalog.</div>
                                    <div id="product-search-results" class="list-group"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://sdk.videosdk.live/rtc-js-prebuilt/0.3.34/rtc-js-prebuilt.js"></script>
<script id="video-consultation-page-data" type="application/json">{!! json_encode($videoConsultationPageData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
<script>
function showToast(message) {
    const container = document.querySelector('.toast-container');
    const toast = document.createElement('div');
    toast.className = 'toast align-items-center text-bg-success border-0 show animate__animated animate__fadeInDown';
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    toast.innerHTML = `<div class="d-flex"><div class="toast-body">${message}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div>`;
    container.appendChild(toast);
    setTimeout(() => { toast.classList.remove('show'); toast.classList.add('animate__fadeOutUp'); setTimeout(()=>toast.remove(), 800); }, 2500);
}

document.addEventListener('DOMContentLoaded', function() {
    const pageData = JSON.parse(document.getElementById('video-consultation-page-data').textContent);
    let seconds = 0;
    let timerInterval = null;
    let currentStatus = pageData.status;
    let startTriggeredByJoin = false;
    let meetingInitialized = false;
    let statusPollingInterval = null;
    const endedAfterLeave = pageData.endedAfterLeave;

    const timerEl = document.getElementById('session-timer');
    const bookingId = pageData.bookingId;
    const leaveRedirectUrl = pageData.leaveRedirectUrl;
    const joinConsultationBtn = document.getElementById('join-consultation-btn');
    const endBtn = document.getElementById('end-session-btn');
    const refreshStatusBtn = document.getElementById('refresh-status-btn');
    const statusText = document.getElementById('session-status-text');
    const feedback = document.getElementById('session-feedback');
    const saveNotesForm = document.getElementById('save-notes-form');
    const saveNotesBtn = document.getElementById('save-notes-btn');
    const noteTextarea = document.getElementById('astrologer-note-textarea');
    const notesFeedback = document.getElementById('notes-feedback');
    const suggestProductsForm = document.getElementById('suggest-products');
    const searchProductsBtn = document.getElementById('search-products-btn');
    const productSearchFeedback = document.getElementById('product-search-feedback');
    const productSearchResults = document.getElementById('product-search-results');
    const productSearchEmptyState = document.getElementById('product-search-empty-state');
    const addSuggestedProductUrl = pageData.addSuggestedProductUrl;
    const root = document.getElementById('astrologer-videosdk-meeting-root');
    const completedState = document.getElementById('consultation-ended-state');
    const sessionStateBadge = document.getElementById('session-state-badge');
    const noteAutosaveDelay = 1200;
    let noteSaveTimeout = null;
    let noteSaveInFlight = false;
    let lastSavedNote = noteTextarea ? noteTextarea.value : '';

    function setButtonLoading(button, isLoading, loadingText, defaultHtml) {
        if (!button) return;

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

    function updateTopStateBadge(status) {
        sessionStateBadge.className = 'badge';

        if (status === 'confirmed') {
            sessionStateBadge.classList.add('bg-warning', 'text-dark');
        } else if (status === 'in_progress') {
            sessionStateBadge.classList.add('bg-success');
        } else if (status === 'completed') {
            sessionStateBadge.classList.add('bg-secondary');
        } else {
            sessionStateBadge.classList.add('bg-secondary');
        }

        sessionStateBadge.style.fontSize = '1.1em';
        sessionStateBadge.textContent = status.replace('_', ' ').replace(/\b\w/g, function(char) {
            return char.toUpperCase();
        });
    }

    function showFeedback(message, type) {
//feedback.innerHTML = '<div class="alert alert-' + type + '">' + message + '</div>';
    }

    function showNotesFeedback(message, type) {
        if (!notesFeedback) {
            return;
        }

        if (!message) {
            notesFeedback.innerHTML = '';
            return;
        }

//notesFeedback.innerHTML = '<div class="alert alert-' + type + ' py-2 mb-0">' + message + '</div>';
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

    function escapeHtml(value) {
        return String(value === null || value === undefined ? '' : value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
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
                setSuggestedProductButtonState(button, 'success', result.data.message || 'Product suggested successfully.');
                showToast(result.data.message || 'Product suggested successfully.');
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
            wrapper.className = 'list-group-item suggested-product-item animate__animated animate__fadeInUp';

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
                            (hasDisplayValue(product.id) ? '<button type="button" class="btn btn-sm btn-outline-theme suggest-product-btn" data-product-id="' + escapeHtml(product.id) + '"' + (hasDisplayValue(product.variation_id) ? ' data-variation-id="' + escapeHtml(product.variation_id) + '"' : '') + '><i class="fa-solid fa-cart-plus me-1"></i> Suggest Product</button>' : '') +
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
            force: false,
            showToastOnSuccess: false,
            successMessage: 'Notes saved successfully.'
        }, options || {});

        if (!saveNotesForm || !saveNotesBtn || !noteTextarea) {
            return Promise.resolve();
        }

        const noteValue = noteTextarea.value;

        if (!config.force && noteValue === lastSavedNote) {
            return Promise.resolve();
        }

        if (noteSaveInFlight) {
            return Promise.resolve();
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
                if (config.showToastOnSuccess) {
                    showToast(result.data.message || config.successMessage);
                }
                showNotesFeedback(result.data.message || 'All changes saved.', 'success');
                return;
            }

            var errorMessage = (result.data && result.data.message) ? result.data.message : 'Failed to save notes.';
            if (result.status === 422 && result.data && result.data.errors && result.data.errors.astrologer_note) {
                errorMessage = result.data.errors.astrologer_note[0];
            }
            showNotesFeedback(errorMessage, 'danger');
        })
        .catch(function(error) {
            showNotesFeedback(error.message || 'Failed to save notes.', 'danger');
        })
        .finally(function() {
            noteSaveInFlight = false;
            setButtonLoading(saveNotesBtn, false);

            if (noteTextarea.value !== lastSavedNote) {
                scheduleNotesAutoSave();
            }
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
        noteSaveTimeout = setTimeout(function() {
            noteSaveTimeout = null;
            submitNotes();
        }, noteAutosaveDelay);
    }

    function updateTimerDisplay() {
        const m = String(Math.floor(seconds / 60)).padStart(2, '0');
        const s = String(seconds % 60).padStart(2, '0');
        timerEl.innerHTML = '<i class="fa-regular fa-clock me-1"></i>' + m + ':' + s;
    }

    function startTimer() {
        if (timerInterval) return;
        timerInterval = setInterval(function() {
            seconds++;
            updateTimerDisplay();
        }, 1000);
    }

    function stopTimer() {
        if (!timerInterval) return;
        clearInterval(timerInterval);
        timerInterval = null;
    }

    function renderCompletedState(message) {
        currentStatus = 'completed';
        joinConsultationBtn.style.display = 'none';
        endBtn.style.display = 'none';
        refreshStatusBtn.style.display = 'none';
        root.style.display = 'none';
        completedState.style.display = '';
        updateTopStateBadge('completed');
        if (message) {
            showFeedback(message, 'success');
        }
        stopTimer();
        statusText.textContent = 'Completed';
    }

    function updateSessionUI(s) {
        currentStatus = s;
        if (s === 'confirmed') {
            root.style.display = '';
            completedState.style.display = 'none';
            joinConsultationBtn.style.display = '';
            endBtn.style.display = 'none';
            refreshStatusBtn.style.display = '';
            stopTimer();
            seconds = 0;
            updateTimerDisplay();
        } else if (s === 'in_progress') {
            root.style.display = '';
            completedState.style.display = 'none';
            joinConsultationBtn.style.display = meetingInitialized ? 'none' : '';
            endBtn.style.display = '';
            refreshStatusBtn.style.display = '';
            startTimer();
        } else {
            renderCompletedState();
            return;
        }
        updateTopStateBadge(s);
        statusText.textContent = s.charAt(0).toUpperCase() + s.slice(1);
    }

    function refreshAppointmentStatus(showLoading) {
        if (showLoading) {
            setButtonLoading(refreshStatusBtn, true, '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Refreshing');
        }

        return fetch(`/astrologer/appointments/${bookingId}/ajax-status`, {
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(function(res) {
            return res.json();
        })
        .then(function(data) {
            if (!data.success || !data.status) {
                return;
            }

            if (data.status !== currentStatus) {
                updateSessionUI(data.status);
            }
        })
        .catch(function() {
            // Ignore transient polling failures.
        })
        .finally(function() {
            if (showLoading) {
                setButtonLoading(refreshStatusBtn, false);
            }
        });
    }

    function startStatusPolling() {
        if (statusPollingInterval) {
            clearInterval(statusPollingInterval);
        }
        statusPollingInterval = setInterval(refreshAppointmentStatus, 10000);
    }

    function postSession(url, onSuccess, onError) {
        feedback.innerHTML = '';
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message || 'Session updated!');
                if (onSuccess) onSuccess(data);
            } else {
                showFeedback(data.message || 'Error', 'danger');
                if (onError) onError(data);
            }
        })
        .catch(err => {
            showFeedback(err.message || 'Error', 'danger');
            if (onError) onError(err);
        });
    }

    function initMeeting() {
        if (meetingInitialized) {
            return;
        }
        meetingInitialized = true;
        joinConsultationBtn.style.display = 'none';

        try {
            const apiKey = pageData.apiKey;
            const meetingId = pageData.meetingId;
            const name = pageData.participantName;

            new window.VideoSDKMeeting().init({
                name: name,
                meetingId: meetingId,
                apiKey: apiKey,
                containerId: 'astrologer-videosdk-meeting-root',
                micEnabled: true,
                webcamEnabled: true,
                participantCanToggleSelfWebcam: true,
                participantCanToggleSelfMic: true,
                chatEnabled: true,
                screenShareEnabled: true,
                joinScreen: {
                    visible: false,
                    title: 'Join Consultation',
                    meetingUrl: window.location.href
                },
                joinWithoutUserInteraction: true,
                participantCanLeave: true,
                participantCanEndMeeting: true,
                redirectOnLeave: leaveRedirectUrl,
                notificationSoundEnabled: true,
                layout: 'GRID'
            });
        } catch (e) {
            meetingInitialized = false;
            startTriggeredByJoin = false;
            setButtonLoading(joinConsultationBtn, false);
            joinConsultationBtn.style.display = '';
            root.innerHTML = '<div class="alert alert-danger m-4">Failed to initialize video meeting: ' + e.message + '</div>';
        }
    }

    function triggerStartFromJoin() {
        if (startTriggeredByJoin || currentStatus === 'completed') {
            return;
        }

        if (currentStatus === 'in_progress') {
            initMeeting();
            updateSessionUI('in_progress');
            return;
        }

        startTriggeredByJoin = true;
        setButtonLoading(joinConsultationBtn, true, '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Joining');
        postSession(
            `/astrologer/appointments/${bookingId}/ajax-start-video-session`,
            function() {
                startTriggeredByJoin = false;
                updateSessionUI('in_progress');
                initMeeting();
            },
            function() {
                startTriggeredByJoin = false;
                setButtonLoading(joinConsultationBtn, false);
            }
        );
    }

    // Initialize timer and UI from server status
    if (endedAfterLeave) {
        renderCompletedState('Video consultation ended successfully.');
    } else if (currentStatus === 'in_progress') {
        startTimer();
    } else {
        seconds = 0;
        updateTimerDisplay();
    }
    if (!endedAfterLeave) {
        updateSessionUI(currentStatus);
    }
    refreshAppointmentStatus();
    startStatusPolling();

    if (!endedAfterLeave && currentStatus === 'in_progress') {
        initMeeting();
    }

    if (joinConsultationBtn) {
        joinConsultationBtn.addEventListener('click', function() {
            triggerStartFromJoin();
        });
    }

    if (refreshStatusBtn) {
        refreshStatusBtn.addEventListener('click', function() {
            refreshAppointmentStatus(true);
        });
    }

    if (endBtn) {
        endBtn.addEventListener('click', function() {
            postSession(`/astrologer/appointments/${bookingId}/ajax-end-video-session`, function() {
                renderCompletedState('Video consultation ended successfully.');
            });
        });
    }

    if (saveNotesForm && saveNotesBtn && noteTextarea) {
        saveNotesForm.addEventListener('submit', function(event) {
            event.preventDefault();
            submitNotes({ force: true, showToastOnSuccess: true, successMessage: 'Notes saved successfully.' });
        });

        noteTextarea.addEventListener('input', function() {
            scheduleNotesAutoSave();
        });

        noteTextarea.addEventListener('blur', function() {
            submitNotes();
        });
    }

    if (suggestProductsForm && searchProductsBtn) {
        suggestProductsForm.addEventListener('submit', function(event) {
            event.preventDefault();
            showProductSearchFeedback('', 'success');
            setButtonLoading(searchProductsBtn, true, '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Searching');

            fetch(suggestProductsForm.action, {
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

    // Bootstrap popover
    if (window.bootstrap) {
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
    }

    if (typeof window.VideoSDKMeeting !== 'function') {
        root.innerHTML = '<div class="alert alert-danger m-4">VideoSDKMeeting is not loaded. Please check your internet connection or try a different network.</div>';
        return;
    }
});
</script>
@endsection
