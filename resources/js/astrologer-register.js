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
