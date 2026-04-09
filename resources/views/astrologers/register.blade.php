@extends('layouts.app')
@section('content')
<style>
    .bg-primary {
        background: linear-gradient(135deg, #ff9800, #f57c00) !important;
    }
    .text-warning{
        color: #f57c00 !important;
    }
    .bg-secondary {
        background: #ffe0b2 !important;
        color: #a77946  !important;
    }
    .text-primary{
        color: #f57c00 !important;
    }

    /* Custom style for checked checkboxes/radios */
    .form-check-input:checked {
        background-color: #f57c00 !important;
        border-color: #f57c00 !important;
    }
</style>
<div class="astrologerReg" style="min-height:100vh;">
 <div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-10">
            <div class="card shadow-lg border-0 p-3 p-md-4" style="border-radius: 18px;">
                <div class="card-header text-white d-flex align-items-center gap-3" style="background: linear-gradient(135deg, #ff9800, #f57c00); border-radius: 14px;">
                    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Astrologer Avatar" class="reg-avatar">
                    <div>
                        <h3 class="mb-0 text-white" style="color: #ffffff !important;">Astrologer Registration</h3>
                        <div class="text-white-50 small text-light" style="color: #ffffff !important;">Join our expert platform

                        </div>
                    </div>
                </div>
                <div class="card-body px-1 px-md-4 py-4">

                    <!-- Step Indicator -->

                        <div class="step-indicator mb-1" id="step-indicator">
                            <div class="step active" data-step="1"></div>
                            <div class="step" data-step="2"></div>
                            <div class="step" data-step="3"></div>
                            <div class="step" data-step="4"></div>
                            <div class="step" data-step="5"></div>
                            <div class="step" data-step="6"></div>
                            <div class="step" data-step="7"></div>
                            <div class="step" data-step="8"></div>
                        </div>

                        <!-- Step labels row -->
                        <div class="d-flex justify-content-between mb-4 px-1" style="font-size:13px; color:#f57c00; font-weight:600; letter-spacing:.3px;">
                            <span>Basic Info</span>
                            <span>Profile</span>
                            <span>Education</span>
                            <span>Professional</span>
                            <span>Availability</span>
                            <span>KYC</span>
                            <span>Banking</span>
                            <span>Agreement</span>
                        </div>

                    <form id="astrologer-registration-form" enctype="multipart/form-data" autocomplete="off">
                        @csrf


                        <!-- ── Step 1: Basic Information ── -->
                        <div class="form-section" data-section="1"> <!-- Basic Info -->
                            <div class="card mb-4 shadow-sm border-0" style="border-radius: 14px;">
                                <div class="card-body pb-1">
                                    <h5 class="mb-3 text-primary"><i class="bi bi-person-circle me-2"></i>Basic Information</h5>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">First Name * <i class="bi bi-info-circle help-icon" data-bs-toggle="tooltip" title="Enter your legal first name."></i></label>
                                            <input type="text" name="astrologer_first_name" class="form-control" required>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Last Name * <i class="bi bi-info-circle help-icon" data-bs-toggle="tooltip" title="Enter your legal last name."></i></label>
                                            <input type="text" name="astrologer_last_name" class="form-control">
                                                <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Email * <i class="bi bi-info-circle help-icon" data-bs-toggle="tooltip" title="We'll never share your email."></i></label>
                                            <input type="email" name="astrologer_email" class="form-control" required>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Mobile No * <i class="bi bi-info-circle help-icon" data-bs-toggle="tooltip" title="10-digit mobile number."></i></label>
                                            <input type="text" name="astrologer_mobile_no" class="form-control" maxlength="10" required>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Password * <i class="bi bi-info-circle help-icon" data-bs-toggle="tooltip" title="Minimum 8 characters."></i></label>
                                            <input type="password" name="astrologer_password" class="form-control" required>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Display Name * <i class="bi bi-info-circle help-icon" data-bs-toggle="tooltip" title="This will be visible to clients."></i></label>
                                            <input type="text" name="astrologer_display_name" class="form-control" required>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ── Step 2: Profile Details ── -->
                        <div class="form-section d-none" data-section="2"> <!-- Profile -->
                            <div class="card mb-4 shadow-sm border-0" style="border-radius: 14px;">
                                <div class="card-body pb-1">
                                    <h5 class="mb-3 text-primary"><i class="bi bi-person-lines-fill me-2"></i>Profile Details</h5>
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <label class="form-label">Short Intro <i class="bi bi-info-circle help-icon" data-bs-toggle="tooltip" title="A brief summary about you (max 200 chars)."></i></label>
                                            <textarea name="astrologer_short_intro" class="form-control" maxlength="200" rows="3"></textarea>
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label">Details Bio <span class="text-muted">(min 150-200 words)</span> * <i class="bi bi-info-circle help-icon" data-bs-toggle="tooltip" title="Describe your background, approach, and expertise."></i></label>
                                            <textarea name="astrologer_details_bio" class="form-control" rows="4" minlength="900" required></textarea>
                                            <div class="form-text" id="bio-word-count">Words left: 150</div>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                    <div class="row g-3 mt-2">
                                        <div class="col-md-12">
                                            <label class="form-label">Address * <i class="bi bi-info-circle help-icon" data-bs-toggle="tooltip" title="Street address, house no., etc."></i></label>
                                            <input type="text" name="astrologer_address" class="form-control mb-2" placeholder="Street address, house no., etc.">
                                            <div class="row g-2">
                                                <div class="col-md-4">
                                                    <label class="form-label">State * </label>
                                                    <select name="astrologer_state" id="astrologer_state" class="form-select" required>
                                                        <option value="">Select State</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">City * </label>
                                                    <select name="astrologer_city" id="astrologer_city" class="form-select" required>
                                                        <option value="">Select City</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label">Pin Code * </label>
                                                    <input type="text" name="astrologer_pin" class="form-control" placeholder="Pin Code" required maxlength="6" pattern="\d{6}">
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Consultation Mode <i class="bi bi-info-circle help-icon" data-bs-toggle="tooltip" title="Choose your preferred consultation mode."></i></label>
                                            <select name="astrologer_consultation_mode" class="form-select">
                                                <option value="video">Video</option>
                                                <option value="audio">Audio</option>
                                                {{-- <option value="chat">Chat</option> --}}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ── Step 3: Education ── -->
                        <div class="form-section d-none" data-section="3"> <!-- Education -->
                            <div class="card mb-4 shadow-sm border-0" style="border-radius: 14px;">
                                <div class="card-body pb-1">
                                    <h5 class="mb-3 text-primary"><i class="bi bi-mortarboard me-2"></i>Education</h5>
                                    <div id="education-section"></div>
                                    <div class="col-lg-3">
                                        <button type="button" id="add-education" class="btn btn-outline-secondary btn-sm mt-2 w-100"><i
                                                class="bi bi-plus-circle"></i> Add Education</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ── Step 4: Professional Details ── -->
                        <div class="form-section d-none" data-section="4"> <!-- Professional -->
                            <div class="card mb-4 shadow-sm border-0" style="border-radius: 14px;">
                                <div class="card-body pb-1">
                                    <h5 class="mb-3 text-primary"><i class="bi bi-briefcase me-2"></i>Professional Details</h5>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Experience (years) * <i class="bi bi-info-circle help-icon" data-bs-toggle="tooltip" title="Total years of professional experience."></i></label>
                                            <input type="number" name="astrologer_experience" class="form-control" min="0" required>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Rate (per session) * <i class="bi bi-info-circle help-icon" data-bs-toggle="tooltip" title="Your consultation fee."></i></label>
                                            <input type="number" name="astrologer_rate" class="form-control" min="0" step="0.01" required>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Duration * <i class="bi bi-info-circle help-icon" data-bs-toggle="tooltip" title="Session duration is fixed at 30 minutes."></i></label>
                                            <select name="astrologer_duration" class="form-select" required>
                                                <option value="30">30 min</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Languages * <i class="bi bi-info-circle help-icon" data-bs-toggle="tooltip" title="Select all languages you can consult in."></i></label>
                                            <div id="languages-badges" class="d-flex flex-wrap gap-2"></div>
                                            <input type="hidden" name="astrologer_languages" id="astrologer_languages-hidden" required>
                                            <div class="invalid-feedback d-block" id="languages-feedback"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Skills * <i class="bi bi-info-circle help-icon" data-bs-toggle="tooltip" title="Select your astrological skills."></i></label>
                                            <div id="skills-badges" class="d-flex flex-wrap gap-2"></div>
                                            <input type="hidden" name="astrologer_skills" id="astrologer_skills-hidden" required>
                                            <div class="invalid-feedback d-block" id="skills-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ── Step 5: Availability ── -->
                        <div class="form-section d-none" data-section="5"> <!-- Availability -->
                            <div class="card mb-4 shadow-sm border-0" style="border-radius: 14px;">
                                <div class="card-body pb-1">
                                    <h5 class="mb-3 text-primary"><i class="bi bi-calendar-check me-2"></i>Availability</h5>
                                    <div id="availability-section"></div>
                                    <div class="col-lg-3">
                                        <button type="button" id="add-availability" class="btn btn-outline-secondary btn-sm mt-2 w-100"><i
                                                class="bi bi-plus-circle"></i> Add Availability</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ── Step 6: KYC Details ── -->
                        <div class="form-section d-none" data-section="6"> <!-- KYC -->
                            <div class="card mb-4 shadow-sm border-0" style="border-radius: 14px;">
                                <div class="card-body pb-1">
                                    <h5 class="mb-3 text-primary"><i class="bi bi-shield-check me-2"></i>KYC Details</h5>
                                    <div class="row g-3">

                                        <div class="col-md-6">
                                            <label class="form-label">Aadhar Number *</label>
                                            <input type="text" name="astrologer_aadhar_number" class="form-control" maxlength="12" required>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">PAN Number *</label>
                                            <input type="text" name="astrologer_pan_number" class="form-control" maxlength="10" required>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Photo *
                                                <i class="bi bi-info-circle help-icon" data-bs-toggle="tooltip" title="Upload a recent, clear passport-size photo. JPG, PNG, or JPEG format. Max size: 1MB. Face should be clearly visible, no sunglasses or hats."></i>
                                            </label>
                                            <div id="photo-dropzone" class="border border-2 border-dashed rounded-3 p-3 text-center bg-light position-relative" style="cursor:pointer; min-height: 140px; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                                                <div id="photo-preview" class="mb-2" style="display:none;"></div>
                                                <div id="photo-dropzone-text">
                                                    <i class="bi bi-cloud-arrow-up" style="font-size:2rem;color:#f57c00;"></i><br>
                                                    <span class="text-muted">Drag & drop photo here<br>or <span class="text-primary text-decoration-underline" style="cursor:pointer;">browse</span></span>
                                                </div>
                                                <input type="file" name="astrologer_photo" id="astrologer_photo" class="form-control position-absolute top-0 start-0 w-100 h-100 opacity-0" accept="image/*" required style="z-index:2;cursor:pointer;">
                                            </div>
                                            <div class="form-text text-muted small">Accepted: JPG, PNG, JPEG. Max 1MB. Face must be clearly visible.</div>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Aadhar Document (PDF/JPG/PNG) *</label>
                                            <div id="aadhar-dropzone" class="border border-2 border-dashed rounded-3 p-3 text-center bg-light position-relative" style="cursor:pointer; min-height: 100px; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                                                <div id="aadhar-preview" class="mb-2" style="display:none;"></div>
                                                <div id="aadhar-dropzone-text">
                                                    <i class="bi bi-cloud-arrow-up" style="font-size:2rem;color:#f57c00;"></i><br>
                                                    <span class="text-muted">Drag & drop Aadhar file here<br>or <span class="text-primary text-decoration-underline" style="cursor:pointer;">browse</span></span>
                                                </div>
                                                <input type="file" name="astrologer_aadhar_document" id="astrologer_aadhar_document" class="form-control position-absolute top-0 start-0 w-100 h-100 opacity-0" accept=".pdf,image/*" required style="z-index:2;cursor:pointer;">
                                            </div>
                                            <div class="form-text text-muted small">Accepted: PDF, JPG, PNG. Max 1MB.</div>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">PAN Document (PDF/JPG/PNG) *</label>
                                            <div id="pan-dropzone" class="border border-2 border-dashed rounded-3 p-3 text-center bg-light position-relative" style="cursor:pointer; min-height: 100px; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                                                <div id="pan-preview" class="mb-2" style="display:none;"></div>
                                                <div id="pan-dropzone-text">
                                                    <i class="bi bi-cloud-arrow-up" style="font-size:2rem;color:#f57c00;"></i><br>
                                                    <span class="text-muted">Drag & drop PAN file here<br>or <span class="text-primary text-decoration-underline" style="cursor:pointer;">browse</span></span>
                                                </div>
                                                <input type="file" name="astrologer_pan_document" id="astrologer_pan_document" class="form-control position-absolute top-0 start-0 w-100 h-100 opacity-0" accept=".pdf,image/*" required style="z-index:2;cursor:pointer;">
                                            </div>
                                            <div class="form-text text-muted small">Accepted: PDF, JPG, PNG. Max 1MB.</div>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ── Step 7: Banking Details ── -->
                        <div class="form-section d-none" data-section="7"> <!-- Banking -->
                            <div class="card mb-4 shadow-sm border-0" style="border-radius: 14px;">
                                <div class="card-body pb-1">
                                    <h5 class="mb-3 text-primary"><i class="bi bi-bank2 me-2"></i>Bank Details</h5>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">A/c Holder Name * <i class="bi bi-info-circle help-icon" data-bs-toggle="tooltip" title="Name as per bank records."></i></label>
                                            <input type="text" name="astrologer_bank_holder_name" class="form-control">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Bank Name * <i class="bi bi-info-circle help-icon" data-bs-toggle="tooltip" title="Your bank's name."></i></label>
                                            <input type="text" name="astrologer_bank_name" class="form-control">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">A/c Number * <i class="bi bi-info-circle help-icon" data-bs-toggle="tooltip" title="Your bank account number."></i></label>
                                            <input type="text" name="astrologer_bank_account_number" class="form-control">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">IFSC Code * <i class="bi bi-info-circle help-icon" data-bs-toggle="tooltip" title="Bank IFSC code."></i></label>
                                            <input type="text" name="astrologer_ifsc_code" class="form-control">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Branch Name * <i class="bi bi-info-circle help-icon" data-bs-toggle="tooltip" title="Bank branch name."></i></label>
                                            <input type="text" name="astrologer_branch_name" class="form-control">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">UPI ID *
                                                <i class="bi bi-info-circle help-icon" data-bs-toggle="tooltip" title="Enter your valid UPI ID (e.g. yourname@bank). This will be used for payments."></i>
                                            </label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white"><i class="bi bi-upc-scan text-primary" title="UPI"></i></span>
                                                <input type="text" name="astrologer_upi_id" id="astrologer_upi_id" class="form-control" placeholder="yourname@bank" autocomplete="off">
                                                <button type="button" class="btn btn-outline-secondary" id="clear-upi" tabindex="-1" title="Clear UPI ID"><i class="bi bi-x"></i></button>
                                            </div>
                                            <div class="form-text text-muted small">Example: yourname@okicici, mobile@upi, etc. Must be a valid UPI VPA.</div>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ── Step 8: Platform Agreement + Declaration ── -->
                        <div class="form-section d-none" data-section="8"> <!-- Agreement -->
                            <div class="card mb-4 shadow-sm border-0" style="border-radius: 14px;">
                                <div class="card-body pb-1">
                                    <h5 class="mb-3 text-primary"><i class="bi bi-clipboard-check me-2"></i>PLATFORM AGREEMENT</h5>
                                    <div class="mb-3 ps-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="astrologer_agreement_info_true" id="astrologer_agreement_info_true" required>
                                            <label class="form-check-label" for="agreement_info_true">I confirm that the information provided by me is true and correct.</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="astrologer_agreement_guidelines" id="astrologer_agreement_guidelines" required>
                                            <label class="form-check-label" for="agreement_guidelines">I agree to follow the ethical and professional guidelines of the platform.</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="astrologer_agreement_no_false_promises" id="astrologer_agreement_no_false_promises" required>
                                            <label class="form-check-label" for="agreement_no_false_promises">I understand that I must not give false promises or fear-based consultations.</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="astrologer_agreement_no_guarantees" id="astrologer_agreement_no_guarantees" required>
                                            <label class="form-check-label" for="agreement_no_guarantees">I understand that I must not provide medical, legal, or financial guarantees.</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="astrologer_agreement_commission_terms" id="astrologer_agreement_commission_terms" required>
                                            <label class="form-check-label" for="agreement_commission_terms">I agree to the platform's commission, payout, and service terms.</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="astrologer_agreement_profile_review" id="astrologer_agreement_profile_review" required>
                                            <label class="form-check-label" for="agreement_profile_review">I agree that my profile may be reviewed before approval.</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-4 shadow-sm border-0" style="border-radius: 14px;">
                                <div class="card-body pb-1">
                                    <h5 class="mb-3 text-primary"><i class="bi bi-file-earmark-text me-2"></i>DECLARATION</h5>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Applicant Name <i class="bi bi-info-circle help-icon" data-bs-toggle="tooltip" title="Your full name for declaration."></i></label>
                                            <input type="text" name="astrologer_declaration_applicant_name" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Signature <i class="bi bi-info-circle help-icon" data-bs-toggle="tooltip" title="Use the digital pad to sign."></i></label>
                                            <canvas id="signature-pad" width="300" height="100" style="border:1px solid #ccc; width:100%;"></canvas>
                                            <input type="hidden" name="astrologer_signature_image" id="astrologer_signature_image">
                                            <div class="invalid-feedback d-block"></div>
                                            <div class="col-lg-6">
                                                <button type="button" class="btn btn-sm btn-outline-secondary mt-2 w-100" id="clear-signature">Clear
                                                    Signature</button>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Date <i class="bi bi-info-circle help-icon" data-bs-toggle="tooltip" title="Today's date."></i></label>
                                            <input type="date" name="astrologer_declaration_date" class="form-control" value="{{ date('Y-m-d') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <button type="button" id="btn-prev" class="btn btn-outline-secondary px-4" style="border-radius:10px;">
                                <i class="bi bi-arrow-left me-1"></i> Back
                            </button>
                            <span class="text-muted small fw-semibold" id="step-counter">Step 1 of 8</span>
                            <button type="button" id="btn-next" class="btn btn-primary btn-lg shadow px-5" style="border-radius: 10px;">
                                <span id="next-text">Next <i class="bi bi-arrow-right ms-1"></i></span>
                                <span id="submit-spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                            <div id="form-message" class="mb-3 mt-3"></div>
                        <!-- Success Modal -->
                        <div class="modal fade" id="reg-success-modal" tabindex="-1" aria-labelledby="regSuccessLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-body text-center p-4">
                                        <i class="bi bi-check-circle-fill text-warning" style="font-size:3rem;"></i>
                                        <h4 class="mt-3">Registration Successful!</h4>
                                        <p class="mb-0">Thank you for registering. Our team will review your profile soon.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@endsection
@push('scripts')
<script>
$(document).ready(function () {
    // ── UPI ID clear button ─────────────────────────────
    $('#clear-upi').on('click', function() {
        $('#astrologer_upi_id').val('').focus();
    });
    // ── Photo Drag & Drop ─────────────────────────────────────
    const photoDropzone = $('#photo-dropzone');
    const photoInput = $('#astrologer_photo');
    const photoPreview = $('#photo-preview');
    const dropzoneText = $('#photo-dropzone-text');
    const photoMaxSize = 1024 * 1024;

    function setPhotoError(message) {
        const feedback = photoInput.closest('.col-md-4').find('.invalid-feedback').last();
        photoInput.addClass('is-invalid');
        feedback.addClass('d-block').text(message);
    }

    function clearPhotoError() {
        const feedback = photoInput.closest('.col-md-4').find('.invalid-feedback').last();
        photoInput.removeClass('is-invalid');
        feedback.removeClass('d-block').text('');
    }

    function showPhotoPreview(file) {
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            photoPreview.html(`<img src="${e.target.result}" alt="Photo Preview" class="img-thumbnail" style="max-width:100px;max-height:100px;">`);
            photoPreview.show();
            dropzoneText.hide();
        };
        reader.readAsDataURL(file);
    }

    photoDropzone.on('click', function(e) {
        if (e.target.id === 'photo-dropzone' || $(e.target).hasClass('text-decoration-underline')) {
            photoInput.trigger('click');
        }
    });

    photoInput.on('change', function(e) {
        const file = this.files && this.files[0];
        if (file) {
            showPhotoPreview(file);
        } else {
            photoPreview.hide();
            dropzoneText.show();
        }

        if (!file) {
            setPhotoError('Photo is required.');
            return;
        }

        if (file.size > photoMaxSize) {
            setPhotoError('Photo must not be greater than 1MB.');
            return;
        }

        clearPhotoError();
    });

    photoDropzone.on('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        photoDropzone.addClass('border-warning bg-white');
    });
    photoDropzone.on('dragleave dragend drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        photoDropzone.removeClass('border-warning bg-white');
    });
    photoDropzone.on('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const files = e.originalEvent.dataTransfer.files;
        if (files && files.length > 0) {
            photoInput[0].files = files;
            photoInput.trigger('change');
        }
    });
    // Reset preview if form is reset
    $('#astrologer-registration-form').on('reset', function() {
        setTimeout(function() {
            photoPreview.hide();
            dropzoneText.show();
        }, 100);
    });
    // ── Aadhar Drag & Drop ─────────────────────────────────
    function setupDropzone(dropzoneId, inputId, previewId, textId, label) {
        const dropzone = $(dropzoneId);
        const input = $(inputId);
        const preview = $(previewId);
        const text = $(textId);

        function showPreview(file) {
            if (!file) return;
            const ext = file.name.split('.').pop().toLowerCase();
            if (['jpg','jpeg','png','gif','bmp','webp'].includes(ext)) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.html(`<img src="${e.target.result}" alt="${label} Preview" class="img-thumbnail" style="max-width:80px;max-height:80px;">`);
                    preview.show();
                    text.hide();
                };
                reader.readAsDataURL(file);
            } else if (ext === 'pdf') {
                preview.html(`<i class="bi bi-file-earmark-pdf" style="font-size:2rem;color:#d32f2f;"></i><br><span class="small">${file.name}</span>`);
                preview.show();
                text.hide();
            } else {
                preview.hide();
                text.show();
            }
        }

        dropzone.on('click', function(e) {
            if (e.target.id === dropzone.attr('id') || $(e.target).hasClass('text-decoration-underline')) {
                input.trigger('click');
            }
        });

        input.on('change', function(e) {
            const file = this.files && this.files[0];
            if (file) {
                showPreview(file);
            } else {
                preview.hide();
                text.show();
            }
        });

        dropzone.on('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.addClass('border-warning bg-white');
        });
        dropzone.on('dragleave dragend drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.removeClass('border-warning bg-white');
        });
        dropzone.on('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const files = e.originalEvent.dataTransfer.files;
            if (files && files.length > 0) {
                input[0].files = files;
                input.trigger('change');
            }
        });
        // Reset preview if form is reset
        $('#astrologer-registration-form').on('reset', function() {
            setTimeout(function() {
                preview.hide();
                text.show();
            }, 100);
        });
    }

    setupDropzone('#aadhar-dropzone', '#astrologer_aadhar_document', '#aadhar-preview', '#aadhar-dropzone-text', 'Aadhar');
    setupDropzone('#pan-dropzone', '#astrologer_pan_document', '#pan-preview', '#pan-dropzone-text', 'PAN');

    // ── State/City dynamic select ───────────────────────────
    function loadStates() {
        $('#astrologer_state').html('<option value="">Loading...</option>');
        $.ajax({
            url: '/api/v1/get-state-list',
            type: 'POST',
            data: { country_id: 101 },
            success: function (res) {
                let options = '<option value="">Select State</option>';
                if (res && res.data && Array.isArray(res.data)) {
                    res.data.forEach(function (state) {
                        options += `<option value="${state.id}">${state.name}</option>`;
                    });
                }
                $('#astrologer_state').html(options);
            },
            error: function () {
                $('#astrologer_state').html('<option value="">Error loading states</option>');
            }
        });
    }

    function loadCities(stateId) {
        $('#astrologer_city').html('<option value="">Loading...</option>');
        if (!stateId) {
            $('#astrologer_city').html('<option value="">Select City</option>');
            return;
        }
        $.ajax({
            url: '/api/v1/get-city-list',
            type: 'POST',
            data: { state_id: stateId },
            success: function (res) {
                let options = '<option value="">Select City</option>';
                if (res && res.data && Array.isArray(res.data)) {
                    res.data.forEach(function (city) {
                        options += `<option value="${city.id}">${city.name}</option>`;
                    });
                }
                $('#astrologer_city').html(options);
            },
            error: function () {
                $('#astrologer_city').html('<option value="">Error loading cities</option>');
            }
        });
    }

    // Initial load
    loadStates();
    // When state changes, load cities
    $(document).on('change', '#astrologer_state', function () {
        loadCities($(this).val());
    });
    // If form is reset, reload states/cities
    $('#astrologer-registration-form').on('reset', function () {
        setTimeout(function () { loadStates(); $('#astrologer_city').html('<option value="">Select City</option>'); }, 100);
    });

    // Live word count for Details Bio (min 150, max 200 words)
    function updateBioWordCount() {
        const text = $('[name="astrologer_details_bio"]').val() || '';
        const words = text.trim().split(/\s+/).filter(Boolean);
        const count = words.length;
        const minWords = 150;
        const maxWords = 200;
        let msg = '';
        let cls = '';
        if (count < minWords) {
            msg = 'Words left: ' + (minWords - count);
            cls = 'text-danger';
        } else if (count > maxWords) {
            msg = 'Word limit exceeded by ' + (count - maxWords);
            cls = 'text-danger';
        } else {
            msg = 'Within limit (' + count + ' words)';
            cls = 'text-success';
        }
        $('#bio-word-count').text(msg).removeClass('text-danger text-success').addClass(cls);
    }
    $(document).on('input', '[name="astrologer_details_bio"]', updateBioWordCount);
    updateBioWordCount();

    const TOTAL_STEPS = 8;
    let currentStep = 1;
    let eduIndex = 0;
    let availIndex = 0;

    // ── Step navigation ───────────────────────────────────────
    function goToStep(n) {
        $('.form-section').addClass('d-none');
        $(`.form-section[data-section="${n}"]`).removeClass('d-none');
        currentStep = n;

        // Update dot indicators
        $('#step-indicator .step').each(function (i) {
            $(this).toggleClass('active', i < n);
        });

        $('#step-counter').text('Step ' + n + ' of ' + TOTAL_STEPS);
        $('#btn-prev').toggleClass('invisible', n === 1);

        if (n === TOTAL_STEPS) {
            $('#next-text').html('<i class="bi bi-check-circle me-1"></i> Submit Registration');
        } else {
            $('#next-text').html('Next <i class="bi bi-arrow-right ms-1"></i>');
        }

        $('html,body').animate({ scrollTop: $('.astrologerReg').offset().top }, 250);
        $('#form-message').html('');
    }

    $('#btn-next').on('click', function () {
        if (!validateStep(currentStep)) return;
        if (currentStep === TOTAL_STEPS) {
            submitForm();
        } else {
            goToStep(currentStep + 1);
        }
    });

    $('#btn-prev').on('click', function () {
        if (currentStep > 1) goToStep(currentStep - 1);
    });

    // ── Validation ────────────────────────────────────────────
