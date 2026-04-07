@extends('layouts.app')

@section('content')
@php
    $formatStatusLabel = function ($value) {
        return ucwords(str_replace('_', ' ', (string) $value));
    };

    $formatDateLabel = function ($value) {
        if (!$value) {
            return '-';
        }

        try {
            return \Carbon\Carbon::parse($value)->format('d M Y, h:i A');
        } catch (\Throwable $e) {
            return (string) $value;
        }
    };

    $statusClasses = [
        'open' => 'bg-success-subtle text-success',
        'pending' => 'bg-warning-subtle text-warning',
        'in_progress' => 'bg-info-subtle text-info',
        'resolved' => 'bg-primary-subtle text-primary',
        'closed' => 'bg-secondary-subtle text-secondary',
    ];

    $pageData = [
        'pageError' => $pageError,
        'detailTitle' => 'Support Ticket Details',
        'successMessage' => session('success') ? strip_tags((string) session('success')) : null,
        'errorMessage' => session('error'),
        'showTicketUrlTemplate' => route($supportTicketPanel['routeShow'], ['ticket' => '__TICKET__']),
        'attachmentRules' => [
            'maxFiles' => 5,
            'maxFileSize' => 5 * 1024 * 1024,
            'allowedExtensions' => ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'txt', 'webp'],
        ],
    ];
@endphp

<div class="container mt-4 inner_back">
    <div class="banner">
        <img src="{{ asset('assets/images/consult.png') }}" alt="Astrology Banner">
    </div>
</div>

