import '../css/support-tickets.css';

const PAGE_DATA_ID = 'support-ticket-page-data';

document.addEventListener('DOMContentLoaded', function () {
    const pageDataElement = document.getElementById(PAGE_DATA_ID);
    const form = document.getElementById('support-ticket-form');

    if (!pageDataElement || !form) {
        return;
    }

    const pageData = JSON.parse(pageDataElement.textContent || '{}');
    const submitButton = document.getElementById('support-ticket-submit-btn');
    const filterForm = document.getElementById('support-ticket-filter-form');
    const filterSubmitButton = filterForm ? filterForm.querySelector('button[type="submit"]') : null;
    const resultsCount = document.getElementById('support-ticket-results-count');
    const attachmentInput = document.getElementById('support-ticket-attachments');
    const attachmentDropzone = document.getElementById('support-ticket-dropzone');
    const attachmentList = document.getElementById('support-ticket-selected-files');
    const attachmentHelp = document.getElementById('support-ticket-attachments-help');
    const ticketList = document.getElementById('support-ticket-list');
    const emptyState = document.getElementById('support-ticket-empty-state');
    const detailModalElement = document.getElementById('support-ticket-detail-modal');
    const detailTitle = document.getElementById('support-ticket-detail-label');
    const detailBody = document.getElementById('support-ticket-detail-body');
    const detailModal = detailModalElement && window.bootstrap ? new window.bootstrap.Modal(detailModalElement) : null;
    const attachmentRules = normalizeAttachmentRules(pageData.attachmentRules || {});
    let attachmentPreviewUrls = [];

    if (detailTitle && pageData.detailTitle) {
        detailTitle.textContent = pageData.detailTitle;
    }

    if (attachmentHelp) {
        attachmentHelp.textContent = 'Optional. Up to ' + attachmentRules.maxFiles + ' files, ' + formatLimitSize(attachmentRules.maxFileSize) + ' each. Allowed: ' + attachmentRules.allowedExtensions.map(function (extension) {
            return extension.toUpperCase();
        }).join(', ') + '.';
    }

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
            input: attachmentInput,
            container: attachmentDropzone,
            error: document.getElementById('support-ticket-attachments-error')
        },
        'attachments.0': {
            input: attachmentInput,
            container: attachmentDropzone,
            error: document.getElementById('support-ticket-attachments-error')
        }
    };

    function normalizeAttachmentRules(rules) {
        return {
            maxFiles: Number(rules.maxFiles || 5),
            maxFileSize: Number(rules.maxFileSize || (5 * 1024 * 1024)),
            allowedExtensions: Array.isArray(rules.allowedExtensions) && rules.allowedExtensions.length
                ? rules.allowedExtensions.map(function (extension) {
                    return String(extension).toLowerCase();
                })
                : ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'txt', 'webp']
        };
    }

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

    function clearFieldError(field) {
        const config = fieldMap[field] || fieldMap[field.replace(/\.\d+$/, '.0')] || null;
        if (!config) {
            return;
        }

        if (config.input) {
            config.input.classList.remove('is-invalid');
        }

        if (config.container) {
            config.container.classList.remove('is-invalid');
        }

        if (config.error) {
            config.error.textContent = '';
            config.error.style.display = 'none';
        }
    }

    function clearValidationErrors() {
        Object.keys(fieldMap).forEach(function (key) {
            clearFieldError(key);
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

        if (config.container) {
            config.container.classList.add('is-invalid');
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

    function formatLimitSize(bytes) {
        return (bytes / (1024 * 1024)).toFixed(0) + ' MB';
    }

    function formatFileSize(bytes) {
        const size = Number(bytes || 0);
        if (size <= 0) {
            return '0 KB';
        }

        if (size >= 1024 * 1024) {
            return (size / (1024 * 1024)).toFixed(1).replace(/\.0$/, '') + ' MB';
        }

        return Math.max(1, Math.round(size / 1024)) + ' KB';
    }

    function revokeAttachmentPreviewUrls() {
        attachmentPreviewUrls.forEach(function (url) {
            URL.revokeObjectURL(url);
        });
        attachmentPreviewUrls = [];
    }

    function isImageFile(file) {
        if (!file) {
            return false;
        }

        if (typeof file.type === 'string' && file.type.indexOf('image/') === 0) {
            return true;
        }

        return /\.(jpg|jpeg|png|webp|gif|bmp|svg)$/i.test(String(file.name || ''));
    }

    function fileExtension(fileName) {
        const parts = String(fileName || '').toLowerCase().split('.');
        return parts.length > 1 ? parts.pop() : '';
    }

    function validateAttachmentFiles(files, options) {
        const normalizedFiles = Array.from(files || []);
        const shouldShowError = !options || options.showError !== false;

        clearFieldError('attachments');

        if (!normalizedFiles.length) {
            return true;
        }

        if (normalizedFiles.length > attachmentRules.maxFiles) {
            const message = 'You can upload up to ' + attachmentRules.maxFiles + ' files.';
            if (shouldShowError) {
                setFieldError('attachments', message);
            }
            return false;
        }

        for (let index = 0; index < normalizedFiles.length; index += 1) {
            const file = normalizedFiles[index];
            const extension = fileExtension(file.name);

            if (!attachmentRules.allowedExtensions.includes(extension)) {
                const message = '"' + (file.name || 'This file') + '" is not an allowed file type.';
                if (shouldShowError) {
                    setFieldError('attachments', message);
                }
                return false;
            }

            if (Number(file.size || 0) > attachmentRules.maxFileSize) {
                const message = '"' + (file.name || 'This file') + '" exceeds the ' + formatLimitSize(attachmentRules.maxFileSize) + ' size limit.';
                if (shouldShowError) {
                    setFieldError('attachments', message);
                }
                return false;
            }
        }

        return true;
    }

    function renderSelectedFiles(files) {
        if (!attachmentList) {
            return;
        }

        revokeAttachmentPreviewUrls();

        const normalizedFiles = Array.from(files || []);
        if (!normalizedFiles.length) {
            attachmentList.innerHTML = '';
            attachmentList.style.display = 'none';
            return;
        }

        attachmentList.innerHTML = normalizedFiles.map(function (file, index) {
            const imageFile = isImageFile(file);
            const previewUrl = imageFile ? URL.createObjectURL(file) : '';

            if (previewUrl) {
                attachmentPreviewUrls.push(previewUrl);
            }

            return '<span class="support-ticket-selected-file' + (imageFile ? ' is-image' : '') + '">' +
                (imageFile
                    ? '<img src="' + escapeHtml(previewUrl) + '" alt="' + escapeHtml(file.name || 'Attachment preview') + '" class="support-ticket-selected-file-preview">'
                    : '<i class="fa-solid fa-paperclip"></i>') +
                '<span class="support-ticket-selected-file-meta">' +
                    '<span class="support-ticket-selected-file-name">' + escapeHtml(file.name || 'Attachment') + '</span>' +
                    '<span class="support-ticket-selected-file-size">' + escapeHtml(formatFileSize(file.size)) + '</span>' +
                '</span>' +
                '<button type="button" class="support-ticket-selected-file-remove" data-file-index="' + index + '" aria-label="Remove ' + escapeHtml(file.name || 'attachment') + '"><i class="fa-solid fa-xmark"></i></button>' +
            '</span>';
        }).join('');
        attachmentList.style.display = 'flex';
    }

    function setAttachmentFiles(files) {
        if (!attachmentInput) {
            return;
        }

        const transfer = new DataTransfer();
        Array.from(files || []).forEach(function (file) {
            transfer.items.add(file);
        });
        attachmentInput.files = transfer.files;
        renderSelectedFiles(attachmentInput.files);
    }

    function removeAttachmentAt(indexToRemove) {
        if (!attachmentInput) {
            return;
        }

        const transfer = new DataTransfer();
        Array.from(attachmentInput.files || []).forEach(function (file, index) {
            if (index !== indexToRemove) {
                transfer.items.add(file);
            }
        });

        attachmentInput.files = transfer.files;
        renderSelectedFiles(attachmentInput.files);
        validateAttachmentFiles(attachmentInput.files, { showError: true });
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

    function supportTicketUrl(ticketId) {
        return String(pageData.showTicketUrlTemplate || '/astrologer/support-tickets/__TICKET__').replace('__TICKET__', encodeURIComponent(ticketId || ''));
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
                '<button type="button" class="btn btn-outline-theme btn-sm support-ticket-view-btn" data-ticket-url="' + escapeHtml(supportTicketUrl(ticket.id)) + '"><i class="fa-solid fa-eye me-1"></i> View Details</button>' +
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

    function updateResultsCount(total) {
        if (!resultsCount) {
            return;
        }

        resultsCount.textContent = String(Number(total || 0)) + ' ticket(s) found in this view.';
    }

    function renderTicketList(tickets, meta) {
        if (!ticketList) {
            return;
        }

        const normalizedTickets = Array.isArray(tickets) ? tickets : [];
        ticketList.innerHTML = normalizedTickets.map(function (ticket) {
            return renderTicketCard(ticket);
        }).join('');

        updateResultsCount(meta && typeof meta.total !== 'undefined' ? meta.total : normalizedTickets.length);
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
                Accept: 'application/json',
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

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        clearValidationErrors();

        if (!validateAttachmentFiles(attachmentInput ? attachmentInput.files : [], { showError: true })) {
            showToast(document.getElementById('support-ticket-attachments-error')?.textContent || 'Please fix the attachment errors before submitting.', true);
            return;
        }

        setButtonLoading(submitButton, true, '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Submitting');

        fetch(form.action, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
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
                    renderSelectedFiles([]);
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
            clearFieldError(field);
        });
    });

    if (attachmentInput) {
        renderSelectedFiles(attachmentInput.files);
        validateAttachmentFiles(attachmentInput.files, { showError: false });

        attachmentInput.addEventListener('change', function () {
            renderSelectedFiles(attachmentInput.files);
            validateAttachmentFiles(attachmentInput.files, { showError: true });
        });
    }

    if (attachmentList) {
        attachmentList.addEventListener('click', function (event) {
            const removeButton = event.target.closest('.support-ticket-selected-file-remove');
            if (!removeButton) {
                return;
            }

            event.preventDefault();
            removeAttachmentAt(Number(removeButton.dataset.fileIndex || -1));
        });
    }

    if (attachmentDropzone && attachmentInput) {
        ['dragenter', 'dragover'].forEach(function (eventName) {
            attachmentDropzone.addEventListener(eventName, function (event) {
                event.preventDefault();
                attachmentDropzone.classList.add('is-dragover');
            });
        });

        ['dragleave', 'dragend', 'drop'].forEach(function (eventName) {
            attachmentDropzone.addEventListener(eventName, function (event) {
                event.preventDefault();
                if (eventName === 'dragleave' && attachmentDropzone.contains(event.relatedTarget)) {
                    return;
                }
                attachmentDropzone.classList.remove('is-dragover');
            });
        });

        attachmentDropzone.addEventListener('drop', function (event) {
            const droppedFiles = event.dataTransfer ? event.dataTransfer.files : null;
            if (!droppedFiles || !droppedFiles.length) {
                return;
            }

            setAttachmentFiles(droppedFiles);
            validateAttachmentFiles(attachmentInput.files, { showError: true });
            attachmentInput.dispatchEvent(new Event('change', { bubbles: true }));
        });

        attachmentDropzone.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                attachmentInput.click();
            }
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

    if (filterForm) {
        filterForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const params = new URLSearchParams(new FormData(filterForm));
            const requestUrl = filterForm.action + '?' + params.toString();

            setButtonLoading(filterSubmitButton, true, '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>Applying');

            fetch(requestUrl, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(function (response) {
                    return response.json().then(function (data) {
                        return { ok: response.ok, data: data };
                    });
                })
                .then(function (result) {
                    if (result.ok && result.data) {
                        renderTicketList(result.data.tickets || [], result.data.meta || {});
                        window.history.replaceState({}, '', requestUrl);

                        if (result.data.message) {
                            showToast(result.data.message, true);
                        }

                        return;
                    }

                    showToast((result.data && result.data.message) ? result.data.message : 'Unable to update ticket filters.', true);
                })
                .catch(function (error) {
                    showToast(error.message || 'Unable to update ticket filters.', true);
                })
                .finally(function () {
                    setButtonLoading(filterSubmitButton, false);
                });
        });
    }

    toggleEmptyState();

    if (pageData.pageError) {
        showToast(pageData.pageError, true);
    }

    if (pageData.successMessage) {
        showToast(pageData.successMessage, false);
    }

    if (pageData.errorMessage) {
        showToast(pageData.errorMessage, true);
    }

    window.addEventListener('beforeunload', revokeAttachmentPreviewUrls);
});
