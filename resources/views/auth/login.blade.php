@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Login</div>
                <div class="card-body">
                    <form id="loginForm">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="loginEmail" placeholder="Enter your email" autocomplete="username">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="loginPassword" placeholder="Enter your password" autocomplete="current-password">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('loginPassword')" aria-label="Show/Hide Password">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="text-end mt-1">
                                <a href="#" class="small text-decoration-none" style="color:#ff9800;" onclick="showForgotForm(event)">Forgot Password?</a>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                    <div id="loginError" class="alert alert-danger mt-3 d-none"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
function togglePassword(id) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
}

function showForgotForm(e) {
    e.preventDefault();
    // Implement forgot password UI logic here
}

$('#loginForm').on('submit', function(e) {
    e.preventDefault();
    const email = $('#loginEmail').val();
    const password = $('#loginPassword').val();
    $.ajax({
        url: '/api/v1/login',
        method: 'POST',
        data: { email, password },
        success: function(response) {
            if (response.success) {
                // Store token and user data in sessionStorage/localStorage as needed
                sessionStorage.setItem('auth_api_token', response.token);
                sessionStorage.setItem('auth_user', JSON.stringify(response.user));
                window.location.href = response.redirect_url || '/';
            } else {
                $('#loginError').text(response.message || 'Login failed.').removeClass('d-none');
            }
        },
        error: function(xhr) {
            const msg = xhr.responseJSON?.message || 'Login failed.';
            $('#loginError').text(msg).removeClass('d-none');
        }
    });
});
</script>
@endsection
