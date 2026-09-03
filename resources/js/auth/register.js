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

            let idVerified = false;

            function lockRegistration() {
                idVerified = false;
                $('#registrationFields').prop('disabled', true);
                $('#verifiedBanner').addClass('hidden');
                $('#name').val('');
            }

            function unlockRegistration(citizen) {
                idVerified = true;
                $('#name').val(citizen.name || '');

                const meta = [];
                if (citizen.gender) meta.push(citizen.gender);
                if (citizen.date_of_birth) meta.push('DOB: ' + citizen.date_of_birth);
                $('#verifiedName').text(citizen.name || 'Identity verified');
                $('#verifiedMeta').text(meta.join(' • '));
                $('#verifiedBanner').removeClass('hidden');

                $('#registrationFields').prop('disabled', false);
                $('#email').trigger('focus');
            }

            // Any change to the ZAN ID requires a fresh verification
            $('#zan_id').on('input', function() {
                if (idVerified) {
                    lockRegistration();
                }
            });

            // Verify the ZAN ID against the Zanzibar ID system
            $('#verifyIdBtn').on('click', function() {
                const zanId = ($('#zan_id').val() || '').trim();

                $('#zan_id').closest('.input-group').removeClass('is-invalid');
                $('#zan_idError').text('');

                if (!/^[0-9]{1,12}$/.test(zanId)) {
                    $('#zan_id').closest('.input-group').addClass('is-invalid');
                    $('#zan_idError').text('The ZAN ID must contain only digits (up to 12 digits).');
                    showToast('error', 'Please enter a valid ZAN ID.');
                    return;
                }

                const $btn = $(this);
                $btn.prop('disabled', true);
                $btn.find('.btn-content').html('<span class="spinner-custom"></span> Verifying...');

                const verifyUrl = $('#registerForm').data('verify-url') || '/register/verify-id';
                window.authApi.verifyZanId(verifyUrl, { zan_id: zanId })
                    .done(function(response) {
                        if (response.success && response.citizen) {
                            unlockRegistration(response.citizen);
                            showToast('success', response.message || 'ZAN ID verified successfully.');
                        } else {
                            lockRegistration();
                            showToast('error', response.message || 'Verification failed.');
                        }
                    })
                    .fail(function(xhr) {
                        lockRegistration();
                        const message = xhr.responseJSON?.message
                            || xhr.responseJSON?.errors?.zan_id?.[0]
                            || 'Unable to verify the ZAN ID. Please try again.';
                        $('#zan_id').closest('.input-group').addClass('is-invalid');
                        $('#zan_idError').text(message);
                        showToast('error', message);
                    })
                    .always(function() {
                        $btn.prop('disabled', false);
                        $btn.find('.btn-content').html('<i class="fas fa-shield-halved"></i> Verify');
                    });
            });

            // Register form submission
            $('#registerForm').on('submit', function(e) {
                e.preventDefault();

                if (!idVerified) {
                    $('#zan_id').closest('.input-group').addClass('is-invalid');
                    $('#zan_idError').text('Please verify your ZAN ID first.');
                    showToast('error', 'Please verify your ZAN ID before creating an account.');
                    return;
                }

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
                            const message = xhr.responseJSON?.message || 'An error occurred. Please try again.';
                            if (xhr.responseJSON?.errors?.zan_id) {
                                $('#zan_id').closest('.input-group').addClass('is-invalid');
                                $('#zan_idError').text(xhr.responseJSON.errors.zan_id[0]);
                            }
                            showToast('error', message);
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
    
