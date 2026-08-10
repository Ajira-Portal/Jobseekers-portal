    import './api.js';

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

            // Register form submission
            $('#registerForm').on('submit', function(e) {
                e.preventDefault();
                
                // Reset errors
                $('.input-group').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                
                // Show loading
                $('#registerBtn').prop('disabled', true);
                $('#registerBtn .btn-content').html('<span class="spinner-custom"></span> Creating Account...');

                const endpoint = $('#registerForm').attr('action') || '/register';
                const loginUrl = $('#registerForm').data('login-url') || '/login';
                window.authApi.register(endpoint, $(this).serialize())
                    .done(function(response) {
                        $('#registerBtn').prop('disabled', false);
                        $('#registerBtn .btn-content').html('<i class="fas fa-user-plus"></i> Create Account');
                        
                        if (response.success) {
                            showToast('success', response.message);
                            setTimeout(() => {
                                window.location.href = response.redirect || loginUrl;
                            }, 1500);
                        }
                    })
                    .fail(function(xhr) {
                        $('#registerBtn').prop('disabled', false);
                        $('#registerBtn .btn-content').html('<i class="fas fa-user-plus"></i> Create Account');
                        
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(key => {
                                const field = $(`#${key}`);
                                if (field.length) {
                                    field.closest('.input-group').addClass('is-invalid');
                                    $(`#${key}Error`).text(errors[key][0]);
                                } else if (key === 'terms') {
                                    $('#termsError').text(errors[key][0]);
                                    $('#terms').closest('.form-group').find('.invalid-feedback').show();
                                }
                            });
                            showToast('error', 'Please fix the validation errors.');
                        } else {
                            showToast('error', 'An error occurred. Please try again.');
                        }
                    });
            });

            // Toast notification system
            function showToast(type, message) {
                const colors = {
                    success: 'bg-success',
                    error: 'bg-danger',
                    info: 'bg-info',
                    warning: 'bg-warning'
                };

                const icons = {
                    success: 'fa-check-circle',
                    error: 'fa-exclamation-circle',
                    info: 'fa-info-circle',
                    warning: 'fa-exclamation-triangle'
                };

                const textColors = {
                    success: 'text-white',
                    error: 'text-white',
                    info: 'text-white',
                    warning: 'text-dark'
                };

                const toastHtml = `
                    <div class="toast align-items-center ${colors[type]} border-0 show" role="alert">
                        <div class="d-flex">
                            <div class="toast-body ${textColors[type]}">
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
    