function validateStep(step) {
    let valid = true;

    const documentMaxSize = 1024 * 1024;
    const documentMaxSizeMessage = 'File size must not be greater than 1MB.';

    function setDocumentError(input, message) {
        const field = $(input);
        field.addClass('is-invalid');

        let feedback = field.siblings('.invalid-feedback');
        if (!feedback.length) {
            feedback = field.parent().siblings('.invalid-feedback').first();
        }
        if (!feedback.length) {
            feedback = field.closest('.edu-dropzone').parent().find('.invalid-feedback').first();
        }
        if (!feedback.length) {
            field.parent().find('.invalid-feedback').remove();
                field.after('<div class="invalid-feedback d-block"></div>');
            feedback = field.siblings('.invalid-feedback');
        }

            feedback.addClass('d-block');
        feedback.text(message);
    }

    function clearDocumentError(input) {
        const field = $(input);
        field.removeClass('is-invalid');

        let feedback = field.siblings('.invalid-feedback');
        if (!feedback.length) {
            feedback = field.parent().siblings('.invalid-feedback').first();
        }
        if (!feedback.length) {
            feedback = field.closest('.edu-dropzone').parent().find('.invalid-feedback').first();
        }

        feedback.removeClass('d-block');
        feedback.text('');
    }

    function validateRequiredDocument(input, requiredMessage) {
        const field = $(input);
        const file = input && input.files && input.files[0] ? input.files[0] : null;

        if (!file) {
            setDocumentError(field, requiredMessage);
            return false;
        }

        if (file.size > documentMaxSize) {
            setDocumentError(field, documentMaxSizeMessage);
            return false;
        }

        clearDocumentError(field);
        return true;
    }

    // Clear all previous errors in this step
    $(`.form-section[data-section="${step}"] .form-control`).removeClass('is-invalid');
    $(`.form-section[data-section="${step}"] .form-select`).removeClass('is-invalid');
    $(`.form-section[data-section="${step}"] .invalid-feedback`).text('');
    $('#form-message').html('');

    // ── Step 1: Basic Information ─────────────────────────────
 if (step === 1) {

    let valid = true;

    // RESET
    const section = $(`.form-section[data-section="1"]`);
    section.find('.form-control').removeClass('is-invalid');
    section.find('.invalid-feedback').text('');
    $('#form-message').html('');

    const fields = [
        { name: 'astrologer_first_name',   msg: 'First name is required.' },
        { name: 'astrologer_last_name',    msg: 'Last name is required.' },
        { name: 'astrologer_email',        msg: 'A valid email is required.' },
        { name: 'astrologer_mobile_no',    msg: 'A 10-digit mobile number is required.' },
        { name: 'astrologer_password',     msg: 'Password must be at least 8 characters.' },
        { name: 'astrologer_display_name', msg: 'Display name is required.' },
    ];

    fields.forEach(function (f) {

        const el = $(`[name="${f.name}"]`);

        if (!el.length) return; // SAFETY

        const val = (el.val() || '').trim();
        let err = '';

        if (!val) {
            err = f.msg;
        }
        else if (f.name === 'astrologer_email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
            err = 'Please enter a valid email address.';
        }
        else if (f.name === 'astrologer_mobile_no' && !/^\d{10}$/.test(val)) {
            err = 'Enter a valid 10-digit mobile number.';
        }
        else if (f.name === 'astrologer_password' && val.length < 8) {
            err = 'Password must be at least 8 characters.';
        }

        if (err) {
            el.addClass('is-invalid');

            // SAFE TARGETING
           el.siblings('.invalid-feedback').text(err);

            valid = false;
        }

    });

    if (!valid) {
        $('#form-message').html('<div class="alert alert-danger">Please fix the errors above before continuing.</div>');
        return false;
    }

    return true;
}
    // ── Step 2: Profile Details ───────────────────────────────
    if (step === 2) {
        const bio = ($('[name="astrologer_details_bio"]').val() || '').trim();
        const words = bio.split(/\s+/).filter(Boolean);
        const count = words.length;
        if (!bio || count < 150 || count > 200) {
            $('[name="astrologer_details_bio"]').addClass('is-invalid');
            let msg = '';
            if (count < 150) {
                msg = 'Details bio must be at least 150 words.';
            } else if (count > 200) {
                msg = 'Details bio must not exceed 200 words.';
            } else {
                msg = 'Details bio is required.';
            }
            $('[name="astrologer_details_bio"]').parent().find('.invalid-feedback').text(msg);
            $('#form-message').html('<div class="alert alert-danger">Please fill in the required profile details.</div>');
            valid = false;
        }

        // Address validation
        const address = $('[name="astrologer_address"]');
        if (!address.val() || address.val().trim().length < 5) {
            address.addClass('is-invalid');
            if (address.siblings('.invalid-feedback').length === 0) {
                address.after('<div class="invalid-feedback">Address is required (min 5 chars).</div>');
            } else {
                address.siblings('.invalid-feedback').text('Address is required (min 5 chars).');
            }
            valid = false;
        } else {
            address.removeClass('is-invalid');
            address.siblings('.invalid-feedback').text('');
        }

        // State validation
        const state = $('[name="astrologer_state"]');
        if (!state.val()) {
            state.addClass('is-invalid');
            if (state.siblings('.invalid-feedback').length === 0) {
                state.after('<div class="invalid-feedback">State is required.</div>');
            } else {
                state.siblings('.invalid-feedback').text('State is required.');
            }
            valid = false;
        } else {
            state.removeClass('is-invalid');
            state.siblings('.invalid-feedback').text('');
        }

        // City validation
        const city = $('[name="astrologer_city"]');
        if (!city.val()) {
            city.addClass('is-invalid');
            if (city.siblings('.invalid-feedback').length === 0) {
                city.after('<div class="invalid-feedback">City is required.</div>');
            } else {
                city.siblings('.invalid-feedback').text('City is required.');
            }
            valid = false;
        } else {
            city.removeClass('is-invalid');
            city.siblings('.invalid-feedback').text('');
        }

        // Pin Code validation (required, must be 6 digits)
        const pin = $('[name="astrologer_pin"]');
        const pinVal = pin.val() ? pin.val().trim() : '';
        if (!/^\d{6}$/.test(pinVal)) {
            pin.addClass('is-invalid');
            if (pin.siblings('.invalid-feedback').length === 0) {
                pin.after('<div class="invalid-feedback">Pin Code must be 6 digits.</div>');
            } else {
                pin.siblings('.invalid-feedback').text('Pin Code must be 6 digits.');
            }
            valid = false;
        } else {
            pin.removeClass('is-invalid');
            pin.siblings('.invalid-feedback').text('');
        }
    }

    // ── Step 3: Education ─────────────────────────────────────
    if (step === 3) {
        let eduValid = true;

        $('#education-section .education-entry').each(function (i) {
            const entry = $(this);

            const degree = entry.find(`[name^="astrologer_education"][name$="[degree]"]`);
            const institution = entry.find(`[name^="astrologer_education"][name$="[institution]"]`);
            const year = entry.find(`[name^="astrologer_education"][name$="[year]"]`);
            const document = entry.find(`[name^="astrologer_education"][name$="[document]"]`);

            if (!degree.val().trim()) {
                degree.addClass('is-invalid');
                degree.parent().find('.invalid-feedback').remove();
                degree.after('<div class="invalid-feedback d-block">Degree is required.</div>');
                eduValid = false;
            }
            if (!institution.val().trim()) {
                institution.addClass('is-invalid');
                institution.parent().find('.invalid-feedback').remove();
                institution.after('<div class="invalid-feedback d-block">Institution is required.</div>');
                eduValid = false;
            }
            if (!year.val()) {
                year.addClass('is-invalid');
                year.parent().find('.invalid-feedback').remove();
                year.after('<div class="invalid-feedback d-block">Year is required.</div>');
                eduValid = false;
            }
            if (!document[0].files || document[0].files.length === 0) {
                setDocumentError(document, 'Document is required.');
                eduValid = false;
            } else if (document[0].files[0].size > documentMaxSize) {
                setDocumentError(document, documentMaxSizeMessage);
                eduValid = false;
            } else {
                clearDocumentError(document);
            }
        });

        if (!eduValid) {
            valid = false;
            $('#form-message').html('<div class="alert alert-danger">Please fill in all required education fields.</div>');
        }
    }

    // ── Step 4: Professional Details ─────────────────────────
    if (step === 4) {
        // Experience validation
        const exp = $('[name="astrologer_experience"]');
        if (!exp.val() || isNaN(exp.val()) || Number(exp.val()) < 0) {
            exp.addClass('is-invalid');
            exp.siblings('.invalid-feedback').text('Experience is required and must be 0 or more.');
            valid = false;
        } else {
            exp.removeClass('is-invalid');
            exp.siblings('.invalid-feedback').text('');
        }

        // Rate validation
        const rate = $('[name="astrologer_rate"]');
        if (!rate.val() || isNaN(rate.val()) || Number(rate.val()) < 0) {
            rate.addClass('is-invalid');
            rate.siblings('.invalid-feedback').text('Rate is required and must be 0 or more.');
            valid = false;
        } else {
            rate.removeClass('is-invalid');
            rate.siblings('.invalid-feedback').text('');
        }

        // Languages validation (existing)
        if (!$('#astrologer_languages-hidden').val()) {
            $('#languages-feedback').text('Please select at least one language.');
            $('#form-message').html('<div class="alert alert-danger">Please select at least one language.</div>');
            valid = false;
        } else {
            $('#languages-feedback').text('');
        }

        // Skills validation
        if (!$('#astrologer_skills-hidden').val()) {
            $('#skills-feedback').text('Please select at least one skill.');
            $('#form-message').html('<div class="alert alert-danger">Please select at least one skill.</div>');
            valid = false;
        } else {
            $('#skills-feedback').text('');
        }
    }

    // ── Step 5: Availability ──────────────────────────────────
    if (step === 5) {
        let availValid = true;

        $('#availability-section .availability-entry').each(function () {
            const entry = $(this);

            // Day is a select — always has a value, but validate it's not empty just in case
            const day = entry.find(`[name^="astrologer_availabilities"][name$="[day]"]`);
            if (!day.val()) {
                day.addClass('is-invalid');
                availValid = false;
            }

            // Validate each slot row's from/to times
            entry.find('.slot-row').each(function () {
                const fromInput = $(this).find(`input[name^="astrologer_availabilities"][name$="[from]"]`);
                const toInput   = $(this).find(`input[name^="astrologer_availabilities"][name$="[to]"]`);

                if (!fromInput.val()) {
                    fromInput.addClass('is-invalid');
                    fromInput.parent().find('.invalid-feedback').remove();
                    fromInput.after('<div class="invalid-feedback d-block">Start time is required.</div>');
                    availValid = false;
                }
                if (!toInput.val()) {
                    toInput.addClass('is-invalid');
                    toInput.parent().find('.invalid-feedback').remove();
                    toInput.after('<div class="invalid-feedback d-block">End time is required.</div>');
                    availValid = false;
                }
                // Ensure "to" is after "from"
                if (fromInput.val() && toInput.val() && toInput.val() <= fromInput.val()) {
                    toInput.addClass('is-invalid');
                    toInput.parent().find('.invalid-feedback').remove();
                    toInput.after('<div class="invalid-feedback d-block">End time must be after start time.</div>');
                    availValid = false;
                }
            });
        });

        if (!availValid) {
            valid = false;
            $('#form-message').html('<div class="alert alert-danger">Please fill in all required availability fields.</div>');
        }
    }


    // ── Step 6: KYC Details ───────────────────────────────
    if (step === 6) {
        let kycValid = true;
        const photo = $('[name="astrologer_photo"]');
        const aadharNumber = $('[name="astrologer_aadhar_number"]');
        const panNumber = $('[name="astrologer_pan_number"]');
        const aadharDoc = $('[name="astrologer_aadhar_document"]');
        const panDoc = $('[name="astrologer_pan_document"]');

        // Photo
        if (!photo[0].files || photo[0].files.length === 0) {
            photo.addClass('is-invalid');
            photo.closest('.col-md-4').find('.invalid-feedback').last().addClass('d-block').text('Photo is required.');
            kycValid = false;
        } else if (photo[0].files[0].size > photoMaxSize) {
            photo.addClass('is-invalid');
            photo.closest('.col-md-4').find('.invalid-feedback').last().addClass('d-block').text('Photo must not be greater than 1MB.');
            kycValid = false;
        } else {
            photo.removeClass('is-invalid');
            photo.closest('.col-md-4').find('.invalid-feedback').last().removeClass('d-block').text('');
        }
        // Aadhar Number
        if (!aadharNumber.val() || !/^\d{12}$/.test(aadharNumber.val().trim())) {
            aadharNumber.addClass('is-invalid');
            aadharNumber.parent().find('.invalid-feedback').text('Valid 12-digit Aadhar number required.');
            kycValid = false;
        } else {
            aadharNumber.removeClass('is-invalid');
            aadharNumber.parent().find('.invalid-feedback').text('');
        }
        // PAN Number
        if (!panNumber.val() || !/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/.test(panNumber.val().trim().toUpperCase())) {
            panNumber.addClass('is-invalid');
            panNumber.parent().find('.invalid-feedback').text('Valid PAN number required (e.g., ABCDE1234F).');
            kycValid = false;
        } else {
            panNumber.removeClass('is-invalid');
            panNumber.parent().find('.invalid-feedback').text('');
        }
        // Aadhar Document
        if (!aadharDoc[0].files || aadharDoc[0].files.length === 0) {
            setDocumentError(aadharDoc, 'Aadhar document is required.');
            kycValid = false;
        } else if (aadharDoc[0].files[0].size > documentMaxSize) {
            setDocumentError(aadharDoc, 'Aadhar document must not be greater than 1MB.');
            kycValid = false;
        } else {
            clearDocumentError(aadharDoc);
        }
        // PAN Document
        if (!panDoc[0].files || panDoc[0].files.length === 0) {
            setDocumentError(panDoc, 'PAN document is required.');
            kycValid = false;
        } else if (panDoc[0].files[0].size > documentMaxSize) {
            setDocumentError(panDoc, 'PAN document must not be greater than 1MB.');
            kycValid = false;
        } else {
            clearDocumentError(panDoc);
        }

        if (!kycValid) {
            valid = false;
            $('#form-message').html('<div class="alert alert-danger">Please fill in all required KYC details.</div>');
        }
    }

    // ── Step 7: Banking Details ───────────────────────────────
    if (step === 7) {
        const bankFields = [
            { name: 'astrologer_bank_holder_name',    msg: 'Account holder name is required.' },
            { name: 'astrologer_bank_name',           msg: 'Bank name is required.' },
            { name: 'astrologer_bank_account_number', msg: 'Account number is required.' },
            { name: 'astrologer_ifsc_code',           msg: 'IFSC code is required.' },
            { name: 'astrologer_branch_name',         msg: 'Branch name is required.' },
            { name: 'astrologer_upi_id',              msg: 'UPI ID is required.' },
        ];

        bankFields.forEach(function (f) {
            const el = $(`[name="${f.name}"]`);
            const val = (el.val() || '').trim();

            if (!val) {
                el.addClass('is-invalid');
                el.parent().find('.invalid-feedback').remove();
                el.after(`<div class="invalid-feedback d-block">${f.msg}</div>`);
                valid = false;
            }
        });

        if (!valid) {
            $('#form-message').html('<div class="alert alert-danger">Please fill in all required banking details.</div>');
        }
    }


    // ── Step 8: Agreement ─────────────────────────────────────
    if (step === 8) {
        const unchecked = $(`.form-section[data-section="8"] input[type="checkbox"]:not(:checked)`).length;
        if (unchecked > 0) {
            $('#form-message').html('<div class="alert alert-danger">Please accept all agreement terms to continue.</div>');
            valid = false;
        }

        // Applicant Name validation
        const applicantName = $('[name="astrologer_declaration_applicant_name"]');
        if (!applicantName.val() || applicantName.val().trim().length < 2) {
            applicantName.addClass('is-invalid');
            if (applicantName.siblings('.invalid-feedback').length === 0) {
                applicantName.after('<div class="invalid-feedback">Applicant name is required.</div>');
            } else {
                applicantName.siblings('.invalid-feedback').text('Applicant name is required.');
            }
            valid = false;
        } else {
            applicantName.removeClass('is-invalid');
            applicantName.siblings('.invalid-feedback').text('');
        }

        // Signature validation (must be drawn on the digital pad)
        const signaturePad = $('#signature-pad');
        const signatureFeedback = $('#astrologer_signature_image').siblings('.invalid-feedback').first();
        const signatureImage = $('#astrologer_signature_image').val();
        if (!signatureImage) {
            signaturePad.addClass('border border-danger');
            signatureFeedback.text('Digital signature is required.');
            valid = false;
        } else {
            signaturePad.removeClass('border-danger');
            signatureFeedback.text('');
        }

        // Date validation (must be today, not past/future)
        const dateInput = $('[name="astrologer_declaration_date"]');
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        const todayStr = `${yyyy}-${mm}-${dd}`;
        if (!dateInput.val() || dateInput.val() !== todayStr) {
            dateInput.addClass('is-invalid');
            if (dateInput.siblings('.invalid-feedback').length === 0) {
                dateInput.after('<div class="invalid-feedback">Date must be today (' + todayStr + ').</div>');
            } else {
                dateInput.siblings('.invalid-feedback').text('Date must be today (' + todayStr + ').');
            }
            valid = false;
        } else {
            dateInput.removeClass('is-invalid');
            dateInput.siblings('.invalid-feedback').text('');
        }
    }

    return valid;
}
    // ── Education repeater ────────────────────────────────────
    function addEducationEntry(data) {
        data = data || {};
        const html = `<div class="row g-2 align-items-end education-entry mb-2 border rounded p-2 bg-light">
            <div class="col-md-3"><input type="text" name="astrologer_education[${eduIndex}][degree]" placeholder="Degree" class="form-control" value="${data.degree || ''}"></div>
            <div class="col-md-3"><input type="text" name="astrologer_education[${eduIndex}][institution]" placeholder="Institution" class="form-control" value="${data.institution || ''}"></div>
            <div class="col-md-2"><input type="number" name="astrologer_education[${eduIndex}][year]" placeholder="Year" class="form-control" value="${data.year || ''}"></div>
            <div class="col-md-3">
                <div class="edu-dropzone border border-2 border-dashed rounded-3 p-2 text-center bg-light position-relative" style="cursor:pointer; min-height: 60px; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                    <div class="edu-preview mb-1" style="display:none;"></div>
                    <div class="edu-dropzone-text">
                        <i class="bi bi-cloud-arrow-up" style="font-size:1.3rem;color:#f57c00;"></i><br>
                        <span class="text-muted" style="font-size:12px;">Drag & drop file<br>or <span class="text-primary text-decoration-underline" style="cursor:pointer;">browse</span></span>
                    </div>
                    <input type="file" name="astrologer_education[${eduIndex}][document]" class="form-control position-absolute top-0 start-0 w-100 h-100 opacity-0 edu-file-input" accept=".pdf,image/*" required style="z-index:2;cursor:pointer;">
                </div>
                <div class="form-text text-muted small">Accepted: PDF, JPG, PNG. Max 1MB.</div>
            </div>
            <div class="col-md-1 text-end"><button type="button" class="btn btn-danger btn-sm remove-education"><i class="bi bi-x"></i></button></div>
        </div>`;
        $('#education-section').append(html);
        eduIndex++;
    }
    function ensureAtLeastOneEducation() {
        if ($('#education-section .education-entry').length === 0) addEducationEntry();
    }
    addEducationEntry();
    $('#add-education').click(function () { addEducationEntry(); });
    $(document).on('click', '.remove-education', function () {
        $(this).closest('.education-entry').remove();
        setTimeout(ensureAtLeastOneEducation, 10);
    });

    // ── Education Document Drag & Drop ─────────────────────
    function setupEduDropzone(entry) {
        const dropzone = entry.find('.edu-dropzone');
        const input = entry.find('.edu-file-input');
        const preview = entry.find('.edu-preview');
        const text = entry.find('.edu-dropzone-text');
        const maxDocumentSize = 1024 * 1024;

        function applyDocumentValidation(fileInput, requiredMessage, sizeMessage) {
            const field = $(fileInput);
            const file = fileInput && fileInput.files && fileInput.files[0] ? fileInput.files[0] : null;
            let feedback = field.closest('.edu-dropzone').parent().find('.invalid-feedback').first();

            if (!feedback.length) {
                field.after('<div class="invalid-feedback d-block"></div>');
                feedback = field.siblings('.invalid-feedback');
            }

            if (!file) {
                field.addClass('is-invalid');
                feedback.text(requiredMessage);
                return false;
            }

            if (file.size > maxDocumentSize) {
                field.addClass('is-invalid');
                feedback.text(sizeMessage);
                return false;
            }

            field.removeClass('is-invalid');
            feedback.text('');
            return true;
        }

        function showPreview(file) {
            if (!file) return;
            const ext = file.name.split('.').pop().toLowerCase();
            if (["jpg","jpeg","png","gif","bmp","webp"].includes(ext)) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.html(`<img src="${e.target.result}" alt="Doc Preview" class="img-thumbnail" style="max-width:40px;max-height:40px;">`);
                    preview.show();
                    text.hide();
                };
                reader.readAsDataURL(file);
            } else if (ext === 'pdf') {
                preview.html(`<i class="bi bi-file-earmark-pdf" style="font-size:1.3rem;color:#d32f2f;"></i><br><span class="small">${file.name}</span>`);
                preview.show();
                text.hide();
            } else {
                preview.hide();
                text.show();
            }
        }

        dropzone.on('click', function(e) {
            if (e.target === dropzone[0] || $(e.target).hasClass('text-decoration-underline')) {
                input.trigger('click');
            }
        });

        input.on('change', function(e) {
            const file = this.files && this.files[0];
            if (file) {
                showPreview(file);
            } else {
                preview.hide();
                text.show();
            }

            applyDocumentValidation(this, 'Document is required.', 'Document must not be greater than 1MB.');
        });

        dropzone.on('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.addClass('border-warning bg-white');
        });
        dropzone.on('dragleave dragend drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.removeClass('border-warning bg-white');
        });
        dropzone.on('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const files = e.originalEvent.dataTransfer.files;
            if (files && files.length > 0) {
                input[0].files = files;
                input.trigger('change');
            }
        });
        // Reset preview if form is reset
        $('#astrologer-registration-form').on('reset', function() {
            setTimeout(function() {
                preview.hide();
                text.show();
            }, 100);
        });
    }

    $('#astrologer_aadhar_document').on('change', function () {
        const file = this.files && this.files[0] ? this.files[0] : null;
        const feedback = $(this).closest('.col-md-4').find('.invalid-feedback').last();

        if (!file) {
            $(this).addClass('is-invalid');
            feedback.addClass('d-block');
            feedback.text('Aadhar document is required.');
            return;
        }

        if (file.size > 1024 * 1024) {
            $(this).addClass('is-invalid');
            feedback.addClass('d-block');
            feedback.text('Aadhar document must not be greater than 1MB.');
            return;
        }

        $(this).removeClass('is-invalid');
        feedback.removeClass('d-block');
        feedback.text('');
    });

    $('#astrologer_pan_document').on('change', function () {
        const file = this.files && this.files[0] ? this.files[0] : null;
        const feedback = $(this).closest('.col-md-4').find('.invalid-feedback').last();

        if (!file) {
            $(this).addClass('is-invalid');
            feedback.addClass('d-block');
            feedback.text('PAN document is required.');
            return;
        }

        if (file.size > 1024 * 1024) {
            $(this).addClass('is-invalid');
            feedback.addClass('d-block');
            feedback.text('PAN document must not be greater than 1MB.');
            return;
        }

        $(this).removeClass('is-invalid');
        feedback.removeClass('d-block');
        feedback.text('');
    });

    // Setup dropzone for all current and future education entries
    function setupAllEduDropzones() {
        $('#education-section .education-entry').each(function() {
            setupEduDropzone($(this));
        });
    }
    setupAllEduDropzones();
    // When new education entry is added
    $('#add-education').on('click', function() {
        setTimeout(setupAllEduDropzones, 50);
    });

    // ── Availability repeater ─────────────────────────────────
    function addAvailabilityEntry() {
        const html = `<div class="availability-entry border rounded p-2 mb-2 bg-light">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <select name="astrologer_availabilities[${availIndex}][day]" class="form-select">
                        <option>Monday</option><option>Tuesday</option><option>Wednesday</option>
                        <option>Thursday</option><option>Friday</option><option>Saturday</option><option>Sunday</option>
                    </select>
                </div>
                <div class="col-md-8 slots-section">
                    <div class="row g-2 slot-row">
                        <div class="col"><input type="time" name="astrologer_availabilities[${availIndex}][slots][0][from]" class="form-control"></div>
                        <div class="col"><input type="time" name="astrologer_availabilities[${availIndex}][slots][0][to]" class="form-control"></div>
                        <div class="col-auto"><button type="button" class="btn btn-outline-secondary btn-sm add-slot" data-avail="${availIndex}"><i class="bi bi-plus"></i></button></div>
                    </div>
                </div>
                <div class="col-md-1 text-end"><button type="button" class="btn btn-danger btn-sm remove-availability"><i class="bi bi-x"></i></button></div>
            </div>
        </div>`;
        $('#availability-section').append(html);
        availIndex++;
    }
    function ensureAtLeastOneAvailability() {
        if ($('#availability-section .availability-entry').length === 0) addAvailabilityEntry();
    }
    addAvailabilityEntry();
    $('#add-availability').click(function () { addAvailabilityEntry(); });
    $(document).on('click', '.remove-availability', function () {
        $(this).closest('.availability-entry').remove();
        setTimeout(ensureAtLeastOneAvailability, 10);
    });
    $(document).on('click', '.add-slot', function () {
        const ai = $(this).data('avail');
        const slotsSection = $(this).closest('.slots-section');
        const slotCount = slotsSection.find('.slot-row').length;
        slotsSection.append(`<div class="row g-2 slot-row mt-1">
            <div class="col"><input type="time" name="astrologer_availabilities[${ai}][slots][${slotCount}][from]" class="form-select"></div>
            <div class="col"><input type="time" name="astrologer_availabilities[${ai}][slots][${slotCount}][to]" class="form-select"></div>
            <div class="col-auto"><button type="button" class="btn btn-outline-danger btn-sm remove-slot"><i class="bi bi-x"></i></button></div>
        </div>`);
    });
    $(document).on('click', '.remove-slot', function () { $(this).closest('.slot-row').remove(); });

    // ── Languages & Skills badges ─────────────────────────────
    function renderBadgeOptions(container, hiddenInput, data) {
        let selected = [];
        function updateHidden() { $(hiddenInput).val(selected.join(',')); }
        $(container).empty();
        (data || []).forEach(function (item) {
            const badge = $(`<span class="badge bg-secondary selectable-badge" data-id="${item.id}" style="cursor:pointer;user-select:none;border-radius:0px">${item.name}</span>`);
            badge.on('click', function () {
                const id = String(item.id);
                if (selected.includes(id)) {
                    selected = selected.filter(v => v !== id);
                    badge.removeClass('bg-primary').addClass('bg-secondary');
                } else {
                    selected.push(id);
                    badge.removeClass('bg-secondary').addClass('bg-primary');
                }
                updateHidden();
            });
            $(container).append(badge);
        });
        updateHidden();
    }
    axios.get('/api/v1/astrologer-languages')
        .then(r => renderBadgeOptions('#languages-badges', '#astrologer_languages-hidden', r.data))
        .catch(() => $('#languages-badges').html('<span class="text-danger">Error loading languages</span>'));
    axios.get('/api/v1/astrologer-skills')
        .then(r => renderBadgeOptions('#skills-badges', '#astrologer_skills-hidden', r.data))
        .catch(() => $('#skills-badges').html('<span class="text-danger">Error loading skills</span>'));

    // ── Improved Signature pad ───────────────────────────────
    const canvas = document.getElementById('signature-pad');
    const ctx = canvas.getContext('2d');
    let drawing = false;
    let lastPoint = { x: 0, y: 0 };
    ctx.strokeStyle = '#333';
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';

    function getCanvasPos(e) {
        const rect = canvas.getBoundingClientRect();
        if (e.touches && e.touches.length > 0) {
            return {
                x: (e.touches[0].clientX - rect.left) * (canvas.width / rect.width),
                y: (e.touches[0].clientY - rect.top) * (canvas.height / rect.height)
            };
        } else {
            return {
                x: (e.clientX - rect.left) * (canvas.width / rect.width),
                y: (e.clientY - rect.top) * (canvas.height / rect.height)
            };
        }
    }

    function startDraw(e) {
        e.preventDefault();
        drawing = true;
        lastPoint = getCanvasPos(e);
        ctx.beginPath();
        ctx.moveTo(lastPoint.x, lastPoint.y);
    }

    function draw(e) {
        if (!drawing) return;
        e.preventDefault();
        const pos = getCanvasPos(e);
        ctx.lineTo(pos.x, pos.y);
        ctx.stroke();
        lastPoint = pos;
    }

    function endDraw(e) {
        if (!drawing) return;
        drawing = false;
        $('#astrologer_signature_image').val(canvas.toDataURL());
        $('#signature-pad').removeClass('border-danger');
        $('#astrologer_signature_image').siblings('.invalid-feedback').first().text('');
    }

    // Mouse events
    canvas.addEventListener('mousedown', startDraw);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', endDraw);
    canvas.addEventListener('mouseleave', endDraw);

    // Touch events
    canvas.addEventListener('touchstart', startDraw, { passive: false });
    canvas.addEventListener('touchmove', draw, { passive: false });
    canvas.addEventListener('touchend', endDraw, { passive: false });
    canvas.addEventListener('touchcancel', endDraw, { passive: false });

    // Prevent scrolling when drawing on touch
    canvas.addEventListener('touchmove', function(e) { if (drawing) e.preventDefault(); }, { passive: false });

    $('#clear-signature').on('click', function () {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        $('#astrologer_signature_image').val('');
        $('#signature-pad').removeClass('border-danger');
        $('#astrologer_signature_image').siblings('.invalid-feedback').first().text('');
    });

