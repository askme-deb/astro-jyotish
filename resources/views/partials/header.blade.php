<!-- Top Header -->
<div class="top-header">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="top-bar">
                    <i class="bi bi-telephone-forward"></i> +91 90918 40899 | <i class="bi bi-envelope-at"></i> contact@astrorajumaharaj.com

                </div>
            </div>
            <div class="col-md-6">
                <div class="account_warp">
                    <i class="bi bi-bell fs-5"></i>
                    @if(session()->has('auth.api_token'))
                    <div class="dropdown d-inline-block">
                        <button class="btn btn-light rounded-pill px-3 py-2 dropdown-toggle d-flex align-items-center gap-2" style="background: no-repeat;border: none;box-shadow: none;color: #ffffff;" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="box-shadow:0 2px 8px rgba(0,0,0,0.08);">
                            <i class="bi bi-person-circle fs-5 text-warning"></i>
                            <span class="fw-semibold">Hi, {{ session('auth.user.first_name') }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end mt-2 shadow-sm" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>My Dashboard</a></li>
                            <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="bi bi-person-lines-fill me-2"></i>Profile</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form id="header-logout-form" method="POST" action="{{ route('logout') }}" class="d-none">
                                    @csrf
                                </form>
                                <button type="button" id="header-logout-trigger" class="dropdown-item"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                            </li>
                            @push('scripts')
                            <script>
                                // ...existing code...

                                // Header logout via JS fetch
                                document.addEventListener('DOMContentLoaded', function() {
                                    const logoutTrigger = document.getElementById('header-logout-trigger');
                                    if (logoutTrigger) {
                                        logoutTrigger.addEventListener('click', function() {
                                            const form = document.getElementById('header-logout-form');
                                            if (!form) return;
                                            fetch(form.action, {
                                                    method: 'POST',
                                                    headers: {
                                                        'X-CSRF-TOKEN': csrfToken,
                                                        'Accept': 'application/json',
                                                    },
                                                    credentials: 'include',
                                                })
                                                .then(() => window.location.reload());
                                        });
                                    }
                                });
                            </script>
                            @endpush
                        </ul>
                    </div>
                    @else
                    <button class="sign-btnk" data-bs-toggle="modal" data-bs-target="#authModal">
                        <i class="bi bi-person-circle fs-5"></i> <span class="fw-semibold">Login / Register</span>
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Navbar -->
@include('partials.nav')

<!-- Modal -->
<div class="modal fade" id="authModal" tabindex="-1" data-bs-backdrop="static" aria-labelledby="authModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="row g-0">
                <!-- LEFT IMAGE (optional for branding) -->
                <div class="col-md-5 d-none d-md-flex align-items-center justify-content-center text-white p-4" style="background: linear-gradient(135deg, #ff9800 0%, #ffb74d 100%);">
                    <div class="text-center w-100">
                        <div style="background:rgba(255,255,255,0.85);border-radius:1rem;display:inline-block;padding:0.5rem 1rem;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
                            <img src="{{ asset('assets/images/Logo.png') }}" alt="Logo" class="animate__animated animate__fadeInDown" style="max-width:150px;display:block;">
                        </div>
                        <h3 class="fw-bold mb-2 animate__animated animate__fadeInUp">Welcome!</h3>
                        <p class="mb-0 animate__animated animate__fadeInUp animate__delay-1s">Sign in or create an account to access personalized astrology services.</p>
                        <div class="mt-4 animate__animated animate__fadeIn animate__delay-2s">
                            <i class="bi bi-stars" style="font-size:2rem;"></i>
                        </div>
                    </div>
                </div>
                <!-- RIGHT FORM -->
                <div class="col-md-7 col-12 p-4 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="fw-semibold mb-0" id="authModalLabel">Account Access</h4>
                        <button class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <!-- Tabs -->
                    <ul class="nav nav-pills nav-justified mb-4 auth-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link auth-tab active fw-semibold d-flex align-items-center gap-2" id="login-tab" type="button" role="tab" aria-selected="true" onclick="showTab('login')">
                                <i class="bi bi-box-arrow-in-right"></i> Login
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link auth-tab fw-semibold d-flex align-items-center gap-2" id="register-tab" type="button" role="tab" aria-selected="false" onclick="showTab('register')" style="color: #fff;">
                                <i class="bi bi-person-plus"></i> Register
                            </button>
                        </li>
                    </ul>
                    <!-- LOGIN -->
                    <div id="loginBox">
                        <div id="loginFields">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" id="header-login-email" placeholder="Enter your email" autocomplete="username">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="header-login-password" placeholder="Enter your password" autocomplete="current-password">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('header-login-password')" aria-label="Show/Hide Password">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div class="text-end mt-1">
                                    <a href="#" class="small text-decoration-none" style="color:#ff9800;" onclick="showForgotForm(event)">Forgot Password?</a>
                                </div>
                            </div>
                            <div id="header-login-error" class="alert alert-danger mt-2 d-none"></div>
                        </div>
                        <form id="forgotForm" class="flex-column gap-2 mt-2" style="max-width: 100%; display:none;">
                            <input type="email" class="form-control mb-2" placeholder="Enter your email for reset" style="font-size:0.95rem;">
                            <button type="button" class="btn w-100" style="border:1px solid #ff9800;color:#ff9800;background:#fff;">Send Reset Link</button>
                            <button type="button" class="btn btn-link w-100" style="color:#ff9800;" onclick="hideForgotForm(event)">Back to Login</button>
                        </form>
                        <div class="" id="loginButtons">
                            <button class="btn btn-primary w-100 mb-2">Login</button>
                            <div class="text-center my-2 text-muted">OR</div>
                            <button class="btn w-100 mb-2" type="button" onclick="showOtpLogin()" style="border:1px solid #ff9800;color:#ff9800;background:#fff;">Login with OTP</button>
                        </div>
                        <!-- OTP Login Section -->
                        <div class="otp-box mt-2" id="otpSection" style="display:none">
                            <div id="header-otp-alert" class="alert d-none mb-2" role="alert"></div>
                            <!-- Step 1: Enter Mobile -->
                            <div id="header-otp-step-mobile">
                                <label class="form-label">Mobile Number</label>
                                <div class="input-group mb-2">
                                    <span class="input-group-text" style="border-color: #ffb74d;">+91</span>
                                    <input type="tel" class="form-control" id="header-otp-mobile" style="border-color: #ffb74d;" maxlength="15" placeholder="Enter your mobile number" autocomplete="tel">
                                </div>
                                <button class="btn btn-warning w-100 mb-2" id="header-otp-send-btn" type="button" style="background: #ffa41f; color: #ffffff;">Send OTP</button>
                            </div>
                            <!-- Step 2: Enter OTP -->
                            <div id="header-otp-step-verify" style="display:none;">
                                <label class="form-label fw-semibold" style="color:#ff9800;">Enter OTP</label>
                                <div class="d-flex align-items-center mb-3 gap-2">
                                    <input type="tel" class="form-control border border-warning" id="header-otp-mobile-readonly" readonly style="width:100%; font-weight:500; color:#ff9800; background:#fffbe6;">
                                    <a href="#" id="header-otp-change-mobile" style="color:#ff9800; font-size:0.97rem; text-decoration:underline; cursor:pointer;">Change</a>
                                </div>
                                <div class="d-flex gap-2 justify-content-center mb-3">
                                    <input type="text" class="form-control text-center header-otp-digit border-2 border-warning" maxlength="1" style="width:2.5rem; font-size:1.5rem; background:#fffbe6; color:#ff9800; box-shadow:none;" autocomplete="one-time-code">
                                    <input type="text" class="form-control text-center header-otp-digit border-2 border-warning" maxlength="1" style="width:2.5rem; font-size:1.5rem; background:#fffbe6; color:#ff9800; box-shadow:none;" autocomplete="one-time-code">
                                    <input type="text" class="form-control text-center header-otp-digit border-2 border-warning" maxlength="1" style="width:2.5rem; font-size:1.5rem; background:#fffbe6; color:#ff9800; box-shadow:none;" autocomplete="one-time-code">
                                    <input type="text" class="form-control text-center header-otp-digit border-2 border-warning" maxlength="1" style="width:2.5rem; font-size:1.5rem; background:#fffbe6; color:#ff9800; box-shadow:none;" autocomplete="one-time-code">
                                </div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <button class="btn btn-warning flex-grow-1 me-2 fw-semibold px-4 py-2" id="header-otp-verify-btn" type="button" style="background: #ffa41f; color: #ffffff;">Verify OTP</button>
                                    <button class="btn btn-link p-0 fw-semibold" id="header-otp-resend-btn" type="button" style="color:#ff9800;">Resend</button>
                                    <span id="header-otp-resend-timer" style="display:none; margin-left:0.5rem; color:#888; font-size:0.95rem;"></span>
                                </div>
                            </div>
                            <button class="btn btn-link w-100 mt-2" type="button" onclick="showNormalLogin()" style="color:#ff9800;">Back to Password Login</button>
                        </div>
                        <!-- Social Login Compact -->
                        <!-- <div class="d-flex align-items-center justify-content-center gap-2 my-3">
                            <button class="btn btn-google d-flex align-items-center gap-1 px-3 py-2" title="Google Login">
                                <i class="bi bi-google"></i>
                            </button>
                            <button class="btn btn-facebook d-flex align-items-center gap-1 px-3 py-2" title="Facebook Login">
                                <i class="bi bi-facebook"></i>
                            </button>
                        </div> -->
                    </div>
                    <!-- REGISTER -->
                    <div id="registerBox" style="display:none">
                        <form id="registerForm">
                            <div class="mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text" class="form-control" id="regFirstName" name="first_name" placeholder="Enter your first name" required autocomplete="given-name">
                                <div class="invalid-feedback" id="regFirstNameError"></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="regLastName" name="last_name" placeholder="Enter your last name" required autocomplete="family-name">
                                <div class="invalid-feedback" id="regLastNameError"></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Mobile Number</label>
                                <input type="tel" class="form-control" id="regMobile" name="mobile_no" placeholder="Enter your mobile number" required autocomplete="tel">
                                <div class="invalid-feedback" id="regMobileError"></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" id="regEmail" name="email" placeholder="Enter your email" required autocomplete="email">
                                <div class="invalid-feedback" id="regEmailError"></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="regPassword" name="password" placeholder="Create a password" required autocomplete="new-password">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('regPassword')" aria-label="Show/Hide Password">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback" id="regPasswordError"></div>
                            </div>
                            <div id="register-error" class="alert alert-danger mt-2 d-none"></div>
                            <div id="register-success" class="alert alert-success mt-2 d-none"></div>
                            <button type="submit" class="btn btn-primary w-100">Register</button>
                        </form>

                        <!-- <div class="d-flex align-items-center my-3">
                            <hr class="flex-grow-1">
                            <span class="mx-2 text-muted small">or</span>
                            <hr class="flex-grow-1">
                        </div> -->
                        <!-- <div class="soceal_login d-flex flex-column gap-2">
                            <button class="btn btn-google w-100 d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-google"></i> Continue with Google
                            </button>
                            <button class="btn btn-facebook w-100 d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-facebook"></i> Continue with Facebook
                            </button>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .auth-tab.active {
        color: #fff !important;
        background: #ffb74d !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
    }
</style>
@push('scripts')
<script>
    // ...existing code for tab switching, password toggle, forgot password, modal animation...

    // Header modal password login AJAX
    document.addEventListener('DOMContentLoaded', function() {
        var loginBtn = document.querySelector('#loginButtons .btn-primary');
        if (loginBtn) {
            loginBtn.addEventListener('click', function(e) {
                e.preventDefault();
                var email = document.getElementById('header-login-email')?.value.trim();
                var password = document.getElementById('header-login-password')?.value;
                var errorBox = document.getElementById('header-login-error');
                if (!email || !password) {
                    if (errorBox) {
                        errorBox.textContent = 'Please enter both email and password.';
                        errorBox.classList.remove('d-none');
                    }
                    return;
                }
                loginBtn.disabled = true;
                loginBtn.textContent = 'Logging in...';
                fetch('/v1/login', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        credentials: 'include',
                        body: JSON.stringify({
                            email,
                            password
                        })
                    })
                    .then(async (response) => {
                        const data = await response.json().catch(() => ({
                            success: false,
                            message: 'Unexpected server response.'
                        }));
                        if (!response.ok || data.success === false) {
                            if (errorBox) {
                                errorBox.textContent = data.message || 'Login failed.';
                                errorBox.classList.remove('d-none');
                            }
                            loginBtn.disabled = false;
                            loginBtn.textContent = 'Login';
                            return;
                        }
                        if (errorBox) errorBox.classList.add('d-none');
                        // Store token/user in sessionStorage if needed
                        if (data.token) sessionStorage.setItem('auth_api_token', data.token);
                        if (data.user) sessionStorage.setItem('auth_user', JSON.stringify(data.user));
                        // Hide modal and redirect
                        const modal = document.getElementById('authModal');
                        if (modal) {
                            const bsModal = bootstrap.Modal.getInstance(modal) || new bootstrap.Modal(modal);
                            bsModal.hide();
                        }
                        if (data.redirect_url) {
                            window.location.href = data.redirect_url;
                        } else {
                            window.location.reload();
                        }
                    })
                    .catch(() => {
                        if (errorBox) {
                            errorBox.textContent = 'Unable to reach authentication service.';
                            errorBox.classList.remove('d-none');
                        }
                        loginBtn.disabled = false;
                        loginBtn.textContent = 'Login';
                    });
            });
        }
    });

    // Show forgot password form, hide login fields/buttons
    function showForgotForm(event) {
        if (event) event.preventDefault();
        var forgotForm = document.getElementById('forgotForm');
        var loginFields = document.getElementById('loginFields');
        var loginButtons = document.getElementById('loginButtons');
        if (forgotForm) forgotForm.style.display = 'flex';
        if (loginFields) loginFields.style.display = 'none';
        if (loginButtons) loginButtons.style.display = 'none';
    }

    // OTP Login JS (header modal)
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const alertBox = document.getElementById('header-otp-alert');
    const stepMobile = document.getElementById('header-otp-step-mobile');
    const stepVerify = document.getElementById('header-otp-step-verify');
    const mobileInput = document.getElementById('header-otp-mobile');
    const mobileReadonly = document.getElementById('header-otp-mobile-readonly');
    const otpInputs = Array.from(document.querySelectorAll('.header-otp-digit'));
    const sendBtn = document.getElementById('header-otp-send-btn');
    const verifyBtn = document.getElementById('header-otp-verify-btn');
    const changeMobileBtn = document.getElementById('header-otp-change-mobile');
    const resendBtn = document.getElementById('header-otp-resend-btn');
    const resendTimer = document.getElementById('header-otp-resend-timer');

    let headerResendCountdown = null;

    function showHeaderAlert(message, type = 'info') {
        if (!alertBox) return;
        alertBox.classList.remove('d-none', 'alert-info', 'alert-danger', 'alert-success');
        alertBox.classList.add('alert-' + type);
        alertBox.textContent = message;
    }

    function clearHeaderAlert() {
        if (!alertBox) return;
        alertBox.classList.add('d-none');
        alertBox.textContent = '';
    }

    function setHeaderLoading(button, isLoading) {
        if (!button) return;
        button.disabled = isLoading;
        if (isLoading) {
            button.dataset.originalText = button.innerText;
            button.innerText = 'Please wait...';
        } else if (button.dataset.originalText) {
            button.innerText = button.dataset.originalText;
        }
    }

    function startHeaderResendCountdown(seconds) {
        if (!resendTimer || !resendBtn) return;
        let remaining = seconds;
        resendTimer.style.display = 'inline';
        resendBtn.style.pointerEvents = 'none';
        resendBtn.style.opacity = '0.5';
        resendTimer.textContent = '(' + remaining + 's)';

        if (headerResendCountdown) clearInterval(headerResendCountdown);
        headerResendCountdown = setInterval(function() {
            remaining -= 1;
            if (remaining <= 0) {
                clearInterval(headerResendCountdown);
                resendTimer.style.display = 'none';
                resendBtn.style.pointerEvents = 'auto';
                resendBtn.style.opacity = '1';
            } else {
                resendTimer.textContent = '(' + remaining + 's)';
            }
        }, 1000);
    }

    function headerPostJson(url, payload, onSuccess) {
        clearHeaderAlert();
        fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                credentials: 'include',
                body: JSON.stringify(payload),
            })
            .then(async (response) => {
                const data = await response.json().catch(() => ({
                    success: false,
                    message: 'Unexpected server response.'
                }));

                if (!response.ok || data.success === false) {
                    const message = data.message || 'Unable to process request.';
                    showHeaderAlert(message, 'danger');
                    return;
                }

                onSuccess(data);
            })
            .catch(() => {
                showHeaderAlert('Unable to reach authentication service. Please try again.', 'danger');
            });
    }

    function getHeaderOtp() {
        if (!otpInputs.length) return '';
        return otpInputs.map(function(input) {
            return (input.value || '').trim();
        }).join('');
    }

    function clearHeaderOtp() {
        otpInputs.forEach(function(input) {
            input.value = '';
        });
        if (otpInputs[0]) {
            otpInputs[0].focus();
        }
    }

    // OTP input UX: auto-advance and backspace behavior
    otpInputs.forEach(function(input, index) {
        input.addEventListener('input', function(e) {
            const value = input.value.replace(/[^0-9]/g, '');
            input.value = value.slice(-1);

            if (value && index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
            }
        });

        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !input.value && index > 0) {
                otpInputs[index - 1].focus();
            }
        });
    });

    if (sendBtn) {
        sendBtn.addEventListener('click', function() {
            const mobile = (mobileInput?.value || '').trim();
            if (!mobile) {
                showHeaderAlert('Please enter your mobile number.', 'danger');
                return;
            }

            setHeaderLoading(sendBtn, true);

            headerPostJson("{{ route('login.otp.request') }}", {
                mobile_no: mobile,
                country_code: '91',
                context: 'header',
            }, function(data) {
                showHeaderAlert(data.message || 'OTP sent successfully.', 'success');
                if (mobileReadonly) mobileReadonly.value = mobile;
                if (stepMobile && stepVerify) {
                    stepMobile.style.display = 'none';
                    stepVerify.style.display = 'block';
                }
                startHeaderResendCountdown(30);
            });

            setTimeout(function() {
                setHeaderLoading(sendBtn, false);
            }, 600);
        });
    }

    if (verifyBtn) {
        verifyBtn.addEventListener('click', function() {
            const mobile = (mobileReadonly?.value || '').trim();
            const otp = getHeaderOtp();

            if (!otp || otp.length < 4) {
                showHeaderAlert('Please enter the 4-digit OTP.', 'danger');
                return;
            }

            setHeaderLoading(verifyBtn, true);

            headerPostJson("{{ route('login.otp.verify') }}", {
                mobile_no: mobile,
                country_code: '91',
                otp: otp,
                context: 'header',
            }, function(data) {
                showHeaderAlert(data.message || 'Logged in successfully.', 'success');
                const modal = document.getElementById('authModal');
                if (modal) {
                    const bsModal = bootstrap.Modal.getInstance(modal) || new bootstrap.Modal(modal);
                    bsModal.hide();
                }
                if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    window.location.reload();
                }
            });

            setTimeout(function() {
                setHeaderLoading(verifyBtn, false);
            }, 600);
        });
    }

    if (changeMobileBtn) {
        changeMobileBtn.addEventListener('click', function() {
            if (stepMobile && stepVerify) {
                stepVerify.style.display = 'none';
                stepMobile.style.display = 'block';
                clearHeaderAlert();
                clearHeaderOtp();
            }
        });
    }

    if (resendBtn) {
        resendBtn.addEventListener('click', function() {
            const mobile = (mobileReadonly?.value || '').trim();
            if (!mobile) {
                showHeaderAlert('Mobile number is missing. Please go back and enter it again.', 'danger');
                return;
            }

            setHeaderLoading(resendBtn, true);

            headerPostJson("{{ route('login.otp.resend') }}", {
                mobile_no: mobile,
                country_code: '91',
                context: 'header',
            }, function(data) {
                showHeaderAlert(data.message || 'OTP resent.', 'success');
                startHeaderResendCountdown(30);
            });

            setTimeout(function() {
                setHeaderLoading(resendBtn, false);
            }, 600);
        });
    }

    function showNormalLogin() {
        // Hide OTP section, show login fields and buttons
        document.getElementById('otpSection').style.display = 'none';
        document.getElementById('loginFields').style.display = 'block';
        document.getElementById('loginButtons').style.display = 'block';
        // Reset OTP modal state
        if (typeof clearHeaderAlert === 'function') clearHeaderAlert();
        if (typeof clearHeaderOtp === 'function') clearHeaderOtp();
        var stepMobile = document.getElementById('header-otp-step-mobile');
        var stepVerify = document.getElementById('header-otp-step-verify');
        if (stepMobile && stepVerify) {
            stepMobile.style.display = 'block';
            stepVerify.style.display = 'none';
        }
        var mobileInput = document.getElementById('header-otp-mobile');
        if (mobileInput) mobileInput.value = '';
        var mobileReadonly = document.getElementById('header-otp-mobile-readonly');
        if (mobileReadonly) mobileReadonly.value = '';
    }


    // Show OTP login section (for legacy or inline calls)
    function showOtpLogin() {
        document.getElementById('loginFields').style.display = 'none';
        document.getElementById('loginButtons').style.display = 'none';
        document.getElementById('otpSection').style.display = 'block';
        // Always reset OTP modal to mobile step and clear alerts/inputs
        if (typeof clearHeaderAlert === 'function') clearHeaderAlert();
        if (typeof clearHeaderOtp === 'function') clearHeaderOtp();
        var stepMobile = document.getElementById('header-otp-step-mobile');
        var stepVerify = document.getElementById('header-otp-step-verify');
        if (stepMobile && stepVerify) {
            stepMobile.style.display = 'block';
            stepVerify.style.display = 'none';
        }
        var mobileInput = document.getElementById('header-otp-mobile');
        if (mobileInput) mobileInput.value = '';
        var mobileReadonly = document.getElementById('header-otp-mobile-readonly');
        if (mobileReadonly) mobileReadonly.value = '';
    }


    // Hide forgot password form, show login fields/buttons
    function hideForgotForm(event) {
        if (event) event.preventDefault();
        var forgotForm = document.getElementById('forgotForm');
        var loginFields = document.getElementById('loginFields');
        var loginButtons = document.getElementById('loginButtons');
        if (forgotForm) forgotForm.style.display = 'none';
        if (loginFields) loginFields.style.display = 'block';
        if (loginButtons) loginButtons.style.display = 'block';
    }


    // Tab switching for login/register
    function showTab(tab) {
        var loginBox = document.getElementById('loginBox');
        var registerBox = document.getElementById('registerBox');
        var loginTab = document.getElementById('login-tab');
        var registerTab = document.getElementById('register-tab');
        if (tab === 'login') {
            if (loginBox) loginBox.style.display = 'block';
            if (registerBox) registerBox.style.display = 'none';
            if (loginTab) loginTab.classList.add('active');
            if (registerTab) registerTab.classList.remove('active');
        } else if (tab === 'register') {
            if (loginBox) loginBox.style.display = 'none';
            if (registerBox) registerBox.style.display = 'block';
            if (loginTab) loginTab.classList.remove('active');
            if (registerTab) registerTab.classList.add('active');
        }
    }


    // Register AJAX
    document.addEventListener('DOMContentLoaded', function() {
        var registerForm = document.getElementById('registerForm');
        if (registerForm) {
            registerForm.addEventListener('submit', function(e) {
                e.preventDefault();
                var first_name = document.getElementById('regFirstName').value.trim();
                var last_name = document.getElementById('regLastName').value.trim();
                var mobile_no = document.getElementById('regMobile').value.trim();
                var email = document.getElementById('regEmail').value.trim();
                var password = document.getElementById('regPassword').value;
                var errorBox = document.getElementById('register-error');
                var successBox = document.getElementById('register-success');
                // Clear previous errors
                if (errorBox) errorBox.classList.add('d-none');
                if (successBox) successBox.classList.add('d-none');
                var fieldIds = [
                    {id: 'regFirstName', error: 'regFirstNameError'},
                    {id: 'regLastName', error: 'regLastNameError'},
                    {id: 'regMobile', error: 'regMobileError'},
                    {id: 'regEmail', error: 'regEmailError'},
                    {id: 'regPassword', error: 'regPasswordError'}
                ];
                fieldIds.forEach(function(f) {
                    var input = document.getElementById(f.id);
                    var err = document.getElementById(f.error);
                    if (input) input.classList.remove('is-invalid');
                    if (err) err.textContent = '';
                });
                if (!first_name || !last_name || !mobile_no || !email || !password) {
                    if (errorBox) {
                        errorBox.textContent = 'All fields are required.';
                        errorBox.classList.remove('d-none');
                    }
                    return;
                }
                var btn = registerForm.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.textContent = 'Registering...';
                }
                fetch('/v1/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    credentials: 'include',
                    body: JSON.stringify({
                        first_name,
                        last_name,
                        mobile_no,
                        email,
                        password
                    })
                })
                .then(async (response) => {
                    const data = await response.json().catch(() => ({
                        success: false,
                        message: 'Unexpected server response.'
                    }));
                    if (!response.ok || data.success === false) {
                        // Show validation errors if present
                        if (data.errors && typeof data.errors === 'object') {
                            let messages = [];
                            Object.keys(data.errors).forEach(function(key) {
                                let fieldErrorId = null;
                                switch (key) {
                                    case 'first_name': fieldErrorId = 'regFirstNameError'; break;
                                    case 'last_name': fieldErrorId = 'regLastNameError'; break;
                                    case 'mobile_no': fieldErrorId = 'regMobileError'; break;
                                    case 'email': fieldErrorId = 'regEmailError'; break;
                                    case 'password': fieldErrorId = 'regPasswordError'; break;
                                }
                                if (fieldErrorId) {
                                    var input = document.getElementById(fieldErrorId.replace('Error',''));
                                    var err = document.getElementById(fieldErrorId);
                                    if (input) input.classList.add('is-invalid');
                                    if (err) err.textContent = data.errors[key][0];
                                }
                                messages.push(data.errors[key][0]);
                            });
                            if (errorBox) {
                                errorBox.textContent = messages.join(' ');
                                errorBox.classList.remove('d-none');
                            }
                        } else if (errorBox) {
                            errorBox.textContent = data.message || 'Registration failed.';
                            errorBox.classList.remove('d-none');
                        }
                        if (btn) {
                            btn.disabled = false;
                            btn.textContent = 'Register';
                        }
                        return;
                    }
                    if (successBox) {
                        successBox.textContent = data.message || 'Registration successful! You can now log in.';
                        successBox.classList.remove('d-none');
                    }
                    // If registration returns a redirect_url, reload or redirect
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        window.location.reload();
                    }
                })
                .catch(() => {
                    if (errorBox) {
                        errorBox.textContent = 'Unable to reach registration service.';
                        errorBox.classList.remove('d-none');
                    }
                    if (btn) {
                        btn.disabled = false;
                        btn.textContent = 'Register';
                    }
                });
            });
        }
    });
</script>
@endpush
