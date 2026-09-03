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

            // Login form submission
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();
                
                // Reset errors
                $('.input-group').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                
                // Show loading
                $('#loginBtn').prop('disabled', true);
                $('#loginBtn .btn-content').html('<span class="spinner-custom"></span> Signing In...');

                const endpoint = $('#loginForm').attr('action') || '/login';
                window.authApi.login(endpoint, $(this).serialize())
                    .done(function(response) {
                        $('#loginBtn').prop('disabled', false);
                        $('#loginBtn .btn-content').html('<i class="fas fa-sign-in-alt"></i> Sign In');
                        
                        if (response.success) {
                            showToast('success', response.message);
                            setTimeout(() => {
                                window.location.href = response.redirect;
                            }, 1000);
                        }
                    })
                    .fail(function(xhr) {
                        $('#loginBtn').prop('disabled', false);
                        $('#loginBtn .btn-content').html('<i class="fas fa-sign-in-alt"></i> Sign In');
                        
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            Object.keys(errors).forEach(key => {
                                const field = $(`#${key}`);
                                field.closest('.input-group').addClass('is-invalid');
                                $(`#${key}Error`).text(errors[key][0]);
                            });
                            showToast('error', 'Please fix the validation errors.');
                        } else if (xhr.status === 401 || xhr.status === 403) {
                            showToast('error', xhr.responseJSON.message);
                            $('#zan_id').closest('.input-group').addClass('is-invalid');
                            $('#password').closest('.input-group').addClass('is-invalid');
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
    