<section class="section-padding dashboard-layout">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3">
                <aside class="astro-sidebar">
                    @if($supportTicketPanel['panel'] === 'astrologer')
                        @include('astrologer.partials.sidebar')
                    @else
                        <div class="astro-sidebar-inner">
                            <div class="astro-profile-card">
                                <style>
                                    .astro-avatar-placeholder {
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        width: 64px;
                                        height: 64px;
                                        border-radius: 50%;
                                        background: #e0e0e0;
                                        position: relative;
                                    }

                                    .avatar-initials {
                                        font-size: 2rem;
                                        font-weight: bold;
                                        color: #555;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        width: 100%;
                                        height: 100%;
                                        border-radius: 50%;
                                        user-select: none;
                                    }

                                    .status-dot {
                                        position: absolute;
                                        bottom: 8px;
                                        right: 8px;
                                        width: 14px;
                                        height: 14px;
                                        background: #4caf50;
                                        border: 2px solid #fff;
                                        border-radius: 50%;
                                    }
                                </style>
                                <div class="astro-avatar astro-avatar-placeholder">
                                    @php
                                        $first = session('auth.user.first_name');
                                        $last = session('auth.user.last_name');
                                        $initials = '';
                                        if ($first) $initials .= strtoupper(mb_substr($first, 0, 1));
                                        if ($last) $initials .= strtoupper(mb_substr($last, 0, 1));
                                    @endphp
                                    <span class="avatar-initials">{{ $initials }}</span>
                                    <span class="status-dot"></span>
                                </div>
                                <h4 class="astro-name">{{ session('auth.user.first_name') }} {{ session('auth.user.last_name') }}</h4>
                            </div>

                            <div class="astro-menu-card">
                                <h6 class="menu-title">MAIN MENU</h6>
                                <nav class="astro-menu">
                                    <a href="{{ route('dashboard') }}">
                                        <i class="fas fa-gauge"></i> Dashboard
                                    </a>
                                    <a href="{{ route('my-bookings') }}">
                                        <i class="fas fa-calendar-check"></i> My Bookings
                                    </a>
                                    <a href="{{ route('profile') }}">
                                        <i class="fas fa-user"></i> My Profile
                                    </a>
                                    <a class="active" href="{{ route('customer.supportTickets.index') }}">
                                        <i class="fas fa-life-ring"></i> Support Tickets
                                    </a>
                                </nav>
                            </div>

                            <div class="astro-action-card">
                                <h6 class="menu-title">QUICK ACTIONS</h6>
                                <a class="btn btn-primary w-100 mb-2" href="{{ route('consultation') }}">
                                    + Book Consultation
                                </a>
                                <a class="btn btn-outline-secondary w-100" href="{{ route('home') }}">
                                    Back to Home
                                </a>
                            </div>
                        </div>
                    @endif
                </aside>
            </div>

            <div class="col-lg-9">
                <div class="sidebar-card dashboard-card dashboard-header" data-aos="fade-up" data-aos-delay="80">
                    <div class="dashboard-header-left">
                        <div class="dashboard-title">Support Tickets</div>
                        <div class="dashboard-subtitle">{{ $supportTicketPanel['headerSubtitle'] }}</div>
                    </div>
                    <div class="dashboard-header-right">
                        <a href="{{ route($supportTicketPanel['backRoute']) }}" class="btn btn-secondary btn-sm">{{ $supportTicketPanel['backLabel'] }}</a>
                    </div>
                </div>

                <div class="row g-4 mt-1">
                    <div class="col-xl-7">
                        <div class="sidebar-card dashboard-card">
                            <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center mb-3">
                                <div>
                                    <h3 class="mb-1">Your Tickets</h3>
                                    <p id="support-ticket-results-count" class="text-muted small mb-0">{{ (int) ($meta['total'] ?? count($tickets)) }} ticket(s) found in this view.</p>
                                </div>

                                <form id="support-ticket-filter-form" method="GET" action="{{ route($supportTicketPanel['routeIndex']) }}" class="support-ticket-filter-form d-flex flex-wrap gap-2 align-items-center">
                                    <select name="status" class="form-select form-select-sm">
                                        @foreach($statusOptions as $statusValue => $statusLabel)
                                            <option value="{{ $statusValue }}" @selected(($filters['status'] ?? 'open') === $statusValue)>{{ $statusLabel }}</option>
                                        @endforeach
                                    </select>

                                    <select name="per_page" class="form-select form-select-sm">
                                        @foreach([10, 15, 25, 50] as $perPageOption)
                                            <option value="{{ $perPageOption }}" @selected((int) ($filters['per_page'] ?? 15) === $perPageOption)>{{ $perPageOption }} / page</option>
                                        @endforeach
                                    </select>

                                    <button type="submit" class="btn btn-outline-secondary btn-sm">Apply</button>
                                </form>
                            </div>

                            <div id="support-ticket-list" class="support-ticket-list">
                                @forelse($tickets as $ticket)
                                    <article class="support-ticket-card" data-ticket-id="{{ $ticket['id'] }}">
                                        <div class="support-ticket-card-head">
                                            <div>
                                                <div class="support-ticket-reference">{{ $ticket['reference'] }}</div>
                                                <h4 class="support-ticket-subject mb-1">{{ $ticket['subject'] }}</h4>
                                                <div class="support-ticket-meta text-muted small">
                                                    <span>{{ $formatDateLabel($ticket['created_at']) }}</span>
                                                    <span>{{ $formatStatusLabel($ticket['context']) }}</span>
                                                </div>
                                            </div>
                                            <span class="badge rounded-pill {{ $statusClasses[$ticket['status']] ?? 'bg-light text-dark' }}">{{ $formatStatusLabel($ticket['status']) }}</span>
                                        </div>

                                        <p class="support-ticket-description mb-3">{{ \Illuminate\Support\Str::limit($ticket['description'], 180) }}</p>

                                        <div class="support-ticket-tags mb-3">
                                            <span class="badge bg-light text-dark border">Category: {{ $formatStatusLabel($ticket['category']) }}</span>
                                            @if($ticket['reason'] !== '')
                                                <span class="badge bg-light text-dark border">Reason: {{ $ticket['reason'] }}</span>
                                            @endif
                                            @if(!empty($ticket['attachments']))
                                                <span class="badge bg-light text-dark border">{{ count($ticket['attachments']) }} attachment(s)</span>
                                            @endif
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center gap-2">
                                            <div class="text-muted small">Updated: {{ $formatDateLabel($ticket['updated_at']) }}</div>
                                            <button
                                                type="button"
                                                class="btn btn-outline-theme btn-sm support-ticket-view-btn"
                                                data-ticket-url="{{ route($supportTicketPanel['routeShow'], ['ticket' => $ticket['id']]) }}"
                                            >
                                                <i class="fa-solid fa-eye me-1"></i> View Details
                                            </button>
                                        </div>
                                    </article>
                                @empty
                                @endforelse
                            </div>

                            <div id="support-ticket-empty-state" class="support-ticket-empty-state" style="display:{{ count($tickets) ? 'none' : 'block' }};">
                                <div class="support-ticket-empty-icon"><i class="fa-regular fa-life-ring"></i></div>
                                <h4 class="mb-2">No tickets in this filter</h4>
                                <p class="text-muted mb-0">{{ $supportTicketPanel['emptySubtitle'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-5">
                        <div class="sidebar-card dashboard-card">
                            <div class="dashboard-section-head mb-3">
                                <h3>Create Ticket</h3>
                                <p class="text-muted small mb-0">{{ $supportTicketPanel['createSubtitle'] }}</p>
                            </div>

                            <form id="support-ticket-form" method="POST" action="{{ route($supportTicketPanel['routeStore']) }}" enctype="multipart/form-data" novalidate>
                                @csrf

                                <div class="mb-3">
                                    <label for="support-ticket-category" class="form-label fw-semibold">Category</label>
                                    <select id="support-ticket-category" name="category" class="form-select @if($errors->supportTicket->has('category')) is-invalid @endif">
                                        <option value="">Select category</option>
                                        @foreach($categoryOptions as $categoryValue => $categoryLabel)
                                            <option value="{{ $categoryValue }}" @selected(old('category') === $categoryValue)>{{ $categoryLabel }}</option>
                                        @endforeach
                                    </select>
                                    <div id="support-ticket-category-error" class="invalid-feedback d-block" @if(!$errors->supportTicket->has('category')) style="display:none;" @endif>{{ $errors->supportTicket->first('category') }}</div>
                                </div>

                                <div class="mb-3">
                                    <label for="support-ticket-subject" class="form-label fw-semibold">Subject</label>
                                    <input id="support-ticket-subject" type="text" name="subject" class="form-control @if($errors->supportTicket->has('subject')) is-invalid @endif" value="{{ old('subject') }}" placeholder="Brief summary of the issue">
                                    <div id="support-ticket-subject-error" class="invalid-feedback d-block" @if(!$errors->supportTicket->has('subject')) style="display:none;" @endif>{{ $errors->supportTicket->first('subject') }}</div>
                                </div>

                                <div class="mb-3">
                                    <label for="support-ticket-reason" class="form-label fw-semibold">Reason</label>
                                    <input id="support-ticket-reason" type="text" name="reason" class="form-control @if($errors->supportTicket->has('reason')) is-invalid @endif" value="{{ old('reason') }}" placeholder="Short reason visible to support">
                                    <div id="support-ticket-reason-error" class="invalid-feedback d-block" @if(!$errors->supportTicket->has('reason')) style="display:none;" @endif>{{ $errors->supportTicket->first('reason') }}</div>
                                </div>

                                <div class="mb-3">
                                    <label for="support-ticket-description" class="form-label fw-semibold">Description</label>
                                    <textarea id="support-ticket-description" name="description" rows="5" class="form-control @if($errors->supportTicket->has('description')) is-invalid @endif" placeholder="{{ $supportTicketPanel['descriptionPlaceholder'] }}">{{ old('description') }}</textarea>
                                    <div id="support-ticket-description-error" class="invalid-feedback d-block" @if(!$errors->supportTicket->has('description')) style="display:none;" @endif>{{ $errors->supportTicket->first('description') }}</div>
                                </div>

                                <div class="mb-3">
                                    <label for="support-ticket-attachments" class="form-label fw-semibold">Attachments</label>
                                    <div id="support-ticket-dropzone" class="support-ticket-dropzone @if($errors->supportTicket->has('attachments') || $errors->supportTicket->has('attachments.*')) is-invalid @endif" tabindex="0" role="button" aria-describedby="support-ticket-attachments-help support-ticket-attachments-error">
                                        <div class="support-ticket-dropzone-icon"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                                        <div class="support-ticket-dropzone-copy">
                                            <strong>Drag and drop files here</strong>
                                            <span>or click to browse from your device</span>
                                        </div>
                                        <input id="support-ticket-attachments" type="file" name="attachments[]" class="support-ticket-file-input @if($errors->supportTicket->has('attachments') || $errors->supportTicket->has('attachments.*')) is-invalid @endif" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.txt,.webp">
                                    </div>
                                    <div id="support-ticket-selected-files" class="support-ticket-selected-files" style="display:none;"></div>
                                    <div id="support-ticket-attachments-help" class="form-text">Optional. Up to 5 files, 5 MB each. Allowed: JPG, PNG, PDF, DOC, DOCX, TXT, WEBP.</div>
                                    <div id="support-ticket-attachments-error" class="invalid-feedback d-block" @if(!$errors->supportTicket->has('attachments') && !$errors->supportTicket->has('attachments.*')) style="display:none;" @endif>{{ $errors->supportTicket->first('attachments') ?: $errors->supportTicket->first('attachments.*') }}</div>
                                </div>

                                <button id="support-ticket-submit-btn" type="submit" class="btn btn-primary w-100">
                                    <i class="fa-solid fa-paper-plane me-1"></i> Submit Ticket
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="support-ticket-detail-modal" tabindex="-1" aria-labelledby="support-ticket-detail-label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="support-ticket-detail-label">Support Ticket Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="support-ticket-detail-body">
                <div class="text-muted">Loading ticket details...</div>
            </div>
        </div>
    </div>
</div>

<script id="support-ticket-page-data" type="application/json">{!! json_encode($pageData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
@endsection
