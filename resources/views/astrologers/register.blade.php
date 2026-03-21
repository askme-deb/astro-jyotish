@extends('layouts.app')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white">
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
                                <!-- Dynamic education entries here -->
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
                            <!-- Dynamic availability entries here -->
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
<script src="/js/astrologer-register.js"></script>
@endpush
