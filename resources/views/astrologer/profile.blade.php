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

                <div class="sidebar-card dashboard-card profile-shell mt-4">
                    <div class="profile-page-head">
                        <div class="profile-page-head-content">
                            <div class="profile-eyebrow">Profile Workspace</div>
                            <h3 class="profile-page-title">Profile Settings</h3>
                            <p class="profile-page-subtitle mb-0">Manage public profile information, credentials, availability, and verification documents in one place.</p>
                        </div>
                        <div class="profile-page-meta">
                                <button type="button" class="btn btn-outline-primary btn-sm" data-edit-profile-trigger>Edit Profile</button>
                            <span class="profile-mode-badge" id="profile-mode-badge">View Mode</span>
                        </div>
                    </div>

                    @if($loadError)
                        <div class="alert alert-warning">{{ $loadError }}</div>
                    @endif

                    <form id="astrologer-profile-form" class="profile-form" autocomplete="off" enctype="multipart/form-data">
                        @csrf

                        <div class="profile-settings-grid">
                            <div class="profile-settings-main">
                                <div class="profile-section">
                            <div class="profile-section-head">
                                <div>
                                    <h4 class="profile-section-title">Core Information</h4>
                                    <p class="profile-section-subtitle mb-0">These details appear across your astrologer profile and booking experience.</p>
                                </div>
                            </div>

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
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" value="" placeholder="Leave blank to keep the current password">
                                <div class="form-text">Only enter a password if you want to update it.</div>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Short Intro</label>
                                <textarea class="form-control" name="short_intro" rows="3">{{ $profile['short_intro'] }}</textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Detailed Bio</label>
                                <textarea class="form-control" name="details_bio" rows="5">{{ $profile['details_bio'] }}</textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" name="address" rows="3">{{ $profile['address'] }}</textarea>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">State</label>
                                <select class="form-select" name="state_id" id="profile-state-select" data-selected="{{ $profile['state_id'] }}">
                                    <option value="">Select State</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">City</label>
                                <select class="form-select" name="city_id" id="profile-city-select" data-selected="{{ $profile['city_id'] }}">
                                    <option value="">Select City</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Pin Code</label>
                                <input type="text" class="form-control" name="pin_code" maxlength="10" value="{{ $profile['pin_code'] }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Consultation Mode</label>
                                <select class="form-select" name="consultation_mode">
                                    <option value="">Select Mode</option>
                                    <option value="online" @selected($profile['consultation_mode'] === 'online')>Online</option>
                                    <option value="video" @selected($profile['consultation_mode'] === 'video')>Video</option>
                                    <option value="audio" @selected($profile['consultation_mode'] === 'audio')>Audio</option>
                                    <option value="chat" @selected($profile['consultation_mode'] === 'chat')>Chat</option>
                                </select>
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
                                </div>

                                <div class="profile-section mt-4">
                            <div class="profile-section-head">
                                <div>
                                    <h4 class="profile-section-title">Expertise</h4>
                                    <p class="profile-section-subtitle mb-0">Showcase the languages you support and the services you specialize in.</p>
                                </div>
                            </div>

                        <div class="row g-4 mt-1">
                            <div class="col-md-12">
                                <div class="profile-subcard h-100">
                                    <div class="profile-subcard-head">
                                        <h5 class="mb-0">Languages</h5>

                                    </div>

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
                            <div class="col-md-12">
                                <div class="profile-subcard h-100">
                                    <div class="profile-subcard-head">
                                        <h5 class="mb-0">Skills</h5>

                                    </div>
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
                        </div>

                                <div class="profile-section mt-4">
                            <div class="profile-section-head">
                                <div>
                                    <h4 class="profile-section-title">Banking Details</h4>
                                    <p class="profile-section-subtitle mb-0">Maintain payout and account information used for settlements and verification.</p>
                                </div>
                            </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Account Holder Name</label>
                                <input type="text" class="form-control" name="ac_holder_name" value="{{ $profile['ac_holder_name'] }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Bank Name</label>
                                <input type="text" class="form-control" name="bank_name" value="{{ $profile['bank_name'] }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Account Number</label>
                                <input type="text" class="form-control" name="ac_number" value="{{ $profile['ac_number'] }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">IFSC Code</label>
                                <input type="text" class="form-control" name="ifsc_code" value="{{ $profile['ifsc_code'] }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Branch Name</label>
                                <input type="text" class="form-control" name="branch_name" value="{{ $profile['branch_name'] }}">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">UPI ID</label>
                                <input type="text" class="form-control" name="upi_id" value="{{ $profile['upi_id'] }}" placeholder="yourname@bank">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        </div>
                            </div>

                            <div class="profile-settings-side">
                                <div class="profile-section">
                            <div class="profile-section-head">
                                <div>
                                    <h4 class="profile-section-title mb-1">Verification Details</h4>
                                    <p class="profile-section-subtitle mb-0">Keep KYC identifiers and declaration details aligned with your profile records.</p>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Aadhaar Number</label>
                                    <input type="text" class="form-control" name="aadhar_number" maxlength="20" value="{{ $profile['aadhar_number'] }}">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">PAN Number</label>
                                    <input type="text" class="form-control" name="pan_number" maxlength="20" value="{{ $profile['pan_number'] }}">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Applicant Name</label>
                                    <input type="text" class="form-control" name="applicant_name" value="{{ $profile['applicant_name'] }}">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                                </div>

                                <div class="profile-section">
                            <div class="profile-section-head">
                                <div>
                                    <h4 class="profile-section-title mb-1">Documents</h4>
                                    <p class="profile-section-subtitle mb-0">Keep identity and profile documents current for trust and compliance.</p>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Photo</label>
                                    <div class="profile-file-dropzone" data-file-dropzone>
                                        <input type="file" class="form-control profile-file-input" name="photo" accept="image/jpeg,image/png,image/jpg">
                                        <div class="profile-file-dropzone-copy">
                                            <span class="profile-file-dropzone-title">Drop photo here</span>
                                            <span class="profile-file-dropzone-subtitle">or click to browse</span>
                                        </div>
                                    </div>
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
                                <div class="col-12">
                                    <label class="form-label">Aadhaar Document</label>
                                    <div class="profile-file-dropzone" data-file-dropzone>
                                        <input type="file" class="form-control profile-file-input" name="aadhar_document" accept=".pdf,image/jpeg,image/png,image/jpg">
                                        <div class="profile-file-dropzone-copy">
                                            <span class="profile-file-dropzone-title">Drop Aadhaar file here</span>
                                            <span class="profile-file-dropzone-subtitle">or click to browse</span>
                                        </div>
                                    </div>
                                    <div class="form-text">Accepted: PDF, JPG, PNG. Max 2MB.</div>
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
                                <div class="col-12">
                                    <label class="form-label">PAN Document</label>
                                    <div class="profile-file-dropzone" data-file-dropzone>
                                        <input type="file" class="form-control profile-file-input" name="pan_document" accept=".pdf,image/jpeg,image/png,image/jpg">
                                        <div class="profile-file-dropzone-copy">
                                            <span class="profile-file-dropzone-title">Drop PAN file here</span>
                                            <span class="profile-file-dropzone-subtitle">or click to browse</span>
                                        </div>
                                    </div>
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

                                <div class="profile-section mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-3 profile-section-head">
                                <div>
                                    <h4 class="profile-section-title mb-1">Digital Signature</h4>
                                    <p class="profile-section-subtitle mb-0">Use a clean signature for agreements and verification workflows.</p>
                                </div>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="clear-profile-signature">Clear Signature</button>
                            </div>
                            <canvas id="profile-signature-pad" class="profile-signature-pad" width="640" height="180"></canvas>
                            <input type="hidden" name="signature" value="{{ $profile['signature'] }}">
                            <div class="form-text">Use the pad to update your saved signature.</div>
                            <div class="invalid-feedback d-block"></div>
                                </div>
                            </div>
                        </div>

                        <div class="profile-section education-section mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-3 profile-section-head">
                                <div>
                                    <div class="education-section-kicker">Credentials</div>
                                    <h4 class="profile-section-title mb-1">Education</h4>
                                    <p class="profile-section-subtitle mb-0">Add formal qualifications and supporting proof for profile verification.</p>
                                </div>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="add-education-row">Add Education</button>
                            </div>
                            <div id="education-rows" class="profile-stack-list education-stack-list"></div>
                            <div class="invalid-feedback d-block" data-feedback="education"></div>
                        </div>

                        <div class="profile-section mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-3 profile-section-head">
                                <div>
                                    <h4 class="profile-section-title mb-1">Availability</h4>
                                    <p class="profile-section-subtitle mb-0">Set clear day and time slots so clients can book you with confidence.</p>
                                </div>
                                <button type="button" class="btn btn-outline-secondary btn-sm" id="add-availability-row">Add Availability</button>
                            </div>
                            <div id="availability-rows" class="profile-stack-list availability-stack-list"></div>
                            <div class="invalid-feedback d-block" data-feedback="availabilities"></div>
                        </div>

                        <div class="profile-form-footer mt-4">
                            <div id="astrologer-profile-message" class="profile-message"></div>

                            <div class="profile-actions">
                                <button type="button" class="btn btn-outline-primary" id="edit-profile-btn" data-edit-profile-trigger>Edit Profile</button>
                                <button type="submit" class="btn btn-primary d-none" id="astrologer-profile-submit">
                                    <span id="astrologer-profile-submit-text">Save Changes</span>
                                    <span id="astrologer-profile-submit-spinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<div id="astrologer-profile-toast" class="profile-toast" role="status" aria-live="polite" aria-atomic="true">
    <div class="profile-toast-body">
        <span id="astrologer-profile-toast-text"></span>
    </div>