function submitForm() {
    $('.form-control, .form-select').removeClass('is-invalid');
    $('.invalid-feedback').text('');
    $('#form-message').html('');

    $('#btn-next').attr('disabled', true);
    $('#submit-spinner').removeClass('d-none');
    $('#next-text').text('Registering...');

    const form = document.getElementById('astrologer-registration-form');

    // ✅ IMPORTANT: This includes FILES automatically
    const formData = new FormData(form);

    // ✅ FIELD MAPPING (overwrite correct API keys)
    const fieldMap = {
        'astrologer_first_name': 'first_name',
        'astrologer_last_name': 'last_name',
        'astrologer_display_name': 'display_name',
        'astrologer_short_intro': 'short_intro',
        'astrologer_details_bio': 'details_bio',
        'astrologer_address': 'address',
        'astrologer_state': 'state_id',
        'astrologer_city': 'city_id',
        'astrologer_pin': 'pin_code',
        'astrologer_consultation_mode': 'consultation_mode',
        'astrologer_bank_holder_name': 'ac_holder_name',
        'astrologer_bank_name': 'bank_name',
        'astrologer_bank_account_number': 'ac_number',
        'astrologer_ifsc_code': 'ifsc_code',
        'astrologer_branch_name': 'branch_name',
        'astrologer_upi_id': 'upi_id',
        'astrologer_declaration_applicant_name': 'applicant_name',
        'astrologer_email': 'email',
        'astrologer_mobile_no': 'mobile_no',
        'astrologer_password': 'password',
        'astrologer_experience': 'experience',
        'astrologer_rate': 'rate',
        'astrologer_duration': 'duration',
        'astrologer_aadhar_number': 'aadhar_number',
        'astrologer_pan_number': 'pan_number',
    };

    // ✅ Replace values with API keys
    Object.keys(fieldMap).forEach(function (k) {
        const el = form.elements[k];
        if (el) {
            formData.set(fieldMap[k], el.value || '');
        }
    });

    // ✅ Languages
    const langs = ($('#astrologer_languages-hidden').val() || '').split(',').filter(Boolean);
    formData.delete('languages[]');
    langs.forEach(l => formData.append('languages[]', l));

    // ✅ Skills
    const skills = ($('#astrologer_skills-hidden').val() || '').split(',').filter(Boolean);
    formData.delete('skills[]');
    skills.forEach(s => formData.append('skills[]', s));

    // =========================
    // 4. EDUCATION (WITH FILE)
    // =========================
    $('#education-section .education-entry').each(function (i) {
        formData.append(`education[${i}][degree]`, $(this).find('[name$="[degree]"]').val() || '');
        formData.append(`education[${i}][institution]`, $(this).find('[name$="[institution]"]').val() || '');
        formData.append(`education[${i}][year]`, $(this).find('[name$="[year]"]').val() || '');
        const fileInput = $(this).find('[name$="[document]"]')[0];
        if (fileInput?.files?.length) {
            formData.append(`education[${i}][document]`, fileInput.files[0]);
        }
    });

    // =========================
    // 5. AVAILABILITY
    // =========================
    $('#availability-section .availability-entry').each(function (i) {
        const day = $(this).find('[name$="[day]"]').val();
        formData.append(`availabilities[${i}][day]`, day);
        $(this).find('.slot-row').each(function (j) {
            formData.append(`availabilities[${i}][slots][${j}][from]`, $(this).find('[name$="[from]"]').val() || '');
            formData.append(`availabilities[${i}][slots][${j}][to]`, $(this).find('[name$="[to]"]').val() || '');
        });
    });

    // ✅ DEBUG (optional)
    console.log("===== FORM DATA =====");
    for (let pair of formData.entries()) {
        console.log(pair[0], pair[1]);
    }

    // ✅ AXIOS CALL
    axios.post('/astrologer/register', formData, {
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    })
    .then(function (response) {
        $('#form-message').html('<div class="alert alert-success">Registration successful!</div>');
        $('#reg-success-modal').modal('show');

        form.reset();
        goToStep(1);
    })
    .catch(function (error) {
        $('#btn-next').attr('disabled', false);
        $('#submit-spinner').addClass('d-none');
        $('#next-text').html('Submit Registration');

        const humanizeMessage = function (value) {
            return String(value || '').replace(/1024 kilobytes/gi, '1MB').trim();
        };

        const responseData = error.response && error.response.data ? error.response.data : null;
        const errors = responseData && responseData.errors ? responseData.errors : null;
        let message = 'Something went wrong. Try again.';

        if (responseData && responseData.message) {
            message = String(responseData.message).trim();

            const jsonStart = message.indexOf('{');
            if (jsonStart !== -1) {
                try {
                    const parsed = JSON.parse(message.slice(jsonStart));
                    if (parsed && parsed.message) {
                        message = humanizeMessage(parsed.message);
                    } else if (parsed && Array.isArray(parsed.all_errors) && parsed.all_errors.length) {
                        message = humanizeMessage(parsed.all_errors[0]);
                    }
                } catch (parseError) {
                    const embeddedMessage = message.match(/"message"\s*:\s*"((?:\\.|[^"\\])+)"/);
                    if (embeddedMessage && embeddedMessage[1]) {
                        message = humanizeMessage(embeddedMessage[1]
                            .replace(/\\"/g, '"')
                            .replace(/\\\\/g, '\\'));
                    }
                }
            } else {
                const embeddedMessage = message.match(/"message"\s*:\s*"((?:\\.|[^"\\])+)"/);
                if (embeddedMessage && embeddedMessage[1]) {
                    message = humanizeMessage(embeddedMessage[1]
                        .replace(/\\"/g, '"')
                        .replace(/\\\\/g, '\\'));
                }
            }
        } else if (responseData && Array.isArray(responseData.all_errors) && responseData.all_errors.length) {
            message = humanizeMessage(responseData.all_errors[0]);
        }

        message = humanizeMessage(message);

        if (errors) {
            Object.keys(errors).forEach(function (key) {
                const el = $(`[name="${key}"]`);
                if (el.length) {
                    el.addClass('is-invalid');
                    el.parent().find('.invalid-feedback').text(humanizeMessage(errors[key][0]));
                }
            });

            $('#form-message').html(`<div class="alert alert-danger">${message}</div>`);
        } else {
            $('#form-message').html(`<div class="alert alert-danger">${message}</div>`);
        }
    })
    .finally(function () {
        $('#btn-next').attr('disabled', false);
        $('#submit-spinner').addClass('d-none');
        $('#next-text').html('<i class="bi bi-check-circle me-1"></i> Submit Registration');
    });
}

    // ── Init ──────────────────────────────────────────────────
    goToStep(1);
});
</script>
@endpush
