<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forgot Password - JobPortal</title>
    <link rel="icon" type="image/png" href="{{ asset('images/emblem.png') }}">
    <link rel="preload" href="{{ asset('webfonts/fa-solid-900.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('webfonts/fa-regular-400.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/all.min.css') }}" rel="stylesheet">
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
        .info-text {
            background: #f7fafc;
            padding: 15px;
            border-radius: 10px;
            color: #718096;
            font-size: 0.95rem;
            border-left: 4px solid #667eea;
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="logo">
                <i class="fas fa-key"></i>
                <h3>Reset Password</h3>
                <p>Enter your email to receive reset instructions</p>
            </div>

            <div class="info-text mb-4">
                <i class="fas fa-info-circle text-primary me-2"></i>
                We'll send you a password reset link to your email address.
            </div>

            <form id="forgotForm">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="Enter your email" required autofocus>
                    </div>
                    <div class="invalid-feedback" id="emailError"></div>
                </div>

                <div id="resetLoading" class="text-center mb-3" style="display: none;">
                    <div class="loading-spinner"></div>
                    <p class="mt-2 text-muted" style="font-size: 0.9rem;">Sending reset link...</p>
                </div>

                <button type="submit" class="btn btn-primary w-100 btn-reset" id="resetBtn">
                    <i class="fas fa-paper-plane me-2"></i> Send Reset Link
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

    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    @vite('resources/js/auth/api.js')
    
    <script>
        $(document).ready(function() {
            // Forgot password form submission
            $('#forgotForm').on('submit', function(e) {
                e.preventDefault();
                
                // Reset errors
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                
                // Show loading
                $('#resetLoading').show();
                $('#resetBtn').prop('disabled', true);
                $('#resetBtn').html('<span class="loading-spinner"></span> Sending...');

                window.authApi.forgotPassword('{{ route("password.email") }}', $(this).serialize())
                    .done(function(response) {
                        $('#resetLoading').hide();
                        $('#resetBtn').prop('disabled', false);
                        $('#resetBtn').html('<i class="fas fa-paper-plane me-2"></i> Send Reset Link');
                        
                        if (response.success) {
                            showToast('success', response.message);
                            setTimeout(() => {
                                window.location.href = '{{ route("login") }}';
                            }, 3000);
                        }
                    })
                    .fail(function(xhr) {
                        $('#resetLoading').hide();
                        $('#resetBtn').prop('disabled', false);
                        $('#resetBtn').html('<i class="fas fa-paper-plane me-2"></i> Send Reset Link');
                        
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