</div>

<style>
    .profile-shell {
        border: 1px solid #d8dee8;
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
        overflow: hidden;
    }

    .profile-page-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.75rem;
        padding: 0.85rem 1.1rem;
        border-bottom: 1px solid #e5eaf1;
        background: #f8fafc;
    }

    .profile-page-head-content {
        min-width: 0;
    }

    .profile-eyebrow {
        display: inline-flex;
        align-items: center;
        padding: 0.2rem 0;
        border-radius: 0;
        background: transparent;
        color: #64748b;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        margin-bottom: 0.2rem;
    }

    .profile-page-title {
        margin: 0;
        font-size: 1.12rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.2;
    }

    .profile-page-subtitle {
        max-width: 760px;
        margin-top: 0.2rem;
        color: #475569;
        font-size: 0.84rem;
        line-height: 1.4;
    }

    .profile-page-meta {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        flex-wrap: wrap;
    }

    .profile-mode-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 104px;
        padding: 0.45rem 0.75rem;
        border-radius: 999px;
        background: #eaf1f8;
        color: #334155;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        border: 1px solid #d6e1ee;
    }

    .profile-form {
        padding: 1rem 1.25rem 1.25rem;
        font-size: 0.94rem;
    }

    .profile-settings-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.9rem;
        align-items: start;
    }

    .profile-settings-main,
    .profile-settings-side {
        display: grid;
        gap: 0.9rem;
    }

    .profile-section {
        border: 1px solid #e5eaf1;
        border-radius: 10px;
        padding: 0.95rem;
        background: #fff;
        box-shadow: none;
    }

    .profile-section.mt-4 {
        margin-top: 0.9rem !important;
    }

    .education-section {
        border-color: #d8e5f1;
        background: linear-gradient(180deg, #fbfdff 0%, #ffffff 100%);
    }

    .education-section .profile-section-head {
        align-items: center;
    }

    .education-section-kicker {
        display: inline-flex;
        align-items: center;
        padding: 0.24rem 0.55rem;
        border-radius: 999px;
        background: #edf5fb;
        color: #456c8f;
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        margin-bottom: 0.45rem;
    }

    .profile-settings-main .profile-section.mt-4,
    .profile-settings-side .profile-section.mt-4 {
        margin-top: 0.9rem !important;
    }

    .profile-section-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 0.75rem;
        margin-bottom: 0.8rem;
        padding-bottom: 0.7rem;
        border-bottom: 1px solid #eef2f7;
    }

    .profile-section-title {
        margin: 0;
        font-size: 0.98rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.3;
    }

    .profile-section-subtitle {
        color: #64748b;
        font-size: 0.84rem;
        line-height: 1.45;
    }

    .profile-subcard {
        height: 100%;
        border: 1px solid #e5eaf1;
        border-radius: 8px;
        padding: 0.85rem;
        background: #ffffff;
    }

    .profile-subcard-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.6rem;
        margin-bottom: 0.75rem;
    }

    .profile-mini-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.22rem 0.5rem;
        border: 1px solid #d7e2ee;
        border-radius: 999px;
        background: #f8fafc;
        color: #64748b;
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .profile-subcard h5,
    .profile-section h5 {
        margin-bottom: 0.75rem !important;
        font-size: 0.92rem;
        font-weight: 700;
        color: #0f172a;
    }

    .profile-file-dropzone {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 84px;
        border: 1px dashed #bfd0e0;
        border-radius: 8px;
        background: #f8fafc;
        cursor: pointer;
        transition: border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
        overflow: hidden;
    }

    .profile-file-dropzone:hover {
        border-color: #8fb0cd;
        background: #f4f8fc;
    }

    .profile-file-dropzone.is-dragover {
        border-color: #f88500;
        background: #edf5fb;
        box-shadow: inset 0 0 0 1px #f88500;
    }

    .profile-file-dropzone.is-disabled {
        cursor: default;
        opacity: 0.75;
    }

    .profile-file-dropzone .profile-file-input {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
        z-index: 2;
    }

    .profile-file-dropzone-copy {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.18rem;
        padding: 0.75rem;
        text-align: center;
        pointer-events: none;
    }

    .profile-file-dropzone-copy.has-preview {
        gap: 0.45rem;
    }

    .profile-file-dropzone-preview {
        display: none;
        align-items: center;
        justify-content: center;
        width: 100%;
    }

    .profile-file-dropzone-preview.is-visible {
        display: flex;
    }

    .profile-file-dropzone-preview img {
        max-width: 84px;
        max-height: 84px;
        border-radius: 8px;
        border: 1px solid #d8e5f1;
        object-fit: cover;
        background: #ffffff;
    }

    .profile-file-dropzone-doc {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        gap: 0.25rem;
        padding: 0.45rem 0.6rem;
        border: 1px solid #e5d2cd;
        border-radius: 8px;
        background: #fff7f4;
        max-width: 100%;
    }

    .profile-file-dropzone-doc-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 52px;
        min-height: 28px;
        padding: 0.15rem 0.45rem;
        border-radius: 999px;
        background: #c2410c;
        color: #ffffff;
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }

    .profile-file-dropzone-doc-name {
        color: #7c2d12;
        font-size: 0.74rem;
        font-weight: 600;
        line-height: 1.35;
        word-break: break-word;
    }

    .profile-file-dropzone-title {
        color: #334155;
        font-size: 0.82rem;
        font-weight: 700;
        line-height: 1.2;
    }

    .profile-file-dropzone-subtitle {
        color: #64748b;
        font-size: 0.75rem;
        line-height: 1.2;
    }

    .profile-form .form-label {
        margin-bottom: 0.35rem;
        font-size: 0.78rem;
        font-weight: 600;
        letter-spacing: 0.01em;
        text-transform: none;
        color: #64748b;
    }

    .profile-form .form-control,
    .profile-form .form-select {
        min-height: 40px;
        border: 1px solid #cfd8e3;
        border-radius: 8px;
        background: #fff;
        color: #0f172a;
        box-shadow: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        font-size: 0.92rem;
        padding: 0.55rem 0.75rem;
    }

    .profile-form textarea.form-control {
        min-height: 112px;
        resize: vertical;
    }

    .profile-form .form-control:focus,
    .profile-form .form-select:focus {
        border-color: #7aa2c8;
        box-shadow: 0 0 0 0.18rem rgba(122, 162, 200, 0.16);
    }

    .profile-form.profile-readonly .form-control,
    .profile-form.profile-readonly .form-select {
        background: #f8fafc;
        border-color: #dbe3ec;
        color: #475569;
        cursor: default;
    }

    .profile-checkbox-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 0.55rem;
    }

    .profile-choice {
        position: relative;
        display: inline-flex;
        align-items: center;
        margin: 0;
    }

    .profile-choice input {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .profile-choice span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 36px;
        padding: 0.55rem 1rem;
        border: 1px solid #f0d8b0;
        border-radius: 6px;
        background: #f9edd8;
        color: #b77413;
        font-size: 0.82rem;
        line-height: 1;
        font-weight: 700;
        white-space: nowrap;
        transition: border-color 0.2s ease, background 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
    }

    .profile-choice:hover span {
        border-color: #e7bf7a;
        background: #f6e1b9;
        color: #a86409;
    }

    .profile-choice input:checked + span {
        border-color: #f59e0b;
        background: #f59e0b;
        color: #ffffff;
        box-shadow: inset 0 0 0 1px rgba(194, 120, 0, 0.18);
    }

    .profile-choice input:focus-visible + span {
        outline: none;
        box-shadow: 0 0 0 0.18rem rgba(245, 158, 11, 0.18);
    }

    .profile-form.profile-readonly .profile-choice span {
        background: #fbf1df;
        border-color: #ecd9b9;
        color: #b47a2b;
    }

    .profile-form.profile-readonly .profile-choice input:checked + span {
        background: #f3a11a;
        border-color: #e09310;
        color: #ffffff;
    }

    .profile-stack-list {
        display: grid;
        gap: 0.8rem;
    }

    .profile-repeat-card {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.75rem;
        background: #ffffff;
        margin-bottom: 0.75rem;
        box-shadow: none;
    }

    .profile-repeat-card.education-row {
        position: relative;
        border-color: #d9e6f2;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        box-shadow: 0 8px 18px rgba(79, 125, 168, 0.06);
        overflow: hidden;
    }

    .profile-repeat-card.availability-row {
        border-color: #dbe4ec;
        background: linear-gradient(180deg, #ffffff 0%, #f9fbfd 100%);
        box-shadow: 0 6px 16px rgba(51, 65, 85, 0.05);
    }

    .profile-repeat-card.education-row::before {
        content: '';
        position: absolute;
        inset: 0 auto 0 0;
        width: 4px;
        background: linear-gradient(180deg, #f78b41 0%, #f88500 100%);
    }

    .profile-repeat-card.availability-row::before {
        content: '';
        position: absolute;
        inset: 0 auto 0 0;
        width: 4px;
        background: linear-gradient(180deg, #f78b41 0%, #f88500 100%);
    }

    .profile-card-topline {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 0.8rem;
        padding-bottom: 0.65rem;
        border-bottom: 1px solid #eaf0f6;
    }

    .profile-card-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.24rem 0.56rem;
        border-radius: 999px;
        background: #e5c38f;
        color: #a77946;
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .availability-row .profile-card-badge {
        background: #eef2f6;
        color: #f88500;
    }

    .profile-card-title {
        margin: 0;
        color: rgb(230, 111, 0);
        font-size: 0.92rem;
        font-weight: 700;
    }

    .profile-card-subtitle {
        margin: 0.18rem 0 0;
        color: #64748b;
        font-size: 0.76rem;
        line-height: 1.35;
    }

    .education-row-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0.75rem;
        align-items: end;
    }

    .education-row-grid > div:not(.profile-inline-actions) {
        padding: 0.15rem 0;
    }

    .education-row-document {
        grid-column: 1 / -1;
        padding-top: 0.15rem;
    }

    .education-row .profile-card-topline {
        align-items: flex-start;
    }

    .education-row .profile-card-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        flex-shrink: 0;
    }

    .education-row .profile-file-dropzone {
        min-height: 74px;
        background: #f7fbff;
        border-color: #c9dced;
    }

    .education-row .profile-file-dropzone:hover {
        background: #eef6fd;
    }

    .education-row .profile-file-dropzone-title {
        font-size: 0.78rem;
    }

    .education-row .profile-file-dropzone-subtitle {
        font-size: 0.72rem;
    }

    .education-row .profile-inline-actions .btn-outline-danger {
        min-width: 88px;
    }

    .availability-row-grid {
        display: grid;
        grid-template-columns: 180px minmax(0, 1fr) 120px;
        gap: 0.75rem;
        align-items: start;
    }

    .availability-row-meta,
    .education-row-meta {
        display: none;
        margin-bottom: 0.4rem;
    }

    .table-cell-label {
        color: #64748b;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .availability-slot-list {
        display: grid;
        gap: 0.45rem;
        padding: 0.7rem;
        border: 1px solid #e7eef5;
        border-radius: 8px;
        background: #fbfcfe;
    }

    .slot-row-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr) 88px;
        gap: 0.55rem;
        align-items: end;
    }

    .profile-inline-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 0.45rem;
        height: 100%;
    }

    .availability-row .profile-inline-actions,
    .education-row .profile-inline-actions {
        align-items: flex-start;
        padding-top: 1.65rem;
    }

    .profile-repeat-card .slot-row + .slot-row {
        margin-top: 0.55rem;
    }

    .profile-photo-preview {
        width: 84px;
        height: 84px;
        object-fit: cover;
        border-radius: 8px;
    }

    .profile-signature-pad {
        width: 100%;
        min-height: 160px;
        border: 1px solid #d6dee8;
        border-radius: 8px;
        background: #f8fafc;
        touch-action: none;
    }

    .profile-signature-pad.disabled-canvas {
        opacity: 0.65;
    }

    .profile-form .form-text,
    .profile-form .small {
        color: #64748b;
        font-size: 0.78rem;
    }

    .profile-form .invalid-feedback,
    .profile-form [data-feedback] {
        color: #dc3545;
    }

    .profile-form-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.75rem;
        padding-top: 0.95rem;
        border-top: 1px solid #e5eaf1;
    }

    .profile-message {
        flex: 1 1 auto;
    }

    .profile-actions {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        flex-shrink: 0;
    }

    .profile-form .btn {
        min-height: 38px;
        padding: 0.5rem 0.9rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.88rem;
    }

    #astrologer-profile-submit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .profile-form .btn-primary {
        border-color: #1d4f7a;
        background: #1f5f95;
    }

    .profile-form .btn-outline-primary,
    .profile-form .btn-outline-secondary,
    .profile-form .btn-outline-danger {
        background: #fff;
    }

    .profile-form .btn-outline-primary {
        border-color: #b8c6d6;
        color: #334155;
    }

    .profile-form .btn-outline-secondary {
        border-color: #cbd5e1;
        color: #475569;
    }

    .profile-form .btn-outline-danger {
        border-color: #e5c7c7;
        color: #b45353;
    }

    .profile-form .row {
        --bs-gutter-x: 0.9rem;
        --bs-gutter-y: 0.7rem;
    }

    .profile-message .alert {
        margin-bottom: 0;
        padding: 0.65rem 0.8rem;
        border-radius: 8px;
        font-size: 0.88rem;
    }

    .profile-toast {
        position: fixed;
        left: 50%;
        bottom: 1.5rem;
        z-index: 1080;
        min-width: 280px;
        max-width: 360px;
        opacity: 0;
        pointer-events: none;
        transform: translate3d(-50%, 12px, 0);
        transition: opacity 0.22s ease, transform 0.22s ease;
    }

    .profile-toast.is-visible {
        opacity: 1;
        pointer-events: auto;
        transform: translate3d(-50%, 0, 0);
    }

    .profile-toast-body {
        padding: 0.85rem 1rem;
        border-radius: 10px;
        background: #1f8f5f;
        color: #ffffff;
        box-shadow: 0 16px 30px rgba(15, 23, 42, 0.18);
        font-size: 0.88rem;
        font-weight: 600;
        line-height: 1.4;
    }

    @media (max-width: 767.98px) {
        .profile-page-head,
        .profile-form-footer {
            flex-direction: column;
            align-items: stretch;
        }

        .profile-page-meta,
        .profile-actions {
            justify-content: flex-start;
        }

        .profile-form {
            padding: 0.9rem;
        }

        .profile-section {
            padding: 0.85rem;
        }

        .profile-settings-main,
        .profile-settings-side {
            gap: 0.9rem;
        }

        .education-row-grid,
        .availability-row-grid,
        .slot-row-grid {
            grid-template-columns: 1fr;
        }

        .education-row-document {
            grid-column: auto;
        }

        .profile-repeat-card.education-row::before {
            width: 100%;
            height: 3px;
            inset: 0 0 auto 0;
        }

        .profile-repeat-card.availability-row::before {
            width: 100%;
            height: 3px;
            inset: 0 0 auto 0;
        }

        .availability-row-meta,
        .education-row-meta {
            display: block;
        }

        .profile-inline-actions {
            justify-content: flex-start;
            padding-top: 0;
        }

        .profile-checkbox-grid {
            gap: 0.45rem;
        }

        .profile-choice span {
            min-height: 34px;
            padding-inline: 0.85rem;
        }

        .profile-toast {
            left: 1rem;
            right: 1rem;
            bottom: 1rem;
            min-width: 0;
            max-width: none;
            transform: translate3d(0, 12px, 0);
        }

        .profile-toast.is-visible {
            transform: translate3d(0, 0, 0);
        }
    }

    #astrologer-profile-form button#clear-profile-signature {
    width: auto;
}

