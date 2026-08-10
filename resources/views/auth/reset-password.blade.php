<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password - JobPortal</title>
    <link rel="icon" type="image/png" href="{{ asset('images/emblem.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .auth-wrapper {
            width: 100%;
            padding: 20px;
        }
        .auth-card {
            max-width: 450px;
            margin: auto;
            padding: 40px;
            border-radius: 20px;
            background: white;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .auth-card .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .auth-card .logo i {
            font-size: 3rem;
            color: #667eea;
            background: rgba(102, 126, 234, 0.1);
            padding: 20px;
            border-radius: 50%;
        }
        .auth-card .logo h3 {
            margin-top: 15px;
            font-weight: 700;
            color: #2d3748;
        }
        .auth-card .logo p {
            color: #718096;
            font-size: 0.95rem;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .input-group-text {
            background: #f7fafc;
            border: 2px solid #e2e8f0;
            border-right: none;
            border-radius: 10px 0 0 10px;
            color: #718096;
        }
        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }
        .btn-reset {
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            transition: all 0.3s ease;
        }
        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }
        .btn-reset:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }
        .toast {
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .password-toggle {
            cursor: pointer;
            border-radius: 0 10px 10px 0;
            border-left: none;
        }
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 0.8s ease infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .back-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .password-strength {
            height: 4px;
            margin-top: 5px;
            border-radius: 2px;
            transition: all 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="logo">
                <i class="fas fa-lock"></i>
                <h3>Set New Password</h3>
                <p>Create a new password for your account</p>
            </div>

            <form id="resetForm">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="Enter your email" required>
                    </div>
                    <div class="invalid-feedback" id="emailError"></div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-semibold">New Password *</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Minimum 8 characters" required>
                        <button class="btn btn-outline-secondary password-toggle" type="button" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="password-strength" id="passwordStrength"></div>
                    <div class="invalid-feedback" id="passwordError"></div>
                    <small class="text-muted">Password must be at least 8 characters long.</small>
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label fw-semibold">Confirm Password *</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password_confirmation" 
                               name="password_confirmation" placeholder="Confirm your password" required>
                    </div>
                </div>

                <div id="resetLoading" class="text-center mb-3" style="display: none;">
                    <div class="loading-spinner"></div>
                    <p class="mt-2 text-muted" style="font-size: 0.9rem;">Resetting password...</p>
                </div>

                <button type="submit" class="btn btn-primary w-100 btn-reset" id="resetBtn">
                    <i class="fas fa-check-circle me-2"></i> Reset Password
                </button>
            </form>

            <div class="text-center mt-3">
                <a href="{{ route('login') }}" class="back-link">
                    <i class="fas fa-arrow-left me-1"></i> Back to Login
                </a>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container"></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @vite('resources/js/auth/api.js')
    
    <script>
        $(document).ready(function() {
            // Toggle password visibility
            $('#togglePassword').click(function() {
                const password = $('#password');
                const icon = $(this).find('i');
                if (password.attr('type') === 'password') {
                    password.attr('type', 'text');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    password.attr('type', 'password');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            // Password strength indicator
            $('#password').on('keyup', function() {
                const password = $(this).val();
                const strength = $('#passwordStrength');
                let color = '#e2e8f0';
                let width = '0%';
                
                if (password.length >= 8) {
                    if (password.match(/[a-z]/) && password.match(/[A-Z]/) && password.match(/[0-9]/)) {
                        color = '#48bb78';
                        width = '100%';
                    } else if (password.match(/[a-z]/) && password.match(/[A-Z]/)) {
                        color = '#4299e1';
                        width = '66%';
                    } else {
                        color = '#ed8936';
                        width = '33%';
                    }
                }
                
                strength.css({
                    'background': color,
                    'width': width
                });
            });

            // Reset password form submission
            $('#resetForm').on('submit', function(e) {
                e.preventDefault();
                
                // Reset errors
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                
                // Show loading
                $('#resetLoading').show();
                $('#resetBtn').prop('disabled', true);
                $('#resetBtn').html('<span class="loading-spinner"></span> Resetting...');

                window.authApi.resetPassword('{{ route("password.update") }}', $(this).serialize())
                    .done(function(response) {
                        $('#resetLoading').hide();
                        $('#resetBtn').prop('disabled', false);
                        $('#resetBtn').html('<i class="fas fa-check-circle me-2"></i> Reset Password');
                        
                        if (response.success) {
                            showToast('success', response.message);
                            setTimeout(() => {
                                window.location.href = response.redirect;
                            }, 2000);
                        }
                    })
                    .fail(function(xhr) {
                        $('#resetLoading').hide();
                        $('#resetBtn').prop('disabled', false);
                        $('#resetBtn').html('<i class="fas fa-check-circle me-2"></i> Reset Password');
                        
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(key => {
                                const field = $(`#${key}`);
                                field.addClass('is-invalid');
                                $(`#${key}Error`).text(errors[key][0]);
                            });
                            showToast('error', 'Please fix the validation errors.');
                        } else if (xhr.status === 400) {
                            showToast('error', xhr.responseJSON.message);
                        } else {
                            showToast('error', 'An error occurred. Please try again.');
                        }
                    });
            });

            // Toast notification system
            function showToast(type, message) {
                const colors = {
                    success: 'bg-success text-white',
                    error: 'bg-danger text-white',
                    info: 'bg-info text-white',
                    warning: 'bg-warning text-dark'
                };

                const icons = {
                    success: 'fa-check-circle',
                    error: 'fa-exclamation-circle',
                    info: 'fa-info-circle',
                    warning: 'fa-exclamation-triangle'
                };

                const toastHtml = `
                    <div class="toast align-items-center ${colors[type]} border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="fas ${icons[type]} me-2"></i>
                                ${message}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                `;

                const container = $('.toast-container');
                const toast = $(toastHtml);
                container.append(toast);

                setTimeout(() => {
                    toast.fadeOut(300, function() {
                        $(this).remove();
                    });
                }, 5000);

                toast.find('.btn-close').click(function() {
                    toast.fadeOut(300, function() {
                        $(this).remove();
                    });
                });
            }

            // Handle CSRF token for all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
    </script>
</body>
</html>