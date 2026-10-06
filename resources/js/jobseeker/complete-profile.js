    let currentStep = 1;
    let totalSteps = 3;
    let resumeObjectUrl = null;
    let profileImageObjectUrl = null;
    const profileConfig = document.getElementById('jobseekerCompleteProfileConfig');

    function safeTrim(value) {
        return String(value ?? '').trim();
    }

    function fieldValue(selector) {
        const field = $(selector);
        return safeTrim(field.length ? field.val() : '');
    }

    const initialResumeUrl = profileConfig?.dataset.initialResumeUrl || null;
    const initialResumeName = profileConfig?.dataset.initialResumeName || null;
    const initialProfileImageUrl = profileConfig?.dataset.initialProfileImageUrl || null;

        $(document).ready(function() {
            if (!profileConfig) {
                return;
            }

            loadEducation();
            if (typeof loadWorkExperiences === 'function' && document.getElementById('workExperienceList')) {
                loadWorkExperiences();
            }

            // Handle step click
            $('.step').click(function() {
                const step = parseInt($(this).data('step'));
                if (step <= currentStep) {
                    goToStep(step);
                }
            });

            // Education form - toggle end date
            $('#edu_is_current').change(function() {
                $('#edu_end_date').prop('disabled', this.checked);
                if (this.checked) {
                    $('#edu_end_date').val('');
                }
            });

            // Experience form - toggle end date
            $('#exp_is_current').change(function() {
                $('#exp_end_date').prop('disabled', this.checked);
                if (this.checked) {
                    $('#exp_end_date').val('');
                }
            });

            $('#resume').on('change', function() {
                const file = this.files[0];
                if (resumeObjectUrl) {
                    URL.revokeObjectURL(resumeObjectUrl);
                    resumeObjectUrl = null;
                }

                if (file) {
                    resumeObjectUrl = URL.createObjectURL(file);
                    renderResumePreview(resumeObjectUrl, file.name);
                } else {
                    renderResumePreview(initialResumeUrl, initialResumeName);
                }
            });

            $('#profile_image').on('change', function() {
                const file = this.files[0];
                if (profileImageObjectUrl) {
                    URL.revokeObjectURL(profileImageObjectUrl);
                    profileImageObjectUrl = null;
                }

                if (file) {
                    profileImageObjectUrl = URL.createObjectURL(file);
                    renderProfileImagePreview(profileImageObjectUrl);
                } else {
                    renderProfileImagePreview(initialProfileImageUrl);
                }
            });

            if (initialResumeUrl && initialResumeName) {
                renderResumePreview(initialResumeUrl, initialResumeName);
            }

            if (initialProfileImageUrl) {
                renderProfileImagePreview(initialProfileImageUrl);
            }

            // Profile form submission
            $('#profileForm').on('submit', function(e) {
                e.preventDefault();
                completeProfile();
            });

            // Update progress on load
            updateProgress();
        });

        // Global ZANID Auto-fetch and Verify Button Listeners
        $(document).on('click', '#btnVerifyZanId', function(e) {
            e.preventDefault();
            const zanId = safeTrim($('#resident_number').val());
            if (!zanId) {
                $('#resident_number').addClass('is-invalid');
                $('#residentNumberError').text('Please enter your Zanzibari Resident Number.');
                return;
            }
            fetchZanIdData(zanId);
        });

        $(document).on('change', '#resident_number', function() {
            const zanId = safeTrim($(this).val());
            if (zanId.length >= 9 && /^\d+$/.test(zanId)) {
                fetchZanIdData(zanId);
            }
        });

        // Unlock fields whenever resident number is manually changed/edited
        $(document).on('input', '#resident_number', function() {
            unlockZanIdFields();
        });

        function lockZanIdFields() {
            $('#name').prop('readonly', true).addClass('bg-light');
            $('#date_of_birth').prop('readonly', true).addClass('bg-light');
            $('#gender').css('pointer-events', 'none').addClass('bg-light').attr('tabindex', '-1');
        }

        function unlockZanIdFields() {
            $('#name').prop('readonly', false).removeClass('bg-light');
            $('#date_of_birth').prop('readonly', false).removeClass('bg-light');
            $('#gender').css('pointer-events', '').removeClass('bg-light').removeAttr('tabindex');
            $('#resident_number').removeClass('is-valid is-invalid');
            $('#residentNumberError').text('');
        }

        function fetchZanIdData(zanId) {
            const $field = $('#resident_number');
            const $error = $('#residentNumberError');
            const $btn = $('#btnVerifyZanId');
            const originalBtnHtml = $btn.html();

            $field.removeClass('is-invalid is-valid');
            $error.text('');

            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status"></span> Verifying...');

            $.ajax({
                url: '/jobseeker/profile/fetch-zanid',
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val(),
                    resident_number: zanId
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                success: function(response) {
                    $btn.prop('disabled', false).html(originalBtnHtml);
                    if (response.success && response.data) {
                        console.log('[ZANID API Success]', response.data);
                        const data = response.data;
                        if (data.name) $('#name').val(data.name);
                        if (data.gender) $('#gender').val(data.gender);
                        if (data.date_of_birth) $('#date_of_birth').val(data.date_of_birth);
                        
                        lockZanIdFields();
                        $field.addClass('is-valid');
                        if (typeof showToast === 'function') {
                            showToast('success', 'ZANID verified! Personal details auto-filled and locked.');
                        }
                    } else {
                        unlockZanIdFields();
                        $field.addClass('is-invalid');
                        $error.text(response.message || 'ZANID verification failed.');
                    }
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).html(originalBtnHtml);
                    unlockZanIdFields();
                    const msg = xhr.responseJSON?.message || 'Failed to connect to ZANID verification service.';
                    console.error('[ZANID API Error]', msg, xhr);
                    $field.addClass('is-invalid');
                    $error.text(msg);
                    if (typeof showToast === 'function') {
                        showToast('error', msg);
                    }
                }
            });
        }

        function updateProgress() {
            const progress = (currentStep / totalSteps) * 100;
            $('#progressFill').css('width', progress + '%');

            $('.step').each(function() {
                const step = parseInt($(this).data('step'));
                $(this).removeClass('active completed');
                if (step === currentStep) {
                    $(this).addClass('active');
                } else if (step < currentStep) {
                    $(this).addClass('completed');
                }
            });
        }

        function goToStep(step) {
            if (step < 1 || step > totalSteps) return;
            currentStep = step;
            $('.step-content').removeClass('active');
            $('#step' + step).addClass('active');
            updateProgress();
        }

        function nextStep(step) {
            // Validate current step before proceeding
            if (step === 2) {
                if (!validateStep1()) return;
            }
            goToStep(step);
        }

        function prevStep(step) {
            goToStep(step);
        }

        function renderResumePreview(url, fileName) {
            const container = $('#resumePreviewContainer');
            if (!url || !fileName) {
                container.html('<small class="text-muted d-block">No resume uploaded yet.</small>');
                return;
            }

            const extension = (fileName.split('.').pop() || '').toLowerCase();
            const isPdf = extension === 'pdf';

            container.html(`
                <small class="text-muted d-block mb-2">Current: <a href="${url}" target="_blank">${fileName}</a></small>
                ${isPdf ? `
                    <iframe src="${url}#toolbar=0" class="w-100 border rounded" style="height: 220px;" title="Resume Preview"></iframe>
                ` : `
                    <small class="text-muted d-block">Preview not available for .${extension} files. Open the file using the link above.</small>
                `}
            `);
        }

        function renderProfileImagePreview(url) {
            const container = $('#profileImagePreviewContainer');
            if (!url) {
                container.html('<small class="text-muted d-block">No profile photo uploaded yet.</small>');
                return;
            }

            container.html(`
                <small class="text-muted d-block mb-2">Selected photo preview</small>
                <a href="${url}" target="_blank" class="d-inline-block">
                    <img src="${url}" alt="Profile photo preview" class="img-thumbnail" style="max-height: 220px; width: auto; object-fit: cover;">
                </a>
            `);
        }

        function validateStep1() {
            let valid = true;

            // Validate name
            const name = fieldValue('#name');
            if (!name) {
                $('#name').addClass('is-invalid');
                $('#nameError').text('Please enter your full name.');
                valid = false;
            } else {
                $('#name').removeClass('is-invalid');
            }

            // Validate phone
            const phone = fieldValue('#phone');
            if (!phone) {
                $('#phone').addClass('is-invalid');
                $('#phoneError').text('Please enter your phone number.');
                valid = false;
            } else {
                $('#phone').removeClass('is-invalid');
            }

            // Validate skills
            const skills = fieldValue('#skills');
            if (!skills) {
                $('#skills').addClass('is-invalid');
                $('#skillsError').text('Please enter your skills.');
                valid = false;
            } else {
                $('#skills').removeClass('is-invalid');
            }

            return valid;
        }

        // Education Functions
        function loadEducation() {
            $.ajax({
                url: '/jobseeker/education',
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        renderEducation(response.education);
                    }
                },
                error: function() {
                    showToast('error', 'Error loading education.');
                }
            });
        }

        function renderEducation(educations) {
            const container = $('#educationList');
            if (educations.length === 0) {
                container.html(`
                    <div class="empty-state">
                        <i class="fas fa-graduation-cap"></i>
                        <p>No education added yet. Click "Add Education" to get started.</p>
                    </div>
                `);
                return;
            }

            let html = '';
            educations.forEach(edu => {
                const dateRange = edu.is_current ? 
                    `${formatDate(edu.start_date)} - Present` : 
                    `${formatDate(edu.start_date)} - ${formatDate(edu.end_date)}`;
                html += `
                    <div class="entry-card">
                        <div class="entry-info">
                            <h6>${edu.institution}</h6>
                            <p>${edu.degree} ${edu.field_of_study ? '- ' + edu.field_of_study : ''}</p>
                            <small class="text-muted">${dateRange}</small>
                        </div>
                        <div class="entry-actions">
                            <button class="btn-edit-sm" onclick="editEducation(${edu.id})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-danger-sm" onclick="deleteEducation(${edu.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
            container.html(html);
        }

        function showEducationForm() {
            $('#educationFormContainer').show();
            $('#edu_id').val('');
            $('#edu_institution').val('');
            $('#edu_degree').val('');
            $('#edu_field').val('');
            $('#edu_description').val('');
            $('#edu_start_date').val('');
            $('#edu_end_date').val('');
            $('#edu_is_current').prop('checked', false);
            $('#edu_end_date').prop('disabled', false);
            clearValidation('edu_');
            $('html, body').animate({
                scrollTop: $('#educationFormContainer').offset().top - 100
            }, 500);
        }

        function hideEducationForm() {
            $('#educationFormContainer').hide();
            clearValidation('edu_');
        }

        function saveEducation() {
            const id = $('#edu_id').val();
            const type = $('#edu_type').length && $('#edu_type').val() ? $('#edu_type').val() : 'university';
            const data = {
                type: type,
                institution: fieldValue('#edu_institution'),
                institution_name: fieldValue('#edu_institution'),
                school_name: fieldValue('#edu_school_name'),
                course: fieldValue('#edu_course') || fieldValue('#edu_field'),
                level: fieldValue('#edu_level') || fieldValue('#edu_degree'),
                degree: fieldValue('#edu_degree') || fieldValue('#edu_level'),
                field_of_study: fieldValue('#edu_field') || fieldValue('#edu_course'),
                description: fieldValue('#edu_description'),
                start_date: $('#edu_start_date').val() || $('#edu_university_start_date').val(),
                end_date: $('#edu_end_date').val() || $('#edu_university_end_date').val(),
                is_current: $('#edu_is_current').is(':checked')
            };

            // Validate
            let valid = true;
            if (type === 'university' && !data.institution && !data.institution_name) {
                $('#edu_institution').addClass('is-invalid');
                $('#edu_institutionError').text('Institution is required.');
                valid = false;
            }

            if (!valid) return;

            const url = id ? `/jobseeker/education/${id}` : '/jobseeker/education';
            const method = id ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    ...data,
                    _method: method
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showToast('success', response.message);
                        loadEducation();
                        hideEducationForm();
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(key => {
                            const field = $(`#edu_${key}`);
                            if (field.length) {
                                field.addClass('is-invalid');
                                $(`#edu_${key}Error`).text(errors[key][0]);
                            }
                        });
                    } else {
                        showToast('error', 'Error saving education.');
                    }
                }
            });
        }

        function editEducation(id) {
            $.ajax({
                url: `/jobseeker/education/${id}`,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        const edu = response.education;
                        $('#edu_id').val(edu.id);
                        $('#edu_institution').val(edu.institution);
                        $('#edu_degree').val(edu.degree);
                        $('#edu_field').val(edu.field_of_study);
                        $('#edu_description').val(edu.description);
                        $('#edu_start_date').val(edu.start_date);
                        $('#edu_end_date').val(edu.end_date);
                        $('#edu_is_current').prop('checked', edu.is_current);
                        if (edu.is_current) {
                            $('#edu_end_date').prop('disabled', true);
                        }
                        $('#educationFormContainer').show();
                        clearValidation('edu_');
                        $('html, body').animate({
                            scrollTop: $('#educationFormContainer').offset().top - 100
                        }, 500);
                    }
                },
                error: function() {
                    showToast('error', 'Error loading education details.');
                }
            });
        }

        function deleteEducation(id) {
            if (!confirm('Are you sure you want to delete this education entry?')) return;

            $.ajax({
                url: `/jobseeker/education/${id}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showToast('success', response.message);
                        loadEducation();
                    }
                },
                error: function() {
                    showToast('error', 'Error deleting education.');
                }
            });
        }

        // Work Experience Functions
        function loadWorkExperiences() {
            $.ajax({
                url: '/jobseeker/work-experiences',
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        renderExperiences(response.experiences || []);
                    }
                },
                error: function() {
                    showToast('error', 'Error loading work experiences.');
                }
            });
        }

        function renderExperiences(experiences) {
            const container = $('#experienceList');
            if (!container.length) {
                return;
            }

            if (!Array.isArray(experiences) || experiences.length === 0) {
                container.html(`
                    <div class="empty-state">
                        <i class="fas fa-briefcase"></i>
                        <p>No work experience added yet. Click "Add Work Experience" to get started.</p>
                    </div>
                `);
                return;
            }

            let html = '';
            experiences.forEach(exp => {
                const dateRange = exp.is_current
                    ? `${formatDate(exp.start_date)} - Present`
                    : `${formatDate(exp.start_date)} - ${formatDate(exp.end_date)}`;
                html += `
                    <div class="entry-card">
                        <div class="entry-info">
                            <h6>${exp.position} at ${exp.company_name}</h6>
                            <p>${exp.location || ''} ${exp.employment_type ? '| ' + exp.employment_type : ''}</p>
                            <small class="text-muted">${dateRange}</small>
                        </div>
                        <div class="entry-actions">
                            <button class="btn-edit-sm" onclick="editExperience(${exp.id})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-danger-sm" onclick="deleteExperience(${exp.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
            container.html(html);
        }

        function showExperienceForm() {
            $('#experienceFormContainer').show();
            $('#exp_id').val('');
            $('#exp_company').val('');
            $('#exp_position').val('');
            $('#exp_location').val('');
            $('#exp_type').val('');
            $('#exp_description').val('');
            $('#exp_start_date').val('');
            $('#exp_end_date').val('');
            $('#exp_is_current').prop('checked', false);
            $('#exp_end_date').prop('disabled', false);
            clearValidation('exp_');
            if ($('#experienceFormContainer').length) {
                $('html, body').animate({
                    scrollTop: $('#experienceFormContainer').offset().top - 100
                }, 500);
            }
        }

        function hideExperienceForm() {
            $('#experienceFormContainer').hide();
            clearValidation('exp_');
        }

        function saveExperience() {
            const id = $('#exp_id').val();
            const data = {
                company_name: fieldValue('#exp_company'),
                position: fieldValue('#exp_position'),
                location: fieldValue('#exp_location'),
                employment_type: $('#exp_type').val(),
                description: fieldValue('#exp_description'),
                start_date: $('#exp_start_date').val(),
                end_date: $('#exp_end_date').val(),
                is_current: $('#exp_is_current').is(':checked')
            };

            let valid = true;
            if (!data.company_name) {
                $('#exp_company').addClass('is-invalid');
                $('#exp_companyError').text('Company name is required.');
                valid = false;
            }
            if (!data.position) {
                $('#exp_position').addClass('is-invalid');
                $('#exp_positionError').text('Position is required.');
                valid = false;
            }

            if (!valid) {
                return;
            }

            const url = id ? `/jobseeker/work-experience/${id}` : '/jobseeker/work-experience';
            const method = id ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    ...data,
                    _method: method
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showToast('success', response.message);
                        loadWorkExperiences();
                        hideExperienceForm();
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(key => {
                            const field = $(`#exp_${key}`);
                            if (field.length) {
                                field.addClass('is-invalid');
                                $(`#exp_${key}Error`).text(errors[key][0]);
                            }
                        });
                    } else {
                        showToast('error', 'Error saving work experience.');
                    }
                }
            });
        }

        function editExperience(id) {
            $.ajax({
                url: `/jobseeker/work-experience/${id}`,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        const exp = response.experience;
                        $('#exp_id').val(exp.id);
                        $('#exp_company').val(exp.company_name);
                        $('#exp_position').val(exp.position);
                        $('#exp_location').val(exp.location);
                        $('#exp_type').val(exp.employment_type);
                        $('#exp_description').val(exp.description);
                        $('#exp_start_date').val(exp.start_date);
                        $('#exp_end_date').val(exp.end_date);
                        $('#exp_is_current').prop('checked', exp.is_current);
                        if (exp.is_current) {
                            $('#exp_end_date').prop('disabled', true);
                        }
                        $('#experienceFormContainer').show();
                        clearValidation('exp_');
                        if ($('#experienceFormContainer').length) {
                            $('html, body').animate({
                                scrollTop: $('#experienceFormContainer').offset().top - 100
                            }, 500);
                        }
                    }
                },
                error: function() {
                    showToast('error', 'Error loading work experience details.');
                }
            });
        }

        function deleteExperience(id) {
            if (!confirm('Are you sure you want to delete this work experience?')) {
                return;
            }

            $.ajax({
                url: `/jobseeker/work-experience/${id}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        showToast('success', response.message);
                        loadWorkExperiences();
                    }
                },
                error: function() {
                    showToast('error', 'Error deleting work experience.');
                }
            });
        }

        // Complete Profile
        function completeProfile() {
            // Validate step 1 again
            if (!validateStep1()) {
                goToStep(1);
                return;
            }

            const formData = new FormData($('#profileForm')[0]);
            
            $('#submitProfileBtn').prop('disabled', true);
            $('#submitProfileBtn').html('<span class="spinner-border spinner-border-sm me-2"></span> Saving...');

            $.ajax({
                url: '/jobseeker/complete-profile',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#submitProfileBtn').prop('disabled', false);
                    $('#submitProfileBtn').html('<i class="fas fa-check me-2"></i> Complete Profile');

                    if (response.success) {
                        showToast('success', response.message);
                        setTimeout(() => {
                            window.location.href = '/jobseeker/dashboard';
                        }, 1500);
                    }
                },
                error: function(xhr) {
                    $('#submitProfileBtn').prop('disabled', false);
                    $('#submitProfileBtn').html('<i class="fas fa-check me-2"></i> Complete Profile');

                    const message = (xhr.responseJSON && xhr.responseJSON.message)
                        ? xhr.responseJSON.message
                        : 'Error completing profile. Please try again.';

                    if (xhr.status === 422) {
                        const errors = (xhr.responseJSON && xhr.responseJSON.errors) ? xhr.responseJSON.errors : null;
                        if (errors && typeof errors === 'object') {
                            Object.keys(errors).forEach(key => {
                                const field = $(`#${key}`);
                                if (field.length) {
                                    field.addClass('is-invalid');
                                    $(`#${key}Error`).text(errors[key][0]);
                                }
                            });
                        }
                        showToast('error', message);
                    } else {
                        showToast('error', message);
                    }
                }
            });
        }

        function clearValidation(prefix) {
            $(`[id^="${prefix}"]`).removeClass('is-invalid');
            $(`[id$="Error"]`).text('');
        }

        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short' });
        }

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
                <div class="toast align-items-center ${colors[type]} border-0 show" role="alert">
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

if (profileConfig) {
    window.nextStep = nextStep;
    window.prevStep = prevStep;
    window.showEducationForm = showEducationForm;
    window.hideEducationForm = hideEducationForm;
    window.saveEducation = saveEducation;
    window.editEducation = editEducation;
    window.deleteEducation = deleteEducation;
    window.showExperienceForm = showExperienceForm;
    window.hideExperienceForm = hideExperienceForm;
    window.saveExperience = saveExperience;
    window.editExperience = editExperience;
    window.deleteExperience = deleteExperience;
}