.profile-file-dropzone-copy{ color:#eb0a0a}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    let isEditMode = false;
    const editBtn = document.getElementById('edit-profile-btn');
    const editTriggers = document.querySelectorAll('[data-edit-profile-trigger]');
    const modeBadge = document.getElementById('profile-mode-badge');
    const locationStateUrl = '/api/v1/get-state-list';
    const locationCityUrl = '/api/v1/get-city-list';

    function setFormEditable(editable) {
        const form = document.getElementById('astrologer-profile-form');
        isEditMode = editable;
        form.classList.toggle('profile-readonly', !editable);

        form.querySelectorAll('input, textarea, select').forEach(function (el) {
            if (el.type === 'hidden') return;
            if (el.type === 'file') {
                el.disabled = !editable;
            } else if (el.type === 'checkbox' || el.type === 'radio') {
                el.disabled = !editable;
            } else {
                el.readOnly = !editable;
                el.disabled = !editable;
            }
        });
        // Action buttons inside repeaters
        form.querySelectorAll('.remove-education-row, .add-slot-row, .remove-slot-row, .remove-availability-row, #add-education-row, #add-availability-row, #clear-profile-signature').forEach(function (el) {
            el.disabled = !editable;
        });
        form.querySelectorAll('[data-file-dropzone]').forEach(function (dropzone) {
            updateDropzoneState(dropzone);
        });
        // Signature pad
        const signatureCanvas = document.getElementById('profile-signature-pad');
        if (signatureCanvas) {
            signatureCanvas.style.pointerEvents = editable ? 'auto' : 'none';
            signatureCanvas.classList.toggle('disabled-canvas', !editable);
        }

        const saveBtn = document.getElementById('astrologer-profile-submit');
        if (saveBtn) {
            saveBtn.classList.toggle('d-none', !editable);
        }

        editTriggers.forEach(function (trigger) {
            trigger.classList.toggle('d-none', editable);
        });

        if (modeBadge) {
            modeBadge.textContent = editable ? 'Edit Mode' : 'View Mode';
        }
    }

    editTriggers.forEach(function (trigger) {
        trigger.addEventListener('click', function () {
            setFormEditable(true);
        });
    });

    const initialProfile = @json($profile);
    const updateUrl = @json(route('astrologer.profile.update'));
    const csrfToken = document.querySelector('#astrologer-profile-form input[name="_token"]').value;
    const messageBox = document.getElementById('astrologer-profile-message');
    const educationRows = document.getElementById('education-rows');
    const availabilityRows = document.getElementById('availability-rows');
    const submitButton = document.getElementById('astrologer-profile-submit');
    const submitButtonText = document.getElementById('astrologer-profile-submit-text');
    const submitButtonSpinner = document.getElementById('astrologer-profile-submit-spinner');
    const toast = document.getElementById('astrologer-profile-toast');
    const toastText = document.getElementById('astrologer-profile-toast-text');
    const form = document.getElementById('astrologer-profile-form');
    const stateSelect = document.getElementById('profile-state-select');
    const citySelect = document.getElementById('profile-city-select');
    const signatureCanvas = document.getElementById('profile-signature-pad');
    const signatureInput = form.querySelector('[name="signature"]');
    const clearSignatureButton = document.getElementById('clear-profile-signature');
    const photoInput = form.querySelector('[name="photo"]');
    const aadharInput = form.querySelector('[name="aadhar_document"]');
    const panInput = form.querySelector('[name="pan_document"]');
    const maxUploadBytes = 2 * 1024 * 1024;
    let toastTimeoutId = null;

    function stripSignatureCacheKey(signatureValue) {
        if (!signatureValue || signatureValue.startsWith('data:')) {
            return signatureValue;
        }

        try {
            const url = new URL(signatureValue, window.location.origin);
            url.searchParams.delete('signature_v');
            return url.toString();
        } catch (error) {
            return signatureValue
                .replace(/([?&])signature_v=[^&#]*/g, '$1')
                .replace(/[?&]$/, '');
        }
    }

    function withSignatureCacheKey(signatureValue, cacheKey) {
        if (!signatureValue || signatureValue.startsWith('data:') || cacheKey === null || cacheKey === undefined || cacheKey === '') {
            return signatureValue;
        }

        const normalizedValue = stripSignatureCacheKey(signatureValue);

        try {
            const url = new URL(normalizedValue, window.location.origin);
            url.searchParams.set('signature_v', String(cacheKey));
            return url.toString();
        } catch (error) {
            return `${normalizedValue}${normalizedValue.includes('?') ? '&' : '?'}signature_v=${encodeURIComponent(String(cacheKey))}`;
        }
    }

    function populateSelect(select, items, placeholder, selectedValue) {
        if (!select) {
            return;
        }

        const normalizedSelectedValue = selectedValue === null || selectedValue === undefined ? '' : String(selectedValue);
        let options = `<option value="">${placeholder}</option>`;

        if (Array.isArray(items)) {
            items.forEach(function (item) {
                const value = item && item.id !== undefined && item.id !== null ? String(item.id) : '';
                const label = item && item.name ? String(item.name) : value;
                const selected = value !== '' && value === normalizedSelectedValue ? ' selected' : '';
                options += `<option value="${value}"${selected}>${label}</option>`;
            });
        }

        select.innerHTML = options;
    }

    function loadCities(stateId, selectedCityId) {
        if (!citySelect) {
            return Promise.resolve();
        }

        if (!stateId) {
            populateSelect(citySelect, [], 'Select City', '');
            return Promise.resolve();
        }

        citySelect.innerHTML = '<option value="">Loading cities...</option>';

        return fetch(locationCityUrl, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ state_id: stateId }),
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Unable to load cities.');
                }

                return response.json();
            })
            .then(function (result) {
                populateSelect(citySelect, result && Array.isArray(result.data) ? result.data : [], 'Select City', selectedCityId);
            })
            .catch(function () {
                citySelect.innerHTML = '<option value="">Unable to load cities</option>';
            });
    }

    function loadStates(selectedStateId, selectedCityId) {
        if (!stateSelect) {
            return Promise.resolve();
        }

        stateSelect.innerHTML = '<option value="">Loading states...</option>';

        return fetch(locationStateUrl, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ country_id: 101 }),
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Unable to load states.');
                }

                return response.json();
            })
            .then(function (result) {
                const selectedValue = selectedStateId || stateSelect.dataset.selected || '';
                populateSelect(stateSelect, result && Array.isArray(result.data) ? result.data : [], 'Select State', selectedValue);
                return loadCities(selectedValue, selectedCityId || citySelect.dataset.selected || '');
            })
            .catch(function () {
                stateSelect.innerHTML = '<option value="">Unable to load states</option>';
                if (citySelect) {
                    citySelect.innerHTML = '<option value="">Select City</option>';
                }
            });
    }

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
                <div class="profile-card-topline">
                    <div>
                        <div class="profile-card-badge">Education Record</div>
                        <p class="profile-card-subtitle">Capture the qualification, institution, graduation year, and supporting document.</p>
                    </div>
                    <div class="profile-card-actions">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-education-row">Remove</button>
                    </div>
                </div>
                <div class="education-row-meta d-md-none">
                    <span class="table-cell-label">Education Entry</span>
                </div>
                <div class="education-row-grid">
                    <div>
                        <label class="form-label">Degree</label>
                        <input type="text" class="form-control" data-field="degree" value="${data.degree || ''}">
                    </div>
                    <div>
                        <label class="form-label">Institution</label>
                        <input type="text" class="form-control" data-field="institution" value="${data.institution || ''}">
                    </div>
                    <div>
                        <label class="form-label">Year</label>
                        <input type="number" class="form-control" data-field="year" value="${data.year || ''}">
                    </div>
                    <div class="education-row-document">
                        <label class="form-label">Education Document</label>
                        <div class="profile-file-dropzone" data-file-dropzone>
                            <input type="file" class="form-control profile-file-input" data-field="document" accept=".pdf,image/jpeg,image/png,image/jpg">
                            <div class="profile-file-dropzone-copy">
                                <span class="profile-file-dropzone-title">Drop document here</span>
                                <span class="profile-file-dropzone-subtitle">or click to browse</span>
                            </div>
                        </div>
                        <div class="form-text">Accepted: PDF, JPG, PNG. Max 1MB.</div>
                        <div class="small mt-1" data-role="education-document-link">${documentLink}</div>
                    </div>
                </div>
            </div>`;
    }

    function slotTemplate(slot) {
        const data = slot || { from: '', to: '' };

        return `
            <div class="slot-row slot-row-grid">
                <div>
                    <label class="form-label">From</label>
                    <input type="time" class="form-control" data-field="from" value="${data.from || ''}">
                </div>
                <div>
                    <label class="form-label">To</label>
                    <input type="time" class="form-control" data-field="to" value="${data.to || ''}">
                </div>
                <div class="profile-inline-actions">
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
                <div class="profile-card-topline">
                    <div>
                        <div class="profile-card-badge">Availability Block</div>
                        <p class="profile-card-subtitle">Set a day and define one or more consultation time slots.</p>
                    </div>
                </div>
                <div class="availability-row-meta d-md-none">
                    <span class="table-cell-label">Availability Entry</span>
                </div>
                <div class="availability-row-grid">
                    <div>
                        <label class="form-label">Day</label>
                        <select class="form-select" data-field="day">${options}</select>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label mb-0">Time Slots</label>
                            <button type="button" class="btn btn-outline-secondary btn-sm add-slot-row">Add Slot</button>
                        </div>
                        <div class="availability-slot-list availability-slots">${slots.map(slotTemplate).join('')}</div>
                    </div>
                    <div class="profile-inline-actions">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-availability-row">Remove Day</button>
                    </div>
                </div>
            </div>`;
    }

    function renderEducation(items) {
        const rows = Array.isArray(items) && items.length ? items : [{ degree: '', institution: '', year: '' }];
        educationRows.innerHTML = rows.map(educationRowTemplate).join('');
        bindAllDropzones(educationRows);
        setFormEditable(isEditMode);
    }

    function renderAvailabilities(items) {
        const rows = Array.isArray(items) && items.length ? items : [{ day: 'Monday', slots: [{ from: '', to: '' }] }];
        availabilityRows.innerHTML = rows.map(availabilityRowTemplate).join('');
        setFormEditable(isEditMode);
    }

    function clearErrors() {
        document.querySelectorAll('#astrologer-profile-form .is-invalid').forEach(function (element) {
            element.classList.remove('is-invalid');
        });
        document.querySelectorAll('#astrologer-profile-form .invalid-feedback').forEach(function (element) {
            element.textContent = '';
        });
        if (signatureCanvas) {
            signatureCanvas.classList.remove('border', 'border-danger');
        }
        messageBox.innerHTML = '';
    }

    function showMessage(type, text) {
        if (type === 'success') {
            showToast(text);
            return;
        }

        messageBox.innerHTML = `<div class="alert alert-${type}">${text}</div>`;
    }

    function showToast(text) {
        if (!toast || !toastText) {
            return;
        }

        toastText.textContent = text;
        toast.classList.add('is-visible');

        if (toastTimeoutId) {
            window.clearTimeout(toastTimeoutId);
        }

        toastTimeoutId = window.setTimeout(function () {
            toast.classList.remove('is-visible');
        }, 2800);
    }

    function setSubmitLoading(isLoading) {
        submitButton.disabled = isLoading;

        if (submitButtonText) {
            submitButtonText.textContent = isLoading ? 'Saving...' : 'Save Changes';
        }

        if (submitButtonSpinner) {
            submitButtonSpinner.classList.toggle('d-none', !isLoading);
        }
    }

    function getFieldFeedbackElement(field) {
        if (!field) {
            return null;
        }

        let feedback = field.parentElement ? field.parentElement.querySelector('.invalid-feedback') : null;

        if (!feedback && field.nextElementSibling && field.nextElementSibling.classList.contains('invalid-feedback')) {
            feedback = field.nextElementSibling;
        }

        if (!feedback) {
            const dropzone = field.closest('[data-file-dropzone]');
            if (dropzone && dropzone.parentElement) {
                feedback = dropzone.parentElement.querySelector('.invalid-feedback');
            }
        }

        if (!feedback && field.parentElement) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback d-block';
            field.insertAdjacentElement('afterend', feedback);
        }

        return feedback;
    }

    function setInputError(field, message) {
        if (!field) {
            return;
        }

        field.classList.add('is-invalid');

        if (field.matches('canvas')) {
            field.classList.add('border', 'border-danger');
        }

        const feedback = getFieldFeedbackElement(field);
        if (feedback) {
            feedback.classList.add('d-block');
            feedback.textContent = message;
        }
    }

    function clearInputError(field) {
        if (!field) {
            return;
        }

        field.classList.remove('is-invalid');

        if (field.matches('canvas')) {
            field.classList.remove('border-danger');
        }

        const feedback = getFieldFeedbackElement(field);
        if (feedback) {
            feedback.textContent = '';
            if (!feedback.hasAttribute('data-feedback')) {
                feedback.classList.remove('d-block');
            }
        }
    }

    function setFieldError(fieldName, message) {
        if (fieldName.startsWith('education.')) {
            const groupFeedback = document.querySelector('#astrologer-profile-form [data-feedback="education"]');
            if (groupFeedback) {
                groupFeedback.classList.add('d-block');
                groupFeedback.textContent = message;
            }
            return;
        }

        if (fieldName.startsWith('availabilities.')) {
            const groupFeedback = document.querySelector('#astrologer-profile-form [data-feedback="availabilities"]');
            if (groupFeedback) {
                groupFeedback.classList.add('d-block');
                groupFeedback.textContent = message;
            }
            return;
        }

        const field = document.querySelector(`#astrologer-profile-form [name="${fieldName}"]`);
        if (!field) {
            const groupFeedback = document.querySelector(`#astrologer-profile-form [data-feedback="${fieldName}"]`);
            if (groupFeedback) {
                groupFeedback.classList.add('d-block');
                groupFeedback.textContent = message;
            }
            return;
        }

        field.classList.add('is-invalid');
        const feedback = field.parentElement.querySelector('.invalid-feedback') || field.nextElementSibling;
        if (feedback) {
            feedback.classList.add('d-block');
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

    function updateDropzoneState(dropzone) {
        const input = dropzone.querySelector('input[type="file"]');
        if (!input) {
            return;
        }

        ensureDropzonePreview(dropzone);

        const title = dropzone.querySelector('.profile-file-dropzone-title');
        const subtitle = dropzone.querySelector('.profile-file-dropzone-subtitle');
        const file = input.files && input.files[0] ? input.files[0] : null;

        dropzone.classList.toggle('is-disabled', input.disabled);

        if (file) {
            renderDropzonePreview(dropzone, file);
            if (title) {
                title.textContent = file.name;
            }
            if (subtitle) {
                subtitle.textContent = 'Click or drop another file to replace';
            }
            return;
        }

        clearDropzonePreview(dropzone);

        if (title && title.dataset.defaultText) {
            title.textContent = title.dataset.defaultText;
        }

        if (subtitle && subtitle.dataset.defaultText) {
            subtitle.textContent = subtitle.dataset.defaultText;
        }
    }

    function bindDropzone(dropzone) {
        if (!dropzone || dropzone.dataset.boundDropzone === 'true') {
            return;
        }

        const input = dropzone.querySelector('input[type="file"]');
        const title = dropzone.querySelector('.profile-file-dropzone-title');
        const subtitle = dropzone.querySelector('.profile-file-dropzone-subtitle');

        if (!input) {
            return;
        }

        dropzone.dataset.boundDropzone = 'true';

        if (title && !title.dataset.defaultText) {
            title.dataset.defaultText = title.textContent;
        }

        if (subtitle && !subtitle.dataset.defaultText) {
            subtitle.dataset.defaultText = subtitle.textContent;
        }

        ensureDropzonePreview(dropzone);

        input.addEventListener('click', function (event) {
            event.stopPropagation();
        });

        ['dragenter', 'dragover'].forEach(function (eventName) {
            dropzone.addEventListener(eventName, function (event) {
                if (input.disabled) {
                    return;
                }

                event.preventDefault();
                dropzone.classList.add('is-dragover');
            });
        });

        ['dragleave', 'dragend', 'drop'].forEach(function (eventName) {
            dropzone.addEventListener(eventName, function (event) {
                event.preventDefault();
                dropzone.classList.remove('is-dragover');
            });
        });

        dropzone.addEventListener('drop', function (event) {
            if (input.disabled) {
                return;
            }

            const files = event.dataTransfer && event.dataTransfer.files ? event.dataTransfer.files : null;
            if (!files || !files.length) {
                return;
            }

            const transfer = new DataTransfer();
            transfer.items.add(files[0]);
            input.files = transfer.files;
            input.dispatchEvent(new Event('change', { bubbles: true }));
        });

        input.addEventListener('change', function () {
            updateDropzoneState(dropzone);
        });

        updateDropzoneState(dropzone);
    }

    function ensureDropzonePreview(dropzone) {
        const copy = dropzone.querySelector('.profile-file-dropzone-copy');
        if (!copy) {
            return null;
        }

        let preview = copy.querySelector('.profile-file-dropzone-preview');
        if (!preview) {
            preview = document.createElement('div');
            preview.className = 'profile-file-dropzone-preview';
            copy.insertAdjacentElement('afterbegin', preview);
        }

        return preview;
    }

    function clearDropzonePreview(dropzone) {
        const copy = dropzone.querySelector('.profile-file-dropzone-copy');
        const preview = ensureDropzonePreview(dropzone);

        if (preview) {
            preview.innerHTML = '';
            preview.classList.remove('is-visible');
        }

        if (copy) {
            copy.classList.remove('has-preview');
        }
    }

    function renderDropzonePreview(dropzone, file) {
        const copy = dropzone.querySelector('.profile-file-dropzone-copy');
        const preview = ensureDropzonePreview(dropzone);
        if (!copy || !preview || !(file instanceof File)) {
            return;
        }

        const extension = (file.name.split('.').pop() || '').toLowerCase();
        const isImage = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(extension);

        copy.classList.add('has-preview');
        preview.classList.add('is-visible');

        if (isImage) {
            const reader = new FileReader();
            reader.onload = function (event) {
                preview.innerHTML = `<img src="${event.target.result}" alt="Selected file preview">`;
            };
            reader.readAsDataURL(file);
            return;
        }

        const badgeText = extension ? extension.slice(0, 4) : 'FILE';
        preview.innerHTML = `
            <div class="profile-file-dropzone-doc">
                <span class="profile-file-dropzone-doc-badge">${badgeText}</span>
                <span class="profile-file-dropzone-doc-name">${file.name}</span>
            </div>
        `;
    }

    function bindAllDropzones(scope) {
        (scope || document).querySelectorAll('[data-file-dropzone]').forEach(function (dropzone) {
            bindDropzone(dropzone);
        });
    }

    function validateFileSize(input, label) {
        const file = input.files && input.files[0] ? input.files[0] : null;
        if (!file) {
            return true;
        }

        if (file.size <= maxUploadBytes) {
            clearInputError(input);
            return true;
        }

        setInputError(input, `${label} must not be greater than 2MB.`);
        return false;
    }

    function hasExistingLink(containerSelector) {
        const container = document.querySelector(containerSelector);
        if (!container) {
            return false;
        }

        return Boolean(container.querySelector('a[href]:not(.d-none)'));
    }

    function validateProfileForm() {
        let valid = true;
        const initialBioValue = String(initialProfile.details_bio || '').trim();

        [
            { name: 'first_name', message: 'First name is required.' },
            { name: 'last_name', message: 'Last name is required.' },
            { name: 'display_name', message: 'Display name is required.' },
        ].forEach(function (rule) {
            const field = form.querySelector(`[name="${rule.name}"]`);
            const value = field ? field.value.trim() : '';

            if (!value) {
                setInputError(field, rule.message);
                valid = false;
                return;
            }

            clearInputError(field);
        });

        const emailField = form.querySelector('[name="email"]');
        const emailValue = emailField.value.trim();
        if (!emailValue) {
            setInputError(emailField, 'A valid email is required.');
            valid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailValue)) {
            setInputError(emailField, 'Please enter a valid email address.');
            valid = false;
        } else {
            clearInputError(emailField);
        }

        const mobileField = form.querySelector('[name="mobile_no"]');
        const mobileValue = mobileField.value.trim();
        if (!/^\d{10}$/.test(mobileValue)) {
            setInputError(mobileField, 'Enter a valid 10-digit mobile number.');
            valid = false;
        } else {
            clearInputError(mobileField);
        }

        const passwordField = form.querySelector('[name="password"]');
        const passwordValue = passwordField.value.trim();
        if (passwordValue !== '' && passwordValue.length < 8) {
            setInputError(passwordField, 'Password must be at least 8 characters.');
            valid = false;
        } else {
            clearInputError(passwordField);
        }

        const bioField = form.querySelector('[name="details_bio"]');
        const bioValue = bioField.value.trim();
        const bioWords = bioValue.split(/\s+/).filter(Boolean).length;
        if (bioValue && bioValue !== initialBioValue && bioWords < 150) {
            setInputError(bioField, 'Details bio must be at least 150 words.');
            valid = false;
        } else if (bioValue && bioValue !== initialBioValue && bioWords > 200) {
            setInputError(bioField, 'Details bio must not exceed 200 words.');
            valid = false;
        } else {
            clearInputError(bioField);
        }

        const addressField = form.querySelector('[name="address"]');
        if (addressField.value.trim() && addressField.value.trim().length < 5) {
            setInputError(addressField, 'Address is required (min 5 chars).');
            valid = false;
        } else {
            clearInputError(addressField);
        }

        const stateField = form.querySelector('[name="state_id"]');
        const cityField = form.querySelector('[name="city_id"]');
        if (stateField.value === '' && cityField.value !== '') {
            setInputError(stateField, 'State is required.');
            valid = false;
        } else {
            clearInputError(stateField);
        }

        if (cityField.value === '' && stateField.value !== '') {
            setInputError(cityField, 'City is required.');
            valid = false;
        } else {
            clearInputError(cityField);
        }

        const pinField = form.querySelector('[name="pin_code"]');
        if (pinField.value.trim() && !/^\d{6}$/.test(pinField.value.trim())) {
            setInputError(pinField, 'Pin Code must be 6 digits.');
            valid = false;
        } else {
            clearInputError(pinField);
        }

        const experienceField = form.querySelector('[name="experience"]');
        if (experienceField.value !== '' && (Number.isNaN(Number(experienceField.value)) || Number(experienceField.value) < 0)) {
            setInputError(experienceField, 'Experience is required and must be 0 or more.');
            valid = false;
        } else {
            clearInputError(experienceField);
        }

        const rateField = form.querySelector('[name="rate"]');
        if (rateField.value !== '' && (Number.isNaN(Number(rateField.value)) || Number(rateField.value) < 0)) {
            setInputError(rateField, 'Rate is required and must be 0 or more.');
            valid = false;
        } else {
            clearInputError(rateField);
        }

        if (!collectSelection('languages[]').length) {
            setFieldError('languages', 'Please select at least one language.');
            valid = false;
        } else {
            setFieldError('languages', '');
        }

        if (!collectSelection('skills[]').length) {
            setFieldError('skills', 'Please select at least one skill.');
            valid = false;
        } else {
            setFieldError('skills', '');
        }

        const educationEntries = Array.from(educationRows.querySelectorAll('.education-row'));
        let educationValid = true;
        let educationCount = 0;
        educationEntries.forEach(function (row) {
            const degreeField = row.querySelector('[data-field="degree"]');
            const institutionField = row.querySelector('[data-field="institution"]');
            const yearField = row.querySelector('[data-field="year"]');
            const documentField = row.querySelector('[data-field="document"]');
            const hasExistingDocument = Boolean(row.querySelector('[data-role="education-document-link"] a'));
            const rowTouched = Boolean(
                degreeField.value.trim()
                || institutionField.value.trim()
                || yearField.value.trim()
                || hasExistingDocument
                || (documentField.files && documentField.files[0])
            );

            if (!rowTouched) {
                clearInputError(degreeField);
                clearInputError(institutionField);
                clearInputError(yearField);
                clearInputError(documentField);
                return;
            }

            educationCount += 1;

            if (!degreeField.value.trim()) {
                setInputError(degreeField, 'Degree is required.');
                educationValid = false;
            } else {
                clearInputError(degreeField);
            }

            if (!institutionField.value.trim()) {
                setInputError(institutionField, 'Institution is required.');
                educationValid = false;
            } else {
                clearInputError(institutionField);
            }

            if (!yearField.value.trim()) {
                setInputError(yearField, 'Year is required.');
                educationValid = false;
            } else {
                clearInputError(yearField);
            }

            if (!hasExistingDocument && !(documentField.files && documentField.files[0])) {
                setInputError(documentField, 'Document is required.');
                educationValid = false;
            } else if (documentField.files && documentField.files[0] && documentField.files[0].size > maxUploadBytes) {
                setInputError(documentField, 'File size must not be greater than 1MB.');
                educationValid = false;
            } else {
                clearInputError(documentField);
            }
        });

        if (!educationCount) {
            setFieldError('education', 'Please add at least one education entry.');
            valid = false;
        } else if (!educationValid) {
            setFieldError('education', 'Please fill in all required education fields.');
            valid = false;
        } else {
            setFieldError('education', '');
        }

        const availabilityEntries = Array.from(availabilityRows.querySelectorAll('.availability-row'));
        let availabilityValid = true;
        let availabilityCount = 0;
        availabilityEntries.forEach(function (row) {
            const dayField = row.querySelector('[data-field="day"]');
            const slotRows = Array.from(row.querySelectorAll('.slot-row'));
            const hasAnySlotValue = slotRows.some(function (slotRow) {
                const fromField = slotRow.querySelector('[data-field="from"]');
                const toField = slotRow.querySelector('[data-field="to"]');
                return Boolean(fromField.value || toField.value);
            });

            if (!hasAnySlotValue) {
                clearInputError(dayField);
                slotRows.forEach(function (slotRow) {
                    clearInputError(slotRow.querySelector('[data-field="from"]'));
                    clearInputError(slotRow.querySelector('[data-field="to"]'));
                });
                return;
            }

            availabilityCount += 1;

            if (!dayField.value) {
                setInputError(dayField, 'Day is required.');
                availabilityValid = false;
            } else {
                clearInputError(dayField);
            }

            slotRows.forEach(function (slotRow) {
                const fromField = slotRow.querySelector('[data-field="from"]');
                const toField = slotRow.querySelector('[data-field="to"]');

                if (!fromField.value) {
                    setInputError(fromField, 'Start time is required.');
                    availabilityValid = false;
                } else {
                    clearInputError(fromField);
                }

                if (!toField.value) {
                    setInputError(toField, 'End time is required.');
                    availabilityValid = false;
                } else if (fromField.value && toField.value <= fromField.value) {
                    setInputError(toField, 'End time must be after start time.');
                    availabilityValid = false;
                } else {
                    clearInputError(toField);
                }
            });
        });

        if (!availabilityCount) {
            setFieldError('availabilities', 'Please add at least one availability entry.');
            valid = false;
        } else if (!availabilityValid) {
            setFieldError('availabilities', 'Please fill in all required availability fields.');
            valid = false;
        } else {
            setFieldError('availabilities', '');
        }

        const aadharNumberField = form.querySelector('[name="aadhar_number"]');
        if (aadharNumberField.value.trim() && !/^\d{12}$/.test(aadharNumberField.value.trim())) {
            setInputError(aadharNumberField, 'Valid 12-digit Aadhar number required.');
            valid = false;
        } else {
            clearInputError(aadharNumberField);
        }

        const panNumberField = form.querySelector('[name="pan_number"]');
        if (panNumberField.value.trim() && !/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/.test(panNumberField.value.trim().toUpperCase())) {
            setInputError(panNumberField, 'Valid PAN number required (e.g., ABCDE1234F).');
            valid = false;
        } else {
            clearInputError(panNumberField);
        }

        clearInputError(photoInput);
        clearInputError(aadharInput);
        clearInputError(panInput);

        const bankingFields = [
            { field: form.querySelector('[name="ac_holder_name"]'), message: 'Account holder name is required.' },
            { field: form.querySelector('[name="bank_name"]'), message: 'Bank name is required.' },
            { field: form.querySelector('[name="ac_number"]'), message: 'Account number is required.' },
            { field: form.querySelector('[name="ifsc_code"]'), message: 'IFSC code is required.' },
            { field: form.querySelector('[name="branch_name"]'), message: 'Branch name is required.' },
            { field: form.querySelector('[name="upi_id"]'), message: 'UPI ID is required.' },
        ];
        const hasAnyBankingValue = bankingFields.some(function (rule) {
            return rule.field.value.trim() !== '';
        });

        bankingFields.forEach(function (rule) {
            if (hasAnyBankingValue && !rule.field.value.trim()) {
                setInputError(rule.field, rule.message);
                valid = false;
                return;
            }

            clearInputError(rule.field);
        });

        const applicantNameField = form.querySelector('[name="applicant_name"]');
        if (applicantNameField.value.trim() && applicantNameField.value.trim().length < 2) {
            setInputError(applicantNameField, 'Applicant name is required.');
            valid = false;
        } else {
            clearInputError(applicantNameField);
        }

        if (!signatureInput.value.trim() && !signatureHasContent) {
            setInputError(signatureCanvas, 'Digital signature is required.');
            valid = false;
        } else {
            clearInputError(signatureCanvas);
        }

        if (!valid) {
            showMessage('danger', 'Please fix the errors above before continuing.');
        }

        return valid;
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
            renderSignature(signatureInput.value, signatureVersion);
        }
    }

    function renderSignature(dataUrl, version) {
        if (!dataUrl) {
            return;
        }

        const image = new Image();
        image.onload = function () {
            if (typeof version === 'number' && version !== signatureVersion) {
                return;
            }

            const context = signatureCanvas.getContext('2d');
            const width = signatureCanvas.getBoundingClientRect().width;
            context.fillStyle = '#fffdf8';
            context.fillRect(0, 0, width, 180);
            context.drawImage(image, 0, 0, width, 180);
            signatureHasContent = true;

            if (String(dataUrl).startsWith('data:')) {
                signatureInput.value = dataUrl;
            }
        };
        image.src = withSignatureCacheKey(dataUrl, signatureCacheKey);
    }

    function loadSignatureAsBase64(signatureValue, version) {
        if (!signatureValue) {
            return Promise.resolve('');
        }

        if (signatureValue.startsWith('data:')) {
            if (typeof version === 'number' && version !== signatureVersion) {
                return Promise.resolve('');
            }

            signatureInput.value = signatureValue;
            signatureHasContent = true;
            return Promise.resolve(signatureValue);
        }

        return fetch(withSignatureCacheKey(signatureValue, signatureCacheKey), {
            credentials: 'same-origin',
            cache: 'no-store',
            headers: {
                'Cache-Control': 'no-cache',
                'Pragma': 'no-cache',
            },
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Unable to load signature image.');
                }

                return response.blob();
            })
            .then(blobToDataUrl)
            .then(function (dataUrl) {
                if (typeof version === 'number' && version !== signatureVersion) {
                    return '';
                }

                signatureInput.value = dataUrl;
                signatureHasContent = true;
                renderSignature(dataUrl, version);
                return dataUrl;
            })
            .catch(function () {
                return '';
            });
    }

    function blobToDataUrl(blob) {
        return new Promise(function (resolve, reject) {
            const reader = new FileReader();
            reader.onloadend = function () {
                if (typeof reader.result === 'string') {
                    resolve(reader.result);
                    return;
                }

                reject(new Error('Unable to convert signature to base64.'));
            };
            reader.onerror = function () {
                reject(new Error('Unable to read signature file.'));
            };
            reader.readAsDataURL(blob);
        });
    }

    function ensureSignatureBase64() {
        return signatureHydrationPromise.then(function () {
            const signatureValue = signatureInput.value.trim();

            if (!signatureHasContent && signatureValue === '') {
                return '';
            }

            if (signatureValue.startsWith('data:')) {
                return signatureValue;
            }

            if (signatureHasContent) {
                const synced = syncSignatureInputFromCanvas() ? signatureInput.value.trim() : '';
                if (synced.startsWith('data:')) {
                    return synced;
                }
            }

            if (signatureValue !== '') {
                signatureHydrationPromise = loadSignatureAsBase64(signatureValue, signatureVersion);
                return signatureHydrationPromise;
            }

            return '';
        });
    }

    function clearSignaturePad() {
        const context = signatureCanvas.getContext('2d');
        const width = signatureCanvas.getBoundingClientRect().width;
        context.fillStyle = '#fffdf8';
        context.fillRect(0, 0, width, 180);
        signatureVersion += 1;
        signatureCacheKey = null;
        signatureHasContent = false;
        signatureInput.value = '';
        signatureHydrationPromise = Promise.resolve('');
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
    loadStates(initialProfile.state_id || '', initialProfile.city_id || '');
    bindFileLink(photoInput, 'photo-current-link', 'photo-current-link-text', 'No photo uploaded yet.', 'View selected photo');
    bindFileLink(aadharInput, 'aadhar-current-link', 'aadhar-current-link-text', 'No Aadhaar document uploaded yet.', 'View selected Aadhaar document');
    bindFileLink(panInput, 'pan-current-link', 'pan-current-link-text', 'No PAN document uploaded yet.', 'View selected PAN document');
    bindAllDropzones(form);
    let isDrawing = false;
    let lastPoint = null;
    let signatureHasContent = Boolean(signatureInput.value && signatureInput.value.trim());
    let signatureVersion = 0;
    let signatureCacheKey = null;
    let signatureHydrationPromise = Promise.resolve(signatureInput.value.trim());

    if (signatureHasContent) {
        signatureHydrationPromise = loadSignatureAsBase64(signatureInput.value.trim(), signatureVersion);
    }

    resizeCanvas();
    const initialSignatureVersion = signatureVersion;
    signatureHydrationPromise.then(function (signatureValue) {
        if (signatureValue) {
            renderSignature(signatureValue, initialSignatureVersion);
            return;
        }

        if (signatureInput.value) {
            renderSignature(signatureInput.value, initialSignatureVersion);
        }
    });
    setFormEditable(false);

    function syncSignatureInputFromCanvas() {
        if (!signatureHasContent) {
            signatureInput.value = '';
            return false;
        }

        try {
            signatureInput.value = signatureCanvas.toDataURL('image/png');
            return true;
        } catch (error) {
            return false;
        }
    }

    signatureCanvas.addEventListener('mousedown', function (event) {
        signatureVersion += 1;
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
        signatureHasContent = true;
        syncSignatureInputFromCanvas();
    });

    ['mouseup', 'mouseleave'].forEach(function (eventName) {
        signatureCanvas.addEventListener(eventName, function () {
            isDrawing = false;
            lastPoint = null;
        });
    });

    signatureCanvas.addEventListener('touchstart', function (event) {
        event.preventDefault();
        signatureVersion += 1;
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
        signatureHasContent = true;
        syncSignatureInputFromCanvas();
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

    if (stateSelect) {
        stateSelect.addEventListener('change', function () {
            loadCities(stateSelect.value, '');
        });
    }

    window.addEventListener('resize', resizeCanvas);

    document.getElementById('add-education-row').addEventListener('click', function () {
        educationRows.insertAdjacentHTML('beforeend', educationRowTemplate());
        bindAllDropzones(educationRows);
        setFormEditable(isEditMode);
    });

    document.getElementById('add-availability-row').addEventListener('click', function () {
        availabilityRows.insertAdjacentHTML('beforeend', availabilityRowTemplate());
        setFormEditable(isEditMode);
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
            setFormEditable(isEditMode);
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

        if (submitButton.classList.contains('d-none')) {
            return false;
        }

        clearErrors();

        if (!validateProfileForm()) {
            return;
        }

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

        setSubmitLoading(true);

        const education = collectEducation();
        const availabilities = collectAvailabilities();
        const formData = new FormData();
        formData.append('_method', 'PATCH');
        formData.append('first_name', form.querySelector('[name="first_name"]').value.trim());
        formData.append('last_name', form.querySelector('[name="last_name"]').value.trim());
        formData.append('email', form.querySelector('[name="email"]').value.trim());
        formData.append('mobile_no', form.querySelector('[name="mobile_no"]').value.trim());
        formData.append('display_name', form.querySelector('[name="display_name"]').value.trim());
        formData.append('short_intro', form.querySelector('[name="short_intro"]').value.trim());
        formData.append('details_bio', form.querySelector('[name="details_bio"]').value.trim());
        formData.append('address', form.querySelector('[name="address"]').value.trim());
        formData.append('state_id', form.querySelector('[name="state_id"]').value);
        formData.append('city_id', form.querySelector('[name="city_id"]').value);
        formData.append('pin_code', form.querySelector('[name="pin_code"]').value.trim());
        formData.append('consultation_mode', form.querySelector('[name="consultation_mode"]').value);
        formData.append('ac_holder_name', form.querySelector('[name="ac_holder_name"]').value.trim());
        formData.append('bank_name', form.querySelector('[name="bank_name"]').value.trim());
        formData.append('ac_number', form.querySelector('[name="ac_number"]').value.trim());
        formData.append('ifsc_code', form.querySelector('[name="ifsc_code"]').value.trim());
        formData.append('branch_name', form.querySelector('[name="branch_name"]').value.trim());
        formData.append('upi_id', form.querySelector('[name="upi_id"]').value.trim());
        formData.append('applicant_name', form.querySelector('[name="applicant_name"]').value.trim());
        formData.append('experience', form.querySelector('[name="experience"]').value);
        formData.append('rate', form.querySelector('[name="rate"]').value);
        formData.append('duration', form.querySelector('[name="duration"]').value);
        formData.append('aadhar_number', form.querySelector('[name="aadhar_number"]').value.trim());
        formData.append('pan_number', form.querySelector('[name="pan_number"]').value.trim());

        const passwordValue = form.querySelector('[name="password"]').value.trim();
        if (passwordValue) {
            formData.append('password', passwordValue);
        }

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

        ensureSignatureBase64().then(function (signatureValue) {
            if (signatureValue) {
                formData.append('signature', signatureValue);
            }

            return fetch(updateUrl, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: formData,
            });
        })
            .then(function (response) {
                return response.json().then(function (data) {
                    return { ok: response.ok, data: data };
                });
            })
            .then(function (result) {
                setSubmitLoading(false);

                if (result.ok && result.data.success) {
                    const returnedSignature = String((result.data.data && result.data.data.signature) || '').trim();
                    if (returnedSignature !== '') {
                        signatureVersion += 1;
                        signatureCacheKey = Date.now();
                        signatureInput.value = withSignatureCacheKey(returnedSignature, signatureCacheKey);
                        signatureHasContent = true;
                        signatureHydrationPromise = loadSignatureAsBase64(signatureInput.value, signatureVersion);
                    }

                    showMessage('success', result.data.message || 'Profile updated successfully.');
                    setFormEditable(false);
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
                setSubmitLoading(false);
                showMessage('danger', 'Network error. Please try again.');
            });
    });
});
</script>
@endsection
