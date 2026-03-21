@extends('layouts.app')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <div class="card shadow-lg border-0">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #ff9800, #f57c00);">
                    <h3 class="mb-0">Astrologer Registration</h3>
                </div>
                <div class="card-body">
                    <form id="astrologer-registration-form" enctype="multipart/form-data" autocomplete="off">
                        <div id="form-message" class="mb-3"></div>
                        <!-- Step 1: Basic Information -->
                        <h5 class="mb-3">Basic Information</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">First Name *</label>
                                <input type="text" name="first_name" class="form-control" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email *</label>
                                <input type="email" name="email" class="form-control" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mobile No *</label>
                                <input type="text" name="mobile_no" class="form-control" maxlength="10" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password *</label>
                                <input type="password" name="password" class="form-control" required>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 d-flex align-items-center">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" checked>
                                    <label class="form-check-label" for="is_active">Active?</label>
                                </div>
                            </div>
                        </div>
                        <hr class="my-4">
                        <!-- Step 2: Professional Details -->
                        <h5 class="mb-3">Professional Details</h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Experience (years)</label>
                                <input type="number" name="experience" class="form-control" min="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Rate (per session)</label>
                                <input type="number" name="rate" class="form-control" min="0" step="0.01">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Duration *</label>
                                <select name="duration" class="form-select" required>
                                    <option value="15">15 min</option>
                                    <option value="30">30 min</option>
                                    <option value="60">60 min</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Languages *</label>
                                <select name="languages[]" class="form-select" multiple required id="languages-select"></select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Skills</label>
                                <select name="skills[]" class="form-select" multiple id="skills-select"></select>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="form-label">Education</label>
                            <div id="education-section">
                                <div class="row g-2 align-items-end education-entry mb-2 border rounded p-2 bg-light">
                                    <div class="col-md-3">
                                        <input type="text" name="education[0][degree]" placeholder="Degree" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" name="education[0][institution]" placeholder="Institution" class="form-control">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="education[0][year]" placeholder="Year" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="file" name="education[0][document]" class="form-control">
                                    </div>
                                    <div class="col-md-1 text-end">
                                        <button type="button" class="btn btn-danger btn-sm remove-education"><i class="bi bi-x"></i></button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" id="add-education" class="btn btn-outline-secondary btn-sm mt-2"><i class="bi bi-plus-circle"></i> Add Education</button>
                        </div>
                        <hr class="my-4">
                        <!-- Step 3: KYC Documents -->
                        <h5 class="mb-3">KYC Documents</h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">KYC Status *</label>
                                <select name="kyc_status" class="form-select" required>
                                    <option value="pending">Pending</option>
                                    <option value="approved">Approved</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Online Status *</label>
                                <select name="online_status" class="form-select" required>
                                    <option value="offline">Offline</option>
                                    <option value="online">Online</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Photo</label>
                                <input type="file" name="photo" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Aadhar Number</label>
                                <input type="text" name="aadhar_number" class="form-control" maxlength="12">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">PAN Number</label>
                                <input type="text" name="pan_number" class="form-control" maxlength="10">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Aadhar Document</label>
                                <input type="file" name="aadhar_document" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">PAN Document</label>
                                <input type="file" name="pan_document" class="form-control">
                            </div>
                        </div>
                        <hr class="my-4">
                        <!-- Step 4: Availability -->
                        <h5 class="mb-3">Availability</h5>
                        <div id="availability-section">
                            <div class="availability-entry border rounded p-2 mb-2 bg-light">
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-3">
                                        <select name="availabilities[0][day]" class="form-select">
                                            <option value="Monday">Monday</option>
                                            <option value="Tuesday">Tuesday</option>
                                            <option value="Wednesday">Wednesday</option>
                                            <option value="Thursday">Thursday</option>
                                            <option value="Friday">Friday</option>
                                            <option value="Saturday">Saturday</option>
                                            <option value="Sunday">Sunday</option>
                                        </select>
                                    </div>
                                    <div class="col-md-8 slots-section">
                                        <div class="row g-2 slot-row">
                                            <div class="col">
                                                <input type="time" name="availabilities[0][slots][0][from]" class="form-control">
                                            </div>
                                            <div class="col">
                                                <input type="time" name="availabilities[0][slots][0][to]" class="form-control">
                                            </div>
                                            <div class="col-auto">
                                                <button type="button" class="btn btn-outline-secondary btn-sm add-slot" data-avail="0"><i class="bi bi-plus"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1 text-end">
                                        <button type="button" class="btn btn-danger btn-sm remove-availability"><i class="bi bi-x"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="add-availability" class="btn btn-outline-secondary btn-sm mt-2"><i class="bi bi-plus-circle"></i> Add Availability</button>
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg" id="submit-btn">
                                <span id="submit-text">Register</span>
                                <span id="submit-spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script >
    $(document).ready(function () {
    // --- Education Section ---
    let eduIndex = 0;
    function addEducationEntry(data = {}) {
        let html = `<div class="row g-2 align-items-end education-entry mb-2 border rounded p-2 bg-light">
            <div class="col-md-3">
                <input type="text" name="education[${eduIndex}][degree]" placeholder="Degree" class="form-control" value="${data.degree || ''}">
            </div>
            <div class="col-md-3">
                <input type="text" name="education[${eduIndex}][institution]" placeholder="Institution" class="form-control" value="${data.institution || ''}">
            </div>
            <div class="col-md-2">
                <input type="number" name="education[${eduIndex}][year]" placeholder="Year" class="form-control" value="${data.year || ''}">
            </div>
            <div class="col-md-3">
                <input type="file" name="education[${eduIndex}][document]" class="form-control">
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-danger btn-sm remove-education"><i class="bi bi-x"></i></button>
            </div>
        </div>`;
        $('#education-section').append(html);
        eduIndex++;
    }
    // Add initial education entry
    function ensureAtLeastOneEducation() {
        if ($('#education-section .education-entry').length === 0) {
            addEducationEntry();
        }
    }
    addEducationEntry();
    $('#add-education').click(function () {
        addEducationEntry();
    });
    $(document).on('click', '.remove-education', function () {
        $(this).closest('.education-entry').remove();
        setTimeout(ensureAtLeastOneEducation, 10);
    });

    // --- Availability Section ---
    let availIndex = 0;
    function addAvailabilityEntry(data = {}) {
        let slotIndex = 0;
        let html = `<div class="availability-entry border rounded p-2 mb-2 bg-light">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <select name="availabilities[${availIndex}][day]" class="form-select">
                        <option value="Monday">Monday</option>
                        <option value="Tuesday">Tuesday</option>
                        <option value="Wednesday">Wednesday</option>
                        <option value="Thursday">Thursday</option>
                        <option value="Friday">Friday</option>
                        <option value="Saturday">Saturday</option>
                        <option value="Sunday">Sunday</option>
                    </select>
                </div>
                <div class="col-md-8 slots-section">
                    <div class="row g-2 slot-row">
                        <div class="col">
                            <input type="time" name="availabilities[${availIndex}][slots][0][from]" class="form-control" value="">
                        </div>
                        <div class="col">
                            <input type="time" name="availabilities[${availIndex}][slots][0][to]" class="form-control" value="">
                        </div>
                        <div class="col-auto">
                            <button type="button" class="btn btn-outline-secondary btn-sm add-slot" data-avail="${availIndex}"><i class="bi bi-plus"></i></button>
                        </div>
                    </div>
                </div>
                <div class="col-md-1 text-end">
                    <button type="button" class="btn btn-danger btn-sm remove-availability"><i class="bi bi-x"></i></button>
                </div>
            </div>
        </div>`;
        $('#availability-section').append(html);
        availIndex++;
    }
    // Add initial availability entry
    function ensureAtLeastOneAvailability() {
        if ($('#availability-section .availability-entry').length === 0) {
            addAvailabilityEntry();
        }
    }
    addAvailabilityEntry();
    $('#add-availability').click(function () {
        addAvailabilityEntry();
    });
    $(document).on('click', '.remove-availability', function () {
        $(this).closest('.availability-entry').remove();
        setTimeout(ensureAtLeastOneAvailability, 10);
    });
    // Add more slots to a day
    $(document).on('click', '.add-slot', function () {
        let availIdx = $(this).data('avail');
        let slotsSection = $(this).closest('.slots-section');
        let slotCount = slotsSection.find('.slot-row').length;
        let html = `<div class="row g-2 slot-row mt-1">
            <div class="col">
                <input type="time" name="availabilities[${availIdx}][slots][${slotCount}][from]" class="form-control" value="">
            </div>
            <div class="col">
                <input type="time" name="availabilities[${availIdx}][slots][${slotCount}][to]" class="form-control" value="">
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-outline-danger btn-sm remove-slot"><i class="bi bi-x"></i></button>
            </div>
        </div>`;
        slotsSection.append(html);
    });
    $(document).on('click', '.remove-slot', function () {
        $(this).closest('.slot-row').remove();
    });

    // --- Fetch languages and skills ---
    $.get('/api/v1/languages', function (data) {
        let options = '';
        data.forEach(function (lang) {
            options += `<option value="${lang.id}">${lang.name}</option>`;
        });
        $('#languages-select').html(options);
    });
    $.get('/api/v1/skills', function (data) {
        let options = '';
        data.forEach(function (skill) {
            options += `<option value="${skill.id}">${skill.name}</option>`;
        });
        $('#skills-select').html(options);
    });

    // --- Form Submission ---
    $('#astrologer-registration-form').submit(function (e) {
        e.preventDefault();
        // Reset errors
        $('.form-control, .form-select').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        $('#form-message').html('');
        $('#submit-btn').attr('disabled', true);
        $('#submit-spinner').removeClass('d-none');
        $('#submit-text').text('Registering...');
        let formData = new FormData(this);
        $.ajax({
            url: '/api/v1/astrologers',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $('#form-message').html(`<div class="alert alert-success">${response.message}</div>`);
                $('#astrologer-registration-form')[0].reset();
                // Remove all but first education/availability
                $('#education-section').html('');
                $('#availability-section').html('');
                eduIndex = 0; availIndex = 0;
                addEducationEntry();
                addAvailabilityEntry();
            },
            error: function (xhr) {
                let msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Registration failed.';
                $('#form-message').html(`<div class="alert alert-danger">${msg}</div>`);
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    // Laravel validation errors
                    $.each(xhr.responseJSON.errors, function (field, messages) {
                        let input = $(`[name='${field}']`);
                        if (input.length) {
                            input.addClass('is-invalid');
                            input.next('.invalid-feedback').text(messages[0]);
                        }
                    });
                }
            },
            complete: function () {
                $('#submit-btn').attr('disabled', false);
                $('#submit-spinner').addClass('d-none');
                $('#submit-text').text('Register');
            }
        });
    });
});

</script>
@endpush
