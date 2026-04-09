@extends('layouts.app')

@section('content')
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
                        <div class="dashboard-title">Astrologer Profile</div>
                        <div class="dashboard-subtitle">Review and update the profile information used on your public astrologer page.</div>
                    </div>
                </div>

                <div class="sidebar-card dashboard-card mt-4">
                    <div class="dashboard-section-head">
                        <h3>Profile Details</h3>
                    </div>

                    @if($loadError)
                        <div class="alert alert-warning">{{ $loadError }}</div>
                    @endif

                    <form id="astrologer-profile-form" autocomplete="off" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">First Name</label>
                                <input type="text" class="form-control" name="first_name" value="{{ $profile['first_name'] }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-control" name="last_name" value="{{ $profile['last_name'] }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="{{ $profile['email'] }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mobile Number</label>
                                <input type="text" class="form-control" name="mobile_no" value="{{ $profile['mobile_no'] }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Display Name</label>
                                <input type="text" class="form-control" name="display_name" value="{{ $profile['display_name'] }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Short Intro</label>
                                <input type="text" class="form-control" name="short_intro" value="{{ $profile['short_intro'] }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Detailed Bio</label>
                                <textarea class="form-control" name="details_bio" rows="5">{{ $profile['details_bio'] }}</textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Experience (Years)</label>
                                <input type="number" class="form-control" name="experience" min="0" value="{{ $profile['experience'] }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Rate</label>
                                <input type="number" class="form-control" name="rate" min="0" step="0.01" value="{{ $profile['rate'] }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Duration (Minutes)</label>
                                <input type="number" class="form-control" name="duration" min="1" value="{{ $profile['duration'] }}">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <div class="row g-4 mt-1">
                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <h5 class="mb-3">Languages</h5>
                                    <div class="profile-checkbox-grid" id="language-options">
                                        @foreach($languageOptions as $option)
                                            <label class="profile-choice">
                                                <input type="checkbox" name="languages[]" value="{{ $option['id'] }}" data-label="{{ strtolower($option['name']) }}">
                                                <span>{{ $option['name'] }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <div class="invalid-feedback d-block" data-feedback="languages"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <h5 class="mb-3">Skills</h5>
                                    <div class="profile-checkbox-grid" id="skill-options">
                                        @foreach($skillOptions as $option)
                                            <label class="profile-choice">
                                                <input type="checkbox" name="skills[]" value="{{ $option['id'] }}" data-label="{{ strtolower($option['name']) }}">
                                                <span>{{ $option['name'] }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <div class="invalid-feedback d-block" data-feedback="skills"></div>
                                </div>
                            </div>
                        </div>

                        <div class="border rounded-3 p-3 mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Education</h5>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="add-education-row">Add Education</button>
                            </div>
                            <div id="education-rows"></div>
                            <div class="invalid-feedback d-block" data-feedback="education"></div>
                        </div>

                        <div class="border rounded-3 p-3 mt-4">
                            <h5 class="mb-3">Documents</h5>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Photo</label>
                                    <input type="file" class="form-control" name="photo" accept="image/jpeg,image/png,image/jpg">
                                    <div class="form-text">Accepted: JPG, PNG, JPEG. Max 1MB.</div>
                                    <div class="mt-2" id="photo-current-wrapper">
                                        @if($profile['photo_url'])
                                            <img src="{{ $profile['photo_url'] }}" alt="Current photo" id="photo-current-preview" class="img-thumbnail mb-2 profile-photo-preview">
                                            <div><a href="{{ $profile['photo_url'] }}" target="_blank" rel="noopener" id="photo-current-link">View current photo</a></div>
                                        @else
                                            <img src="" alt="Photo preview" id="photo-current-preview" class="img-thumbnail mb-2 profile-photo-preview d-none">
                                            <div class="small text-muted" id="photo-current-link-text">No photo uploaded yet.</div>
                                            <a href="#" target="_blank" rel="noopener" id="photo-current-link" class="d-none">View uploaded photo</a>
                                        @endif
                                    </div>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Aadhaar Document</label>
                                    <input type="file" class="form-control" name="aadhar_document" accept=".pdf,image/jpeg,image/png,image/jpg">
                                    <div class="form-text">Accepted: PDF, JPG, PNG. Max 1MB.</div>
                                    <div class="mt-2 small" id="aadhar-current-wrapper">
                                        @if($profile['aadhar_document_url'])
                                            <a href="{{ $profile['aadhar_document_url'] }}" target="_blank" rel="noopener" id="aadhar-current-link">View current Aadhaar document</a>
                                        @else
                                            <span class="text-muted" id="aadhar-current-link-text">No Aadhaar document uploaded yet.</span>
                                            <a href="#" target="_blank" rel="noopener" id="aadhar-current-link" class="d-none">View uploaded Aadhaar document</a>
                                        @endif
                                    </div>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">PAN Document</label>
                                    <input type="file" class="form-control" name="pan_document" accept=".pdf,image/jpeg,image/png,image/jpg">
                                    <div class="form-text">Accepted: PDF, JPG, PNG. Max 1MB.</div>
                                    <div class="mt-2 small" id="pan-current-wrapper">
                                        @if($profile['pan_document_url'])
                                            <a href="{{ $profile['pan_document_url'] }}" target="_blank" rel="noopener" id="pan-current-link">View current PAN document</a>
                                        @else
                                            <span class="text-muted" id="pan-current-link-text">No PAN document uploaded yet.</span>
                                            <a href="#" target="_blank" rel="noopener" id="pan-current-link" class="d-none">View uploaded PAN document</a>
                                        @endif
                                    </div>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <div class="border rounded-3 p-3 mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Digital Signature</h5>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="clear-profile-signature">Clear Signature</button>
                            </div>
                            <canvas id="profile-signature-pad" class="profile-signature-pad" width="640" height="180"></canvas>
                            <input type="hidden" name="astrologer_signature_image" value="{{ $profile['astrologer_signature_image'] }}">
                            <div class="form-text">Use the pad to update your saved signature.</div>
                            <div class="invalid-feedback d-block"></div>
                        </div>

                        <div class="border rounded-3 p-3 mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Availability</h5>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="add-availability-row">Add Availability</button>
                            </div>
                            <div id="availability-rows"></div>
                            <div class="invalid-feedback d-block" data-feedback="availabilities"></div>
                        </div>

                        <div id="astrologer-profile-message" class="mt-3"></div>

                        <div class="mt-4 d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary" id="astrologer-profile-submit">Save Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .profile-checkbox-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 0.75rem;
    }

    .profile-choice {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        border: 1px solid #ece7df;
        border-radius: 10px;
        padding: 0.7rem 0.85rem;
        background: #fff9f1;
    }

    .profile-choice input {
        margin: 0;
    }

    .profile-repeat-card {
        border: 1px solid #ece7df;
        border-radius: 12px;
        padding: 1rem;
        background: #fff;
        margin-bottom: 1rem;
    }

    .profile-repeat-card .slot-row + .slot-row {
        margin-top: 0.75rem;
    }

    .profile-photo-preview {
        width: 96px;
        height: 96px;
        object-fit: cover;
    }

    .profile-signature-pad {
        width: 100%;
        min-height: 180px;
        border: 1px solid #d9d2c7;
        border-radius: 12px;
        background: linear-gradient(180deg, #fffdf8 0%, #fff7eb 100%);
        touch-action: none;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const initialProfile = @json($profile);
    const updateUrl = @json(route('astrologer.profile.update'));
    const csrfToken = document.querySelector('#astrologer-profile-form input[name="_token"]').value;
    const messageBox = document.getElementById('astrologer-profile-message');
    const educationRows = document.getElementById('education-rows');
    const availabilityRows = document.getElementById('availability-rows');
    const submitButton = document.getElementById('astrologer-profile-submit');
    const form = document.getElementById('astrologer-profile-form');
    const signatureCanvas = document.getElementById('profile-signature-pad');
    const signatureInput = form.querySelector('[name="astrologer_signature_image"]');
    const clearSignatureButton = document.getElementById('clear-profile-signature');
    const photoInput = form.querySelector('[name="photo"]');
    const aadharInput = form.querySelector('[name="aadhar_document"]');
    const panInput = form.querySelector('[name="pan_document"]');
    const maxUploadBytes = 1024 * 1024;

    function normalizeSelectionValues(values) {
        return new Set((Array.isArray(values) ? values : []).map(function (value) {
            return String(value).trim().toLowerCase();
        }));
    }

    function hydrateCheckboxes(selector, values) {
        const selected = normalizeSelectionValues(values);

        document.querySelectorAll(selector).forEach(function (checkbox) {
            const value = String(checkbox.value || '').trim().toLowerCase();
            const label = String(checkbox.dataset.label || '').trim().toLowerCase();
            checkbox.checked = selected.has(value) || selected.has(label);
        });
    }

    function educationRowTemplate(item) {
        const data = item || { degree: '', institution: '', year: '', document_url: '' };
        const documentLink = data.document_url
            ? `<a href="${data.document_url}" target="_blank" rel="noopener">View current document</a>`
            : '<span class="text-muted">No document uploaded yet.</span>';

        return `
            <div class="profile-repeat-card education-row">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Degree</label>
                        <input type="text" class="form-control" data-field="degree" value="${data.degree || ''}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Institution</label>
                        <input type="text" class="form-control" data-field="institution" value="${data.institution || ''}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Year</label>
                        <input type="number" class="form-control" data-field="year" value="${data.year || ''}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Education Document</label>
                        <input type="file" class="form-control" data-field="document" accept=".pdf,image/jpeg,image/png,image/jpg">
                        <div class="form-text">Accepted: PDF, JPG, PNG. Max 1MB.</div>
                        <div class="small mt-1" data-role="education-document-link">${documentLink}</div>
                    </div>
                    <div class="col-md-1 text-end">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-education-row">Remove</button>
                    </div>
                </div>
            </div>`;
    }

    function slotTemplate(slot) {
        const data = slot || { from: '', to: '' };

        return `
            <div class="row g-2 slot-row align-items-end">
                <div class="col-md-5">
                    <label class="form-label">From</label>
                    <input type="time" class="form-control" data-field="from" value="${data.from || ''}">
                </div>
                <div class="col-md-5">
                    <label class="form-label">To</label>
                    <input type="time" class="form-control" data-field="to" value="${data.to || ''}">
                </div>
                <div class="col-md-2 text-end">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-slot-row">Remove</button>
                </div>
            </div>`;
    }

    function availabilityRowTemplate(item) {
        const data = item || { day: 'Monday', slots: [{ from: '', to: '' }] };
        const slots = Array.isArray(data.slots) && data.slots.length ? data.slots : [{ from: '', to: '' }];
        const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        const options = days.map(function (day) {
            return `<option value="${day}" ${day === data.day ? 'selected' : ''}>${day}</option>`;
        }).join('');

        return `
            <div class="profile-repeat-card availability-row">
                <div class="row g-3 align-items-end mb-3">
                    <div class="col-md-5">
                        <label class="form-label">Day</label>
                        <select class="form-select" data-field="day">${options}</select>
                    </div>
                    <div class="col-md-7 text-end">
                        <button type="button" class="btn btn-outline-secondary btn-sm add-slot-row">Add Slot</button>
                        <button type="button" class="btn btn-outline-danger btn-sm remove-availability-row">Remove Day</button>
                    </div>
                </div>
                <div class="availability-slots">${slots.map(slotTemplate).join('')}</div>
            </div>`;
    }

    function renderEducation(items) {
        const rows = Array.isArray(items) && items.length ? items : [{ degree: '', institution: '', year: '' }];
        educationRows.innerHTML = rows.map(educationRowTemplate).join('');
    }

    function renderAvailabilities(items) {
        const rows = Array.isArray(items) && items.length ? items : [{ day: 'Monday', slots: [{ from: '', to: '' }] }];
        availabilityRows.innerHTML = rows.map(availabilityRowTemplate).join('');
    }

    function clearErrors() {
        document.querySelectorAll('#astrologer-profile-form .is-invalid').forEach(function (element) {
            element.classList.remove('is-invalid');
        });
        document.querySelectorAll('#astrologer-profile-form .invalid-feedback').forEach(function (element) {
            element.textContent = '';
        });
        messageBox.innerHTML = '';
    }

    function showMessage(type, text) {
        messageBox.innerHTML = `<div class="alert alert-${type}">${text}</div>`;
    }

    function setFieldError(fieldName, message) {
        if (fieldName.startsWith('education.')) {
            const groupFeedback = document.querySelector('#astrologer-profile-form [data-feedback="education"]');
            if (groupFeedback) {
                groupFeedback.textContent = message;
            }
            return;
        }

        if (fieldName.startsWith('availabilities.')) {
            const groupFeedback = document.querySelector('#astrologer-profile-form [data-feedback="availabilities"]');
            if (groupFeedback) {
                groupFeedback.textContent = message;
            }
            return;
        }

        const field = document.querySelector(`#astrologer-profile-form [name="${fieldName}"]`);
        if (!field) {
            const groupFeedback = document.querySelector(`#astrologer-profile-form [data-feedback="${fieldName}"]`);
            if (groupFeedback) {
                groupFeedback.textContent = message;
            }
            return;
        }

        field.classList.add('is-invalid');
        const feedback = field.parentElement.querySelector('.invalid-feedback') || field.nextElementSibling;
        if (feedback) {
            feedback.textContent = message;
        }
    }

    function collectSelection(name) {
        return Array.from(document.querySelectorAll(`#astrologer-profile-form input[name="${name}"]:checked`)).map(function (checkbox) {
            const numericValue = Number(checkbox.value);
            return Number.isNaN(numericValue) ? checkbox.value : numericValue;
        });
    }

    function collectEducation() {
        return Array.from(educationRows.querySelectorAll('.education-row')).map(function (row) {
            return {
                degree: row.querySelector('[data-field="degree"]').value.trim(),
                institution: row.querySelector('[data-field="institution"]').value.trim(),
                year: row.querySelector('[data-field="year"]').value.trim(),
                document: row.querySelector('[data-field="document"]').files[0] || null,
            };
        }).filter(function (item) {
            return item.degree || item.institution || item.year || item.document;
        });
    }

    function collectAvailabilities() {
        return Array.from(availabilityRows.querySelectorAll('.availability-row')).map(function (row) {
            const slots = Array.from(row.querySelectorAll('.slot-row')).map(function (slot) {
                return {
                    from: slot.querySelector('[data-field="from"]').value,
                    to: slot.querySelector('[data-field="to"]').value,
                };
            }).filter(function (slot) {
                return slot.from || slot.to;
            });

            return {
                day: row.querySelector('[data-field="day"]').value,
                slots: slots,
            };
        }).filter(function (item) {
            return item.day || item.slots.length;
        });
    }

    function setDocumentLink(linkId, textId, url, text) {
        const link = document.getElementById(linkId);
        const textNode = textId ? document.getElementById(textId) : null;

        if (url) {
            link.href = url;
            link.textContent = text;
            link.classList.remove('d-none');
            if (textNode) {
                textNode.textContent = '';
            }
            return;
        }

        if (textNode) {
            textNode.textContent = text;
        }

        if (link) {
            link.classList.add('d-none');
        }
    }

    function previewPhoto(file) {
        const preview = document.getElementById('photo-current-preview');
        const textNode = document.getElementById('photo-current-link-text');
        const link = document.getElementById('photo-current-link');

        if (!(file instanceof File)) {
            return;
        }

        const objectUrl = URL.createObjectURL(file);
        preview.src = objectUrl;
        preview.classList.remove('d-none');
        link.href = objectUrl;
        link.textContent = 'View selected photo';
        link.classList.remove('d-none');
        if (textNode) {
            textNode.textContent = '';
        }
    }

    function bindFileLink(input, linkId, textId, emptyText, selectedText) {
        input.addEventListener('change', function () {
            const file = input.files && input.files[0] ? input.files[0] : null;

            if (!file) {
                setDocumentLink(linkId, textId, '', emptyText);
                return;
            }

            const objectUrl = URL.createObjectURL(file);
            setDocumentLink(linkId, textId, objectUrl, selectedText);

            if (input === photoInput) {
                previewPhoto(file);
            }
        });
    }

    function validateFileSize(input, label) {
        const file = input.files && input.files[0] ? input.files[0] : null;
        if (!file) {
            return true;
        }

        if (file.size <= maxUploadBytes) {
            return true;
        }

        setFieldError(input.name, `${label} must not be greater than 1MB.`);
        return false;
    }

    function resizeCanvas() {
        const ratio = window.devicePixelRatio || 1;
        const bounds = signatureCanvas.getBoundingClientRect();
        signatureCanvas.width = Math.max(1, Math.floor(bounds.width * ratio));
        signatureCanvas.height = Math.max(1, Math.floor(180 * ratio));

        const context = signatureCanvas.getContext('2d');
        context.setTransform(1, 0, 0, 1, 0, 0);
        context.scale(ratio, ratio);
        context.lineWidth = 2;
        context.lineCap = 'round';
        context.lineJoin = 'round';
        context.strokeStyle = '#2f241d';
        context.fillStyle = '#fffdf8';
        context.fillRect(0, 0, bounds.width, 180);

        if (signatureInput.value) {
            renderSignature(signatureInput.value);
        }
    }

    function renderSignature(dataUrl) {
        if (!dataUrl) {
            return;
        }

        const image = new Image();
        image.onload = function () {
            const context = signatureCanvas.getContext('2d');
            const width = signatureCanvas.getBoundingClientRect().width;
            context.fillStyle = '#fffdf8';
            context.fillRect(0, 0, width, 180);
            context.drawImage(image, 0, 0, width, 180);
        };
        image.src = dataUrl;
    }

    function clearSignaturePad() {
        const context = signatureCanvas.getContext('2d');
        const width = signatureCanvas.getBoundingClientRect().width;
        context.fillStyle = '#fffdf8';
        context.fillRect(0, 0, width, 180);
        signatureInput.value = '';
    }

    function pointFromEvent(event) {
        const bounds = signatureCanvas.getBoundingClientRect();
        const source = event.touches ? event.touches[0] : event;

        return {
            x: source.clientX - bounds.left,
            y: source.clientY - bounds.top,
        };
    }

    hydrateCheckboxes('input[name="languages[]"]', initialProfile.languages || []);
    hydrateCheckboxes('input[name="skills[]"]', initialProfile.skills || []);
    renderEducation(initialProfile.education || []);
    renderAvailabilities(initialProfile.availabilities || []);
    bindFileLink(photoInput, 'photo-current-link', 'photo-current-link-text', 'No photo uploaded yet.', 'View selected photo');
    bindFileLink(aadharInput, 'aadhar-current-link', 'aadhar-current-link-text', 'No Aadhaar document uploaded yet.', 'View selected Aadhaar document');
    bindFileLink(panInput, 'pan-current-link', 'pan-current-link-text', 'No PAN document uploaded yet.', 'View selected PAN document');
    resizeCanvas();

    let isDrawing = false;
    let lastPoint = null;

    signatureCanvas.addEventListener('mousedown', function (event) {
        isDrawing = true;
        lastPoint = pointFromEvent(event);
    });

    signatureCanvas.addEventListener('mousemove', function (event) {
        if (!isDrawing || !lastPoint) {
            return;
        }

        const context = signatureCanvas.getContext('2d');
        const nextPoint = pointFromEvent(event);
        context.beginPath();
        context.moveTo(lastPoint.x, lastPoint.y);
        context.lineTo(nextPoint.x, nextPoint.y);
        context.stroke();
        lastPoint = nextPoint;
        signatureInput.value = signatureCanvas.toDataURL('image/png');
    });

    ['mouseup', 'mouseleave'].forEach(function (eventName) {
        signatureCanvas.addEventListener(eventName, function () {
            isDrawing = false;
            lastPoint = null;
        });
    });

    signatureCanvas.addEventListener('touchstart', function (event) {
        event.preventDefault();
        isDrawing = true;
        lastPoint = pointFromEvent(event);
    }, { passive: false });

    signatureCanvas.addEventListener('touchmove', function (event) {
        event.preventDefault();
        if (!isDrawing || !lastPoint) {
            return;
        }

        const context = signatureCanvas.getContext('2d');
        const nextPoint = pointFromEvent(event);
        context.beginPath();
        context.moveTo(lastPoint.x, lastPoint.y);
        context.lineTo(nextPoint.x, nextPoint.y);
        context.stroke();
        lastPoint = nextPoint;
        signatureInput.value = signatureCanvas.toDataURL('image/png');
    }, { passive: false });

    ['touchend', 'touchcancel'].forEach(function (eventName) {
        signatureCanvas.addEventListener(eventName, function () {
            isDrawing = false;
            lastPoint = null;
        });
    });

    clearSignatureButton.addEventListener('click', function () {
        clearSignaturePad();
    });

    window.addEventListener('resize', resizeCanvas);

    document.getElementById('add-education-row').addEventListener('click', function () {
        educationRows.insertAdjacentHTML('beforeend', educationRowTemplate());
    });

    document.getElementById('add-availability-row').addEventListener('click', function () {
        availabilityRows.insertAdjacentHTML('beforeend', availabilityRowTemplate());
    });

    educationRows.addEventListener('click', function (event) {
        if (event.target.classList.contains('remove-education-row')) {
            event.target.closest('.education-row').remove();
            if (!educationRows.children.length) {
                renderEducation([]);
            }
        }
    });

    availabilityRows.addEventListener('click', function (event) {
        if (event.target.classList.contains('remove-availability-row')) {
            event.target.closest('.availability-row').remove();
            if (!availabilityRows.children.length) {
                renderAvailabilities([]);
            }
        }

        if (event.target.classList.contains('add-slot-row')) {
            event.target.closest('.availability-row').querySelector('.availability-slots').insertAdjacentHTML('beforeend', slotTemplate());
        }

        if (event.target.classList.contains('remove-slot-row')) {
            const slotsContainer = event.target.closest('.availability-slots');
            event.target.closest('.slot-row').remove();
            if (!slotsContainer.children.length) {
                slotsContainer.insertAdjacentHTML('beforeend', slotTemplate());
            }
        }
    });

    document.getElementById('astrologer-profile-form').addEventListener('submit', function (event) {
        event.preventDefault();
        clearErrors();

        const filesAreValid = [
            validateFileSize(photoInput, 'Photo'),
            validateFileSize(aadharInput, 'Aadhaar document'),
            validateFileSize(panInput, 'PAN document'),
        ].every(Boolean) && Array.from(educationRows.querySelectorAll('[data-field="document"]')).every(function (input) {
            return validateFileSize(input, 'Education document');
        });

        if (!filesAreValid) {
            return;
        }

        submitButton.disabled = true;

        const education = collectEducation();
        const availabilities = collectAvailabilities();
        const formData = new FormData();
        formData.append('_method', 'PATCH');
        formData.append('first_name', document.querySelector('[name="first_name"]').value.trim());
        formData.append('last_name', document.querySelector('[name="last_name"]').value.trim());
        formData.append('email', document.querySelector('[name="email"]').value.trim());
        formData.append('mobile_no', document.querySelector('[name="mobile_no"]').value.trim());
        formData.append('display_name', document.querySelector('[name="display_name"]').value.trim());
        formData.append('short_intro', document.querySelector('[name="short_intro"]').value.trim());
        formData.append('details_bio', document.querySelector('[name="details_bio"]').value.trim());
        formData.append('experience', document.querySelector('[name="experience"]').value);
        formData.append('rate', document.querySelector('[name="rate"]').value);
        formData.append('duration', document.querySelector('[name="duration"]').value);

        collectSelection('languages[]').forEach(function (value) {
            formData.append('languages[]', value);
        });

        collectSelection('skills[]').forEach(function (value) {
            formData.append('skills[]', value);
        });

        education.forEach(function (item, index) {
            formData.append(`education[${index}][degree]`, item.degree);
            formData.append(`education[${index}][institution]`, item.institution);
            formData.append(`education[${index}][year]`, item.year);

            if (item.document) {
                formData.append(`education[${index}][document]`, item.document);
            }
        });

        availabilities.forEach(function (item, index) {
            formData.append(`availabilities[${index}][day]`, item.day);
            item.slots.forEach(function (slot, slotIndex) {
                formData.append(`availabilities[${index}][slots][${slotIndex}][from]`, slot.from);
                formData.append(`availabilities[${index}][slots][${slotIndex}][to]`, slot.to);
            });
        });

        if (photoInput.files && photoInput.files[0]) {
            formData.append('photo', photoInput.files[0]);
        }

        if (aadharInput.files && aadharInput.files[0]) {
            formData.append('aadhar_document', aadharInput.files[0]);
        }

        if (panInput.files && panInput.files[0]) {
            formData.append('pan_document', panInput.files[0]);
        }

        if (signatureInput.value.trim()) {
            formData.append('astrologer_signature_image', signatureInput.value.trim());
        }

        fetch(updateUrl, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: formData,
        })
            .then(function (response) {
                return response.json().then(function (data) {
                    return { ok: response.ok, data: data };
                });
            })
            .then(function (result) {
                submitButton.disabled = false;

                if (result.ok && result.data.success) {
                    showMessage('success', result.data.message || 'Profile updated successfully.');
                    return;
                }

                const errors = result.data.errors || {};
                Object.keys(errors).forEach(function (field) {
                    if (Array.isArray(errors[field]) && errors[field][0]) {
                        setFieldError(field, errors[field][0]);
                    }
                });

                showMessage('danger', result.data.message || 'Unable to update profile.');
            })
            .catch(function () {
                submitButton.disabled = false;
                showMessage('danger', 'Network error. Please try again.');
            });
    });
});
</script>
@endsection
