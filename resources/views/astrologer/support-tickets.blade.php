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
                    @include('astrologer.partials.sidebar')
                </aside>
            </div>

            <div class="col-lg-9">
                <div class="sidebar-card dashboard-card dashboard-header" data-aos="fade-up" data-aos-delay="80">
                    <div class="dashboard-header-left">
                        <div class="dashboard-title">Support Tickets</div>
                        <div class="dashboard-subtitle">Create and track help requests for consultation, payout, and technical issues.</div>
                    </div>
                    <div class="dashboard-header-right">
                        <a href="{{ route('astrologer.dashboard') }}" class="btn btn-secondary btn-sm">Back to Dashboard</a>
                    </div>
                </div>

                <div class="row g-4 mt-1">
                    <div class="col-xl-7">
                        <div class="sidebar-card dashboard-card">
                            <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center mb-3">
                                <div>
                                    <h3 class="mb-1">Your Tickets</h3>
                                    <p class="text-muted small mb-0">{{ (int) ($meta['total'] ?? count($tickets)) }} ticket(s) found in this view.</p>
                                </div>

                                <form method="GET" action="{{ route('astrologer.supportTickets.index') }}" class="support-ticket-filter-form d-flex flex-wrap gap-2 align-items-center">
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
                                                data-ticket-url="{{ route('astrologer.supportTickets.show', ['ticket' => $ticket['id']]) }}"
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
                                <p class="text-muted mb-0">Create a ticket to contact support about consultation workflow, payouts, or technical issues.</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-5">
                        <div class="sidebar-card dashboard-card">
                            <div class="dashboard-section-head mb-3">
                                <h3>Create Ticket</h3>
                                <p class="text-muted small mb-0">Support will receive this request under the astrologer context automatically.</p>
                            </div>

                            <form id="support-ticket-form" method="POST" action="{{ route('astrologer.supportTickets.store') }}" enctype="multipart/form-data" novalidate>
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
                                    <textarea id="support-ticket-description" name="description" rows="5" class="form-control @if($errors->supportTicket->has('description')) is-invalid @endif" placeholder="Describe the issue in detail, including booking IDs or steps to reproduce when relevant.">{{ old('description') }}</textarea>
                                    <div id="support-ticket-description-error" class="invalid-feedback d-block" @if(!$errors->supportTicket->has('description')) style="display:none;" @endif>{{ $errors->supportTicket->first('description') }}</div>
                                </div>

                                <div class="mb-3">
                                    <label for="support-ticket-attachments" class="form-label fw-semibold">Attachments</label>
                                    <input id="support-ticket-attachments" type="file" name="attachments[]" class="form-control @if($errors->supportTicket->has('attachments') || $errors->supportTicket->has('attachments.*')) is-invalid @endif" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.txt,.webp">
                                    <div class="form-text">Optional. Up to 5 files, 5 MB each. Allowed: JPG, PNG, PDF, DOC, DOCX, TXT, WEBP.</div>
                                    <div id="support-ticket-attachments-error" class="invalid-feedback d-block" @if(!$errors->supportTicket->has('attachments') && !$errors->supportTicket->has('attachments.*')) style="display:none;" @endif>{{ $errors->supportTicket->first('attachments') ?: $errors->supportTicket->first('attachments.*') }}</div>
                                </div>

                                <button id="support-ticket-submit-btn" type="submit" class="btn btn-theme w-100">
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

