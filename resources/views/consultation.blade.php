@extends('layouts.app')

@section('title', 'Book a Consultation')

@section('content')


<div class="container py-5 book_ing">
    <div class="form-container">

        <div class="row">

            <div class="col-md-4 dri">
                <img src="{{ asset('assets/images/login.jpg') }}" />
            </div>

            <div class="col-md-8 cdser">
                <div class="row">
                    <!-- LEFT STEPPER -->
                    <div class="col-md-4">
                        <div class="step-list">

                            <div class="step active" data-step="0">
                                <div class="step-circle"><i class="bi bi-file-earmark-person"></i></div>
                                <div class="step-title">
                                    <h6>Personal Info</h6>
                                    <small>Enter details</small>
                                </div>
                            </div>

                            <div class="step" data-step="1">
                                <div class="step-circle"><i class="bi bi-cake2"></i></div>
                                <div class="step-title">
                                    <h6>Birth Details</h6>
                                    <small>Your birth info</small>
                                </div>
                            </div>

                            <div class="step" data-step="2">
                                <div class="step-circle"><i class="bi bi-people"></i></div>
                                <div class="step-title">
                                    <h6>Consultation</h6>
                                    <small>Select service</small>
                                </div>
                            </div>

                            <div class="step" data-step="3">
                                <div class="step-circle"><i class="bi bi-credit-card"></i></div>
                                <div class="step-title">
                                    <h6>Payment</h6>
                                    <small>Choose method</small>
                                </div>
                            </div>

                            <div class="step" data-step="4">
                                <div class="step-circle"><i class="bi bi-check2-square"></i></div>
                                <div class="step-title">
                                    <h6>Complete</h6>
                                    <small>Submit form</small>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- RIGHT FORM -->
                    <div class="col-md-7">

                        <form id="multiStepForm">

                            <!-- Step 1 -->
                            <div class="form-section active">
                                <h4>Personal Information</h4>

                                <div class="mb-3">
                                    <label>Name</label>
                                    <input type="text" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label>Email</label>
                                    <input type="email" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label>Phone</label>
                                    <input type="tel" class="form-control" required>
                                </div>

                                <button type="button" class="btn btn-next next">Next</button>
                            </div>

                            <!-- Step 2 -->
                            <div class="form-section">
                                <h4>Birth Details</h4>

                                <div class="mb-3">
                                    <label>Date of Birth</label>
                                    <input type="date" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label>Time of Birth</label>
                                    <input type="time" class="form-control">
                                </div>

                                <div class="mb-3">
                                    <label>Place of Birth</label>
                                    <input type="text" class="form-control">
                                </div>

                                <button type="button" class="btn btn-secondary btn-prev prev">Previous</button>
                                <button type="button" class="btn btn-next next">Next</button>
                            </div>

                            <!-- Step 3 -->
                            <div class="form-section">
                                <h4>Consultation</h4>


                                <div class="row g-3">
                                    <!-- Consultation Type -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Consultation Type</label>
                                        <select class="form-select" name="consultation_type">
                                            <option value="video">Video Call</option>
                                            <option value="phone">Phone Call</option>
                                            <option value="inperson">In-person</option>
                                        </select>
                                    </div>

                                    <!-- Astrologer -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Choose Astrologer</label>
                                        <select class="form-select" name="astrologer" id="astrologerSelect">
                                            <option value="">-- Select Astrologer --</option>
                                            <option>Anurag Singh</option>
                                            <option>Amit Diwedi</option>
                                            <option>Nimisha Purbiya</option>
                                            <option>Satyam Gupta</option>
                                            <option>Maulik Bamania</option>
                                            <option>Pawan Tiwari</option>
                                            <option>Aditya Dubey</option>
                                            <option>Anshuman Rajak</option>
                                            <option>Navneet Kaur</option>
                                            <option>Sayali Gite</option>
                                            <option>Jay Jethwani</option>
                                            <option>Rahul Varma</option>
                                        </select>
                                    </div>

                                    <!-- Session Duration -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Session Duration</label>
                                        <input type="text" class="form-control" id="sessionDuration" value="30 Minutes" readonly>
                                    </div>

                                    <!-- Preferred Date -->
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Preferred Date</label>
                                        <input type="date" class="form-control" name="preferred_date" id="preferredDate">
                                    </div>

                                    <!-- Slots -->
                                    <div class="col-md-12">
                                        <label class="form-label fw-semibold">Available Slots</label>

                                        <div id="slotGrid" class="d-flex flex-wrap gap-2 mb-2">

                                            <button type="button" class="btn btn-outline-primary slot-btn" data-value="17:00 - 17:30">
                                                17:00 - 17:30
                                            </button>

                                            <button type="button" class="btn btn-outline-primary slot-btn" data-value="17:30 - 18:00">
                                                17:30 - 18:00
                                            </button>

                                            <button type="button" class="btn btn-outline-primary slot-btn" data-value="18:00 - 18:30">
                                                18:00 - 18:30
                                            </button>

                                        </div>

                                        <input type="hidden" name="slot_time" id="slotTimeInput">

                                        <div class="mt-1 text-muted small">
                                            Selected Slot:
                                            <strong id="slotText">None</strong>
                                        </div>

                                    </div>

                                    <!-- Notes -->
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Notes / Questions</label>
                                        <textarea class="form-control" name="notes" rows="3"
                                            placeholder="Briefly describe your concern"></textarea>
                                    </div>
                                    <div class="col-12">
                                        <button type="button" class="btn btn-secondary btn-prev prev">Previous</button>
                                        <button type="button" class="btn btn-next next">Next</button>
                                    </div>
                                </div>


                            </div>








                            <!-- Step 4 -->
                            <div class="form-section">
                                <h4>Payment</h4>

                                <div class="mb-3">
                                    <label>Select Payment Method</label>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment">
                                        <label class="form-check-label">UPI</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment">
                                        <label class="form-check-label">Credit / Debit Card</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment">
                                        <label class="form-check-label">Net Banking</label>
                                    </div>

                                </div>

                                <button type="button" class="btn btn-secondary btn-prev prev">Previous</button>
                                <button type="button" class="btn btn-next next">Next</button>
                            </div>

                            <!-- Step 5 -->
                            <div class="form-section">
                                <h4>Terms & Conditions</h4>

                                <div class="mb-3" style="max-height:150px; overflow:auto; background:white; color:black; padding:10px; border-radius:10px;">
                                    <p>
                                        By booking this consultation, you agree that astrology guidance is based on belief systems and should not replace professional medical, legal, or financial advice.
                                        Payments are non-refundable once the consultation is completed.
                                    </p>
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="termsCheck" required>
                                    <label class="form-check-label">I agree to Terms & Conditions</label>
                                </div>

                                <button type="button" class="btn btn-secondary btn-prev prev">Previous</button>
                                <button type="submit" class="btn btn-success">Submit</button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>


        </div>
    </div>
</div>


@endsection