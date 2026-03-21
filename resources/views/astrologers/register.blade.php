@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Astrologer Registration</h2>
    <form id="astrologer-registration-form" enctype="multipart/form-data">
        <!-- Step 1: Basic Information -->
        <h4>Basic Information</h4>
        <div class="form-group">
            <label>First Name *</label>
            <input type="text" name="first_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="last_name" class="form-control">
        </div>
        <div class="form-group">
            <label>Email *</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Mobile No *</label>
            <input type="text" name="mobile_no" class="form-control" maxlength="10" required>
        </div>
        <div class="form-group">
            <label>Password *</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Active?</label>
            <input type="checkbox" name="is_active" value="1" checked>
        </div>
        <hr>
        <!-- Step 2: Professional Details -->
        <h4>Professional Details</h4>
        <div class="form-group">
            <label>Experience (years)</label>
            <input type="number" name="experience" class="form-control" min="0">
        </div>
        <div class="form-group">
            <label>Rate (per session)</label>
            <input type="number" name="rate" class="form-control" min="0" step="0.01">
        </div>
        <div class="form-group">
            <label>Duration *</label>
            <select name="duration" class="form-control" required>
                <option value="15">15 min</option>
                <option value="30">30 min</option>
                <option value="60">60 min</option>
            </select>
        </div>
        <div class="form-group">
            <label>Languages *</label>
            <select name="languages[]" class="form-control" multiple required id="languages-select"></select>
        </div>
        <div class="form-group">
            <label>Skills</label>
            <select name="skills[]" class="form-control" multiple id="skills-select"></select>
        </div>
        <div id="education-section">
            <label>Education</label>
            <div class="education-entry">
                <input type="text" name="education[0][degree]" placeholder="Degree" class="form-control mb-1">
                <input type="text" name="education[0][institution]" placeholder="Institution" class="form-control mb-1">
                <input type="number" name="education[0][year]" placeholder="Year" class="form-control mb-1">
                <input type="file" name="education[0][document]" class="form-control mb-1">
            </div>
            <button type="button" id="add-education" class="btn btn-sm btn-secondary">Add More</button>
        </div>
        <hr>
        <!-- Step 3: KYC Documents -->
        <h4>KYC Documents</h4>
        <div class="form-group">
            <label>KYC Status *</label>
            <select name="kyc_status" class="form-control" required>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
        <div class="form-group">
            <label>Online Status *</label>
            <select name="online_status" class="form-control" required>
                <option value="offline">Offline</option>
                <option value="online">Online</option>
            </select>
        </div>
        <div class="form-group">
            <label>Aadhar Number</label>
            <input type="text" name="aadhar_number" class="form-control" maxlength="12">
        </div>
        <div class="form-group">
            <label>PAN Number</label>
            <input type="text" name="pan_number" class="form-control" maxlength="10">
        </div>
        <div class="form-group">
            <label>Aadhar Document</label>
            <input type="file" name="aadhar_document" class="form-control">
        </div>
        <div class="form-group">
            <label>PAN Document</label>
            <input type="file" name="pan_document" class="form-control">
        </div>
        <div class="form-group">
            <label>Photo</label>
            <input type="file" name="photo" class="form-control">
        </div>
        <hr>
        <!-- Step 4: Availability -->
        <h4>Availability</h4>
        <div id="availability-section">
            <div class="availability-entry">
                <select name="availabilities[0][day]" class="form-control mb-1">
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                    <option value="Saturday">Saturday</option>
                    <option value="Sunday">Sunday</option>
                </select>
                <input type="time" name="availabilities[0][slots][0][from]" class="form-control mb-1">
                <input type="time" name="availabilities[0][slots][0][to]" class="form-control mb-1">
            </div>
            <button type="button" id="add-availability" class="btn btn-sm btn-secondary">Add More</button>
        </div>
        <hr>
        <button type="submit" class="btn btn-primary">Register</button>
        <div id="form-message" class="mt-3"></div>
    </form>
</div>
@endsection
@push('scripts')
<script src="/js/astrologer-register.js"></script>
@endpush