<style>
    .support-ticket-filter-form .form-select {
        min-width: 120px;
    }

    .support-ticket-list {
        display: grid;
        gap: 1rem;
    }

    .support-ticket-card {
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 18px;
        padding: 1.1rem 1.1rem 1rem;
        background: linear-gradient(180deg, #ffffff 0%, #fffaf2 100%);
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.05);
    }

    .support-ticket-card-head {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: flex-start;
        margin-bottom: 0.8rem;
    }

    .support-ticket-reference {
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #f98700;
        margin-bottom: 0.2rem;
    }

    .support-ticket-subject {
        font-size: 1rem;
        font-weight: 700;
        color: #1f2937;
    }

    .support-ticket-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .support-ticket-description {
        color: #475569;
        line-height: 1.6;
    }

    .support-ticket-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .support-ticket-empty-state {
        border: 1px dashed rgba(15, 23, 42, 0.14);
        border-radius: 18px;
        padding: 2rem 1.25rem;
        text-align: center;
        background: #fffdf9;
    }

    .support-ticket-empty-icon {
        width: 58px;
        height: 58px;
        margin: 0 auto 0.9rem;
        border-radius: 16px;
        display: grid;
        place-items: center;
        background: linear-gradient(135deg, #fff1d6 0%, #ffe2ad 100%);
        color: #f98700;
        font-size: 1.4rem;
    }

    .support-ticket-detail-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.85rem;
        margin-bottom: 1rem;
    }

    .support-ticket-detail-card {
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 14px;
        padding: 0.9rem;
        background: #fff;
    }

    .support-ticket-detail-label {
        font-size: 0.75rem;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 0.25rem;
    }

    .support-ticket-detail-value {
        color: #1f2937;
        font-weight: 600;
    }

    .support-ticket-attachment-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.65rem;
    }

    .support-ticket-attachment-link {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.55rem 0.8rem;
        border-radius: 999px;
        border: 1px solid rgba(15, 23, 42, 0.08);
        color: #1f2937;
        text-decoration: none;
        background: #fffaf2;
    }

    @media (max-width: 767.98px) {
        .support-ticket-card-head {
            flex-direction: column;
        }

        .support-ticket-detail-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('support-ticket-form');
    const submitButton = document.getElementById('support-ticket-submit-btn');
    const ticketList = document.getElementById('support-ticket-list');
    const emptyState = document.getElementById('support-ticket-empty-state');
    const detailModalElement = document.getElementById('support-ticket-detail-modal');
    const detailBody = document.getElementById('support-ticket-detail-body');
    const pageDataElement = document.getElementById('support-ticket-page-data');
    const pageData = pageDataElement ? JSON.parse(pageDataElement.textContent || '{}') : {};
    const detailModal = detailModalElement && window.bootstrap ? new bootstrap.Modal(detailModalElement) : null;
    const fieldMap = {
        category: {
            input: document.getElementById('support-ticket-category'),
            error: document.getElementById('support-ticket-category-error')
        },
        subject: {
            input: document.getElementById('support-ticket-subject'),
            error: document.getElementById('support-ticket-subject-error')
        },
        reason: {
            input: document.getElementById('support-ticket-reason'),
            error: document.getElementById('support-ticket-reason-error')
        },
        description: {
            input: document.getElementById('support-ticket-description'),
            error: document.getElementById('support-ticket-description-error')
        },
        attachments: {
            input: document.getElementById('support-ticket-attachments'),
            error: document.getElementById('support-ticket-attachments-error')
        },
        'attachments.0': {
            input: document.getElementById('support-ticket-attachments'),
            error: document.getElementById('support-ticket-attachments-error')
        }
    };

    function escapeHtml(value) {
        return String(value === null || value === undefined ? '' : value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function showToast(message, isError) {
        if (!message) {
            return;
        }

        let toast = document.getElementById('support-ticket-toast');

        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'support-ticket-toast';
            toast.style.position = 'fixed';
            toast.style.top = '24px';
            toast.style.right = '24px';
            toast.style.zIndex = '9999';
            toast.style.padding = '12px 16px';
            toast.style.borderRadius = '10px';
            toast.style.color = '#fff';
            toast.style.fontSize = '14px';
            toast.style.fontWeight = '600';
            toast.style.maxWidth = '360px';
            toast.style.boxShadow = '0 14px 30px rgba(0,0,0,0.16)';
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-12px)';
            toast.style.transition = 'all 0.25s ease';
            document.body.appendChild(toast);
        }

        toast.textContent = message;
        toast.style.background = isError ? '#cf5a5a' : '#2f8f5b';
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';

        clearTimeout(toast._hideTimer);
        toast._hideTimer = setTimeout(function () {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-12px)';
        }, 2800);
    }

    function setButtonLoading(button, isLoading, loadingText) {
        if (!button) {
            return;
        }

        if (!button.dataset.defaultHtml) {
            button.dataset.defaultHtml = button.innerHTML;
        }

        if (isLoading) {
            button.disabled = true;
            button.innerHTML = loadingText;
            return;
        }

        button.disabled = false;
        button.innerHTML = button.dataset.defaultHtml;
    }

    function clearValidationErrors() {
        Object.keys(fieldMap).forEach(function (key) {
            const config = fieldMap[key];
            if (!config) {
                return;
            }

            if (config.input) {
                config.input.classList.remove('is-invalid');
            }

            if (config.error) {
                config.error.textContent = '';
                config.error.style.display = 'none';
            }
        });
    }

    function setFieldError(field, message) {
        const config = fieldMap[field] || fieldMap[field.replace(/\.\d+$/, '.0')] || null;
        if (!config) {
            return;
        }

        if (config.input) {
            config.input.classList.add('is-invalid');
        }

        if (config.error) {
            config.error.textContent = message;
            config.error.style.display = 'block';
        }
    }

    function firstErrorMessage(errors, fallback) {
        if (!errors || typeof errors !== 'object') {
            return fallback;
        }

        const firstKey = Object.keys(errors)[0];
        if (!firstKey) {
            return fallback;
        }

        const firstValue = errors[firstKey];
        if (Array.isArray(firstValue) && firstValue.length > 0) {
            return firstValue[0];
        }

        if (typeof firstValue === 'string' && firstValue.trim() !== '') {
            return firstValue.trim();
        }

        return fallback;
    }

    function statusLabel(value) {
        return String(value || '').replace(/_/g, ' ').replace(/\b\w/g, function (character) {
            return character.toUpperCase();
        });
    }

    function statusBadgeClass(status) {
        const statusMap = {
            open: 'bg-success-subtle text-success',
            pending: 'bg-warning-subtle text-warning',
            in_progress: 'bg-info-subtle text-info',
            resolved: 'bg-primary-subtle text-primary',
            closed: 'bg-secondary-subtle text-secondary'
        };

        return statusMap[status] || 'bg-light text-dark';
    }

    function formatDate(value) {
        if (!value) {
            return '-';
        }

        const date = new Date(value);
        if (Number.isNaN(date.getTime())) {
            return value;
        }

        return date.toLocaleString('en-IN', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function renderAttachmentLinks(attachments) {
        if (!attachments || !attachments.length) {
            return '<div class="text-muted">No attachments added.</div>';
        }

        return '<div class="support-ticket-attachment-list">' + attachments.map(function (attachment) {
            const label = escapeHtml(attachment.name || 'Attachment');
            if (!attachment.url) {
                return '<span class="support-ticket-attachment-link"><i class="fa-solid fa-paperclip"></i>' + label + '</span>';
            }

            return '<a class="support-ticket-attachment-link" href="' + escapeHtml(attachment.url) + '" target="_blank" rel="noopener noreferrer"><i class="fa-solid fa-paperclip"></i>' + label + '</a>';
        }).join('') + '</div>';
    }

    function renderTicketCard(ticket) {
        return '<article class="support-ticket-card" data-ticket-id="' + escapeHtml(ticket.id || '') + '">' +
            '<div class="support-ticket-card-head">' +
                '<div>' +
                    '<div class="support-ticket-reference">' + escapeHtml(ticket.reference || ('TKT-' + (ticket.id || ''))) + '</div>' +
                    '<h4 class="support-ticket-subject mb-1">' + escapeHtml(ticket.subject || 'Untitled support ticket') + '</h4>' +
                    '<div class="support-ticket-meta text-muted small">' +
                        '<span>' + escapeHtml(formatDate(ticket.created_at)) + '</span>' +
                        '<span>' + escapeHtml(statusLabel(ticket.context || 'astrologer')) + '</span>' +
                    '</div>' +
                '</div>' +
                '<span class="badge rounded-pill ' + statusBadgeClass(ticket.status) + '">' + escapeHtml(statusLabel(ticket.status)) + '</span>' +
            '</div>' +
            '<p class="support-ticket-description mb-3">' + escapeHtml(ticket.description || '') + '</p>' +
            '<div class="support-ticket-tags mb-3">' +
                '<span class="badge bg-light text-dark border">Category: ' + escapeHtml(statusLabel(ticket.category)) + '</span>' +
                (ticket.reason ? '<span class="badge bg-light text-dark border">Reason: ' + escapeHtml(ticket.reason) + '</span>' : '') +
                ((ticket.attachments || []).length ? '<span class="badge bg-light text-dark border">' + escapeHtml((ticket.attachments || []).length) + ' attachment(s)</span>' : '') +
            '</div>' +
            '<div class="d-flex justify-content-between align-items-center gap-2">' +
                '<div class="text-muted small">Updated: ' + escapeHtml(formatDate(ticket.updated_at)) + '</div>' +
                '<button type="button" class="btn btn-outline-theme btn-sm support-ticket-view-btn" data-ticket-url="/astrologer/support-tickets/' + encodeURIComponent(ticket.id || '') + '"><i class="fa-solid fa-eye me-1"></i> View Details</button>' +
            '</div>' +
        '</article>';
    }

    function toggleEmptyState() {
        if (!ticketList || !emptyState) {
            return;
        }

        const hasCards = ticketList.querySelectorAll('.support-ticket-card').length > 0;
        emptyState.style.display = hasCards ? 'none' : 'block';
    }

    function prependTicket(ticket) {
        if (!ticketList) {
            return;
        }

        const wrapper = document.createElement('div');
        wrapper.innerHTML = renderTicketCard(ticket);
        ticketList.prepend(wrapper.firstChild);
        toggleEmptyState();
    }

    function renderDetail(ticket) {
        return '<div class="support-ticket-detail-grid">' +
            '<div class="support-ticket-detail-card"><div class="support-ticket-detail-label">Ticket</div><div class="support-ticket-detail-value">' + escapeHtml(ticket.reference || ('TKT-' + (ticket.id || ''))) + '</div></div>' +
            '<div class="support-ticket-detail-card"><div class="support-ticket-detail-label">Status</div><div class="support-ticket-detail-value">' + escapeHtml(statusLabel(ticket.status)) + '</div></div>' +
            '<div class="support-ticket-detail-card"><div class="support-ticket-detail-label">Category</div><div class="support-ticket-detail-value">' + escapeHtml(statusLabel(ticket.category)) + '</div></div>' +
            '<div class="support-ticket-detail-card"><div class="support-ticket-detail-label">Created</div><div class="support-ticket-detail-value">' + escapeHtml(formatDate(ticket.created_at)) + '</div></div>' +
        '</div>' +
        '<div class="mb-3"><div class="support-ticket-detail-label">Subject</div><div class="support-ticket-detail-value">' + escapeHtml(ticket.subject || '') + '</div></div>' +
        (ticket.reason ? '<div class="mb-3"><div class="support-ticket-detail-label">Reason</div><div class="support-ticket-detail-value">' + escapeHtml(ticket.reason) + '</div></div>' : '') +
        '<div class="mb-3"><div class="support-ticket-detail-label">Description</div><div class="support-ticket-detail-card"><div class="mb-0" style="white-space:pre-wrap;">' + escapeHtml(ticket.description || '') + '</div></div></div>' +
        '<div><div class="support-ticket-detail-label">Attachments</div>' + renderAttachmentLinks(ticket.attachments || []) + '</div>';
    }

    function loadTicketDetails(url) {
        if (!url || !detailBody) {
            return;
        }

        detailBody.innerHTML = '<div class="text-muted">Loading ticket details...</div>';
        if (detailModal) {
            detailModal.show();
        }

        fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(function (response) {
            return response.json().then(function (data) {
                return { ok: response.ok, data: data };
            });
        })
        .then(function (result) {
            if (result.ok && result.data.success && result.data.ticket) {
                detailBody.innerHTML = renderDetail(result.data.ticket);
                return;
            }

            detailBody.innerHTML = '<div class="alert alert-danger mb-0">' + escapeHtml((result.data && result.data.message) ? result.data.message : 'Unable to load ticket details.') + '</div>';
        })
        .catch(function (error) {
            detailBody.innerHTML = '<div class="alert alert-danger mb-0">' + escapeHtml(error.message || 'Unable to load ticket details.') + '</div>';
        });
    }

    if (form && submitButton) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            clearValidationErrors();
            setButtonLoading(submitButton, true, '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Submitting');

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new FormData(form)
            })
            .then(function (response) {
                return response.json().then(function (data) {
                    return { ok: response.ok, data: data };
                });
            })
            .then(function (result) {
                if (result.ok && result.data.success) {
                    form.reset();
                    clearValidationErrors();
                    if (result.data.ticket) {
                        prependTicket(result.data.ticket);
                    }
                    showToast(result.data.message || 'Support ticket created successfully.', false);
                    return;
                }

                const errors = result.data && result.data.errors ? result.data.errors : null;
                if (errors && typeof errors === 'object') {
                    Object.keys(errors).forEach(function (field) {
                        const value = errors[field];
                        if (Array.isArray(value) && value.length > 0) {
                            setFieldError(field, value[0]);
                        } else if (typeof value === 'string' && value.trim() !== '') {
                            setFieldError(field, value.trim());
                        }
                    });
                }

                showToast(firstErrorMessage(errors, (result.data && result.data.message) ? result.data.message : 'Failed to create support ticket.'), true);
            })
            .catch(function (error) {
                showToast(error.message || 'Failed to create support ticket.', true);
            })
            .finally(function () {
                setButtonLoading(submitButton, false);
            });
        });

        Object.keys(fieldMap).forEach(function (field) {
            const config = fieldMap[field];
            if (!config || !config.input) {
                return;
            }

            config.input.addEventListener(field === 'category' ? 'change' : 'input', function () {
                config.input.classList.remove('is-invalid');
                if (config.error) {
                    config.error.textContent = '';
                    config.error.style.display = 'none';
                }
            });
        });
    }

    if (ticketList) {
        ticketList.addEventListener('click', function (event) {
            const button = event.target.closest('.support-ticket-view-btn');
            if (!button) {
                return;
            }

            event.preventDefault();
            loadTicketDetails(button.dataset.ticketUrl || '');
        });
    }

    toggleEmptyState();

    if (pageData.pageError) {
        showToast(pageData.pageError, true);
    }

    @if(session('success'))
        showToast(@json(strip_tags(session('success'))), false);
    @endif

    @if(session('error'))
        showToast(@json(session('error')), true);
    @endif
});
</script>
@endsection
