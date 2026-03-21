$(document).ready(function() {
    // Dynamic add education
    let eduIndex = 1;
    $('#add-education').click(function() {
        let html = `<div class="education-entry">
            <input type="text" name="education[${eduIndex}][degree]" placeholder="Degree" class="form-control mb-1">
            <input type="text" name="education[${eduIndex}][institution]" placeholder="Institution" class="form-control mb-1">
            <input type="number" name="education[${eduIndex}][year]" placeholder="Year" class="form-control mb-1">
            <input type="file" name="education[${eduIndex}][document]" class="form-control mb-1">
            <button type="button" class="btn btn-sm btn-danger remove-education">Remove</button>
        </div>`;
        $('#education-section').append(html);
        eduIndex++;
    });
    $(document).on('click', '.remove-education', function() {
        $(this).closest('.education-entry').remove();
    });

    // Dynamic add availability
    let availIndex = 1;
    $('#add-availability').click(function() {
        let html = `<div class="availability-entry">
            <select name="availabilities[${availIndex}][day]" class="form-control mb-1">
                <option value="Monday">Monday</option>
                <option value="Tuesday">Tuesday</option>
                <option value="Wednesday">Wednesday</option>
                <option value="Thursday">Thursday</option>
                <option value="Friday">Friday</option>
                <option value="Saturday">Saturday</option>
                <option value="Sunday">Sunday</option>
            </select>
            <input type="time" name="availabilities[${availIndex}][slots][0][from]" class="form-control mb-1">
            <input type="time" name="availabilities[${availIndex}][slots][0][to]" class="form-control mb-1">
            <button type="button" class="btn btn-sm btn-danger remove-availability">Remove</button>
        </div>`;
        $('#availability-section').append(html);
        availIndex++;
    });
    $(document).on('click', '.remove-availability', function() {
        $(this).closest('.availability-entry').remove();
    });

    // Fetch languages and skills
    $.get('/api/v1/languages', function(data) {
        let options = '';
        data.forEach(function(lang) {
            options += `<option value="${lang.id}">${lang.name}</option>`;
        });
        $('#languages-select').html(options);
    });
    $.get('/api/v1/skills', function(data) {
        let options = '';
        data.forEach(function(skill) {
            options += `<option value="${skill.id}">${skill.name}</option>`;
        });
        $('#skills-select').html(options);
    });

    // Submit form
    $('#astrologer-registration-form').submit(function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        $.ajax({
            url: '/api/v1/astrologers',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#form-message').html(`<div class="alert alert-success">${response.message}</div>`);
                $('#astrologer-registration-form')[0].reset();
            },
            error: function(xhr) {
                let msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Registration failed.';
                $('#form-message').html(`<div class="alert alert-danger">${msg}</div>`);
            }
        });
    });
});
