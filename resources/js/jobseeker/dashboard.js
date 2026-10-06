import './api.js';

$(document).ready(function() {
        const dashboardConfig = document.getElementById('jobseekerDashboardConfig');
        if (!dashboardConfig) {
            return;
        }

        function safeTrim(value) {
            return String(value ?? '').trim();
        }

        function fieldValue(selector) {
            const field = $(selector);
            return safeTrim(field.length ? field.val() : '');
        }

        const userName = dashboardConfig?.dataset.userName || '';
        const userEmail = dashboardConfig?.dataset.userEmail || '';
        const profileImageViewUrl = dashboardConfig?.dataset.profileImageViewUrl || '';
        const savedResumeUrl = dashboardConfig?.dataset.savedResumeUrl || null;
        const savedResumeName = dashboardConfig?.dataset.savedResumeName || null;
        let profileCompletedState = (dashboardConfig?.dataset.profileCompleted || '0') === '1';

        function togglePreviewAccess() {
            if (profileCompletedState) {
                $('#previewProfileBtn').removeClass('d-none');
            } else {
                $('#previewProfileBtn').addClass('d-none');
            }
        }

        

        // ============================================
        // INITIALIZE
        // ============================================
        loadDashboardStats();
        loadRecentJobs();
        loadEducation();
        initSkillsDropdown();
        initLocationDropdowns();
        initEducationTypeToggle();
        togglePreviewAccess();

        // ============================================
        // NAVIGATION
        // ============================================
        $('#dashboardLink, #browseJobsBtn').click(function(e) {
            e.preventDefault();
            showDashboard();
        });

        $('#browseJobsLink').click(function(e) {
            e.preventDefault();
            showBrowseJobs();
        });

        $('#myApplicationsLink').click(function(e) {
            e.preventDefault();
            showMyApplications();
        });

        $('#profileLink').click(function(e) {
            e.preventDefault();
            showProfile();
        });

        $('#backToDashboardBtn, #backToDashboardBtn2, #backToProfileDashboardBtn').click(function(e) {
            e.preventDefault();
            showDashboard();
        });

        // ============================================
        // SEARCH JOBS
        // ============================================
        $('#searchJobsBtn').click(function() {
            loadJobs();
        });

        $('#searchJob').keypress(function(e) {
            if (e.which === 13) {
                loadJobs();
            }
        });

        // ============================================
        // SUBMIT APPLICATION
        // ============================================
        $('#submitApplication').on('click', function(e) {
            e.preventDefault();
            submitApplication();
        });

        // ============================================
        // PROFILE FORM SUBMISSION
        // ============================================
        $('#profileForm').on('submit', function(e) {
            e.preventDefault();
            completeProfile();
        });

        // ============================================
        // SKILLS DROPDOWN HANDLER
        // ============================================
        $('#skillsDropdown').on('change', function() {
            const value = $(this).val();
            if (!value) {
                return;
            }

            if (value === '__other__') {
                $('#customSkillsArea').slideDown(150);
                $('#customSkillsInput').focus();
                return;
            }

            $('#customSkillsArea').slideUp(150);
            addSkillTag(value, false);
            $(this).val('');
        });

        $('#addCustomSkillsBtn').on('click', function() {
            const customValue = fieldValue('#customSkillsInput');
            if (!customValue) {
                showToast('warning', 'Please enter at least one custom skill.');
                return;
            }

            customValue.split(',').forEach(function(skill) {
                addSkillTag(safeTrim(skill), true);
            });

            $('#customSkillsInput').val('');
        });

        $('#customSkillsInput').on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                $('#addCustomSkillsBtn').click();
            }
        });

        // ============================================
        // FILE SELECTION HANDLER
        // ============================================
        let profileImageObjectUrl = null;
        let savedProfileImageUrl = dashboardConfig?.dataset.savedProfileImageUrl || null;
        function getFreshProfileImageUrl() {
            if (!savedProfileImageUrl) {
                return null;
            }

            const separator = savedProfileImageUrl.includes('?') ? '&' : '?';
            return `${savedProfileImageUrl}${separator}t=${Date.now()}`;
        }

        function getPreviewProfileImageUrl() {
            return profileImageObjectUrl || getFreshProfileImageUrl();
        }

        function renderProfileImagePreview(url, label) {
            const container = $('#profileImagePreviewContainer');
            if (!url) {
                container.html('<small class="text-muted d-block">No profile photo uploaded yet.</small>');
                return;
            }

            container.html(`
                <small class="text-muted d-block mb-2">${label}</small>
                <a href="${url}" target="_blank" class="d-inline-block">
                    <img src="${url}" alt="Profile photo preview" class="img-thumbnail" style="max-height: 220px; width: auto; object-fit: cover;">
                </a>
            `);
        }

        $('#cover_letter').on('change', function() {
            const file = this.files[0];
            if (file) {
                if (file.type !== 'application/pdf') {
                    showToast('error', 'Please upload a PDF file.');
                    $(this).val('');
                    $('#fileInfo').hide();
                    return;
                }
                if (file.size > 2 * 1024 * 1024) {
                    showToast('error', 'File size must be less than 2MB.');
                    $(this).val('');
                    $('#fileInfo').hide();
                    return;
                }
                $('#fileName').text(file.name + ' (' + (file.size / 1024).toFixed(2) + ' KB)');
                $('#fileInfo').show();
                $('#cover_letter').removeClass('is-invalid');
                $('#cover_letterError').text('');
            } else {
                $('#fileInfo').hide();
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
                renderProfileImagePreview(profileImageObjectUrl, 'Selected photo preview');
            } else {
                renderProfileImagePreview(getFreshProfileImageUrl(), 'Current photo uploaded');
            }

            if ($('#profilePreviewModal').hasClass('show')) {
                $('#profilePreviewContent .preview-profile-photo').attr('src', getPreviewProfileImageUrl());
                $('#profilePreviewContent .preview-profile-photo-link').attr('href', getPreviewProfileImageUrl());
            }
        });

        renderProfileImagePreview(getFreshProfileImageUrl(), 'Current photo uploaded');

        // ============================================
        // REMOVE FILE
        // ============================================
        window.removeFile = function() {
            $('#cover_letter').val('');
            $('#fileInfo').hide();
            $('#cover_letter').removeClass('is-invalid');
            $('#cover_letterError').text('');
        };

        // ============================================
        // MODAL CLOSE RESET
        // ============================================
        $('#applyJobModal').on('hidden.bs.modal', function() {
            $('#cover_letter').val('');
            $('#fileInfo').hide();
            $('#cover_letter').removeClass('is-invalid');
            $('#cover_letterError').text('');
            $('#applyLoading').hide();
            $('#submitApplication').prop('disabled', false);
            $('#submitApplication').html('<i class="fas fa-paper-plane me-2"></i> Submit Application');
        });

        $('#profilePreviewModal').on('hide.bs.modal', function() {
            if (document.activeElement) {
                document.activeElement.blur();
            }
        });

        $('#profilePreviewModal').on('hidden.bs.modal', function() {
            if (!$('#previewProfileBtn').hasClass('d-none')) {
                $('#previewProfileBtn').trigger('focus');
            }
        });

        // ============================================
        // EDUCATION TYPE TOGGLE
        // ============================================
        function initEducationTypeToggle() {
            $('#edu_type').on('change', function() {
                const type = $(this).val();
                
                $('.edu-conditional-fields').removeClass('show');
                
                if (type === 'informal') {
                    $('#edu_informal_fields').addClass('show');
                    clearEducationFields();
                } else if (type === 'primary' || type === 'secondary') {
                    $('#edu_primary_secondary_fields').addClass('show');
                    clearEducationFields();
                } else if (type === 'university') {
                    $('#edu_university_fields').addClass('show');
                    clearEducationFields();
                }
            });
        }

        function clearEducationFields() {
            $('#edu_informal_description').val('');
            $('#edu_school_name').val('');
            $('#edu_institution').val('');
            $('#edu_course').val('');
            $('#edu_level').val('');
            $('#edu_start_date').val('');
            $('#edu_end_date').val('');
            $('#edu_is_current').prop('checked', false);
        }

        // ============================================
        // LOCATION DROPDOWNS
        // ============================================
        function initLocationDropdowns() {
            const initialRegion = $('#region').val();
            const initialDistrict = $('#district').val();
            const initialWard = $('#ward').val();
            const initialShehia = $('#shehia').val();

            if (initialRegion) {
                loadDistricts(initialRegion, function() {
                    if (initialDistrict) {
                        loadWards(initialDistrict, function() {
                            if (initialWard) {
                                loadShehias(initialWard, initialShehia);
                            }
                        }, initialWard);
                    }
                }, initialDistrict);
            }

            $('#region').on('change', function() {
                const regionId = $(this).val();
                $('#district').html('<option value="">Loading...</option>').prop('disabled', true);
                $('#ward').html('<option value="">Select Ward</option>').prop('disabled', true);
                $('#shehia').html('<option value="">Select Shehia</option>').prop('disabled', true);
                
                if (regionId) {
                    loadDistricts(regionId, null, null);
                } else {
                    $('#district').html('<option value="">Select District</option>').prop('disabled', true);
                    $('#ward').html('<option value="">Select Ward</option>').prop('disabled', true);
                    $('#shehia').html('<option value="">Select Shehia</option>').prop('disabled', true);
                }
            });

            $('#district').on('change', function() {
                const districtId = $(this).val();
                $('#ward').html('<option value="">Loading...</option>').prop('disabled', true);
                $('#shehia').html('<option value="">Select Shehia</option>').prop('disabled', true);
                
                if (districtId) {
                    loadWards(districtId, null, null);
                } else {
                    $('#ward').html('<option value="">Select Ward</option>').prop('disabled', true);
                    $('#shehia').html('<option value="">Select Shehia</option>').prop('disabled', true);
                }
            });

            $('#ward').on('change', function() {
                const wardId = $(this).val();
                $('#shehia').html('<option value="">Loading...</option>').prop('disabled', true);
                
                if (wardId) {
                    loadShehias(wardId, null);
                } else {
                    $('#shehia').html('<option value="">Select Shehia</option>').prop('disabled', true);
                }
            });
        }

        function togglePassportFields() {
            const hasPassport = $('#has_passport').val();

            if (hasPassport === 'yes') {
                $('#placeOfIssueWrapper').stop(true, true).slideDown(150).css('display', 'block');
                $('#passportFieldsRow').stop(true, true).slideDown(150).css('display', 'flex');
                $('#place_of_issue, #passport_number, #passport_expiry_date').prop('required', true);
                return;
            }

            $('#placeOfIssueWrapper').stop(true, true).slideUp(150);
            $('#passportFieldsRow').stop(true, true).slideUp(150);
            $('#place_of_issue').val('').removeClass('is-invalid');
            $('#passport_number').val('').removeClass('is-invalid');
            $('#passport_expiry_date').val('').removeClass('is-invalid');
            $('#place_of_issue, #passport_number, #passport_expiry_date').prop('required', false);
            $('#place_of_issueError, #passport_numberError, #passport_expiry_dateError').text('');
        }

        function loadDistricts(regionId, callback, selectedDistrictId) {
            window.jobseekerApi.getDistricts(regionId)
                .done(function(response) {
                    if (response.success) {
                        let options = '<option value="">Select District</option>';
                        response.data.forEach(function(district) {
                            options += `<option value="${district.id}">${district.name}</option>`;
                        });
                        $('#district').html(options).prop('disabled', false);
                        if (selectedDistrictId) {
                            $('#district').val(String(selectedDistrictId));
                        }
                        if (typeof callback === 'function') {
                            callback();
                        }
                    } else {
                        $('#district').html('<option value="">Error loading districts</option>').prop('disabled', true);
                    }
                })
                .fail(function() {
                    $('#district').html('<option value="">Error loading districts</option>').prop('disabled', true);
                    showToast('error', 'Error loading districts. Please try again.');
                });
        }

        function loadWards(districtId, callback, selectedWardId) {
            window.jobseekerApi.getWards(districtId)
                .done(function(response) {
                    if (response.success) {
                        let options = '<option value="">Select Ward</option>';
                        response.data.forEach(function(ward) {
                            options += `<option value="${ward.id}">${ward.name}</option>`;
                        });
                        $('#ward').html(options).prop('disabled', false);
                        if (selectedWardId) {
                            $('#ward').val(String(selectedWardId));
                        }
                        if (typeof callback === 'function') {
                            callback();
                        }
                    } else {
                        $('#ward').html('<option value="">Error loading wards</option>').prop('disabled', true);
                    }
                })
                .fail(function() {
                    $('#ward').html('<option value="">Error loading wards</option>').prop('disabled', true);
                    showToast('error', 'Error loading wards. Please try again.');
                });
        }

        function loadShehias(wardId, selectedShehiaId) {
            window.jobseekerApi.getShehias(wardId)
                .done(function(response) {
                    if (response.success) {
                        let options = '<option value="">Select Shehia</option>';
                        response.data.forEach(function(shehia) {
                            options += `<option value="${shehia.id}">${shehia.name}</option>`;
                        });
                        $('#shehia').html(options).prop('disabled', false);
                        if (selectedShehiaId) {
                            $('#shehia').val(String(selectedShehiaId));
                        }
                    } else {
                        $('#shehia').html('<option value="">Error loading shehias</option>').prop('disabled', true);
                    }
                })
                .fail(function() {
                    $('#shehia').html('<option value="">Error loading shehias</option>').prop('disabled', true);
                    showToast('error', 'Error loading shehias. Please try again.');
                });
        }

        // ============================================
        // SKILLS MANAGEMENT
        // ============================================
        function initSkillsDropdown() {
            $('#selectedSkills').off('click', '.remove-skill').on('click', '.remove-skill', function(e) {
                e.preventDefault();
                const parent = $(this).closest('.skill-tag');
                const skill = safeTrim(parent.attr('data-skill') || parent.clone().children().remove().end().text());
                window.removeSkill(skill);
            });

            updateSkillsInput();
        }

        function renderSelectedSkillsFromInput(skillsCsv) {
            const parsedSkills = String(skillsCsv || '')
                .split(',')
                .map(function(skill) {
                    return safeTrim(skill);
                })
                .filter(function(skill) {
                    return skill.length > 0;
                });

            const uniqueSkills = [];
            const seen = new Set();
            parsedSkills.forEach(function(skill) {
                const key = skill.toLowerCase();
                if (!seen.has(key)) {
                    seen.add(key);
                    uniqueSkills.push(skill);
                }
            });

            const container = $('#selectedSkills');
            container.empty();

            if (uniqueSkills.length === 0) {
                container.html('<span class="empty-message">No skills added yet.</span>');
                $('#skillsInput').val('');
                $('#skillCount').text('0');
                return;
            }

            uniqueSkills.forEach(function(skill) {
                const escapedSkill = $('<div>').text(skill).html();
                const tag = $(
                    '<span class="skill-tag" data-skill="' + escapedSkill + '">' +
                        escapedSkill +
                        '<i class="fas fa-times remove-skill"></i>' +
                    '</span>'
                );
                container.append(tag);
            });

            $('#skillsInput').val(uniqueSkills.join(', '));
            $('#skillCount').text(String(uniqueSkills.length));
        }

        function updateSkillsInput() {
            const skills = [];
            $('#selectedSkills .skill-tag').each(function() {
                const value = safeTrim($(this).attr('data-skill') || $(this).clone().children().remove().end().text() || '');
                if (value) {
                    skills.push(value);
                }
            });

            $('#skillsInput').val(skills.join(', '));
            $('#skillsInput').removeClass('is-invalid');
            $('#skillsInputError').text('');
            $('#skillCount').text(skills.length);

            if (skills.length === 0) {
                $('#selectedSkills').html('<span class="empty-message">No skills added yet.</span>');
            }
        }

        window.updateSkillsInput = updateSkillsInput;

        window.addSkillTag = function(skill, isCustom) {
            skill = safeTrim(skill);
            if (!skill) {
                return;
            }

            let exists = false;
            $('#selectedSkills .skill-tag').each(function() {
                const text = safeTrim(($(this).attr('data-skill') || $(this).clone().children().remove().end().text()) || '');
                if (text.toLowerCase() === skill.toLowerCase()) {
                    exists = true;
                    return false;
                }
            });

            if (exists) {
                showToast('warning', 'Skill already added.');
                return;
            }

            const escapedSkill = $('<div>').text(skill).html();
            const customClass = isCustom ? ' custom-skill' : '';
            const tag = $(
                '<span class="skill-tag' + customClass + '" data-skill="' + escapedSkill + '">' +
                    escapedSkill +
                    '<i class="fas fa-times remove-skill"></i>' +
                '</span>'
            );

            $('#selectedSkills .empty-message').remove();
            $('#selectedSkills').append(tag);
            window.updateSkillsInput();
        };

        window.removeSkill = function(skill) {
            $('#selectedSkills .skill-tag').each(function() {
                const text = safeTrim(($(this).attr('data-skill') || $(this).clone().children().remove().end().text()) || '');
                if (text.toLowerCase() === safeTrim(skill).toLowerCase()) {
                    $(this).remove();
                    return false;
                }
            });
            window.updateSkillsInput();
        };

        // Normalize server-rendered tags and input so deletions keep working after save.
        renderSelectedSkillsFromInput($('#skillsInput').val());

        window.saveSkills = function(callback, silent) {
            const done = typeof callback === 'function' ? callback : function() {};
            const showToastOnSuccess = silent !== true;
            const skills = fieldValue('#skillsInput');

            if (!skills) {
                $('#skillsInput').addClass('is-invalid');
                $('#skillsInputError').text('Please add at least one skill.');
                if (!silent) {
                    showToast('error', 'Please add at least one skill.');
                }
                done(false);
                return;
            }

            const saveBtn = $('#saveSkillsBtn');
            const hasSaveBtn = saveBtn.length > 0;
            const originalHtml = hasSaveBtn ? saveBtn.html() : '';
            if (hasSaveBtn) {
                saveBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Saving...');
            }

            window.jobseekerApi.saveSkills(skills)
                .done(function(response) {
                    if (hasSaveBtn) {
                        saveBtn.prop('disabled', false).html(originalHtml);
                    }
                    if (response.success) {
                        const normalizedSkills = response.skills || skills;
                        renderSelectedSkillsFromInput(normalizedSkills);
                        if (showToastOnSuccess) {
                            showToast('success', response.message || 'Skills saved successfully.');
                        }
                        done(true);
                    } else {
                        showToast('error', response.message || 'Failed to save skills.');
                        done(false);
                    }
                })
                .fail(function(xhr) {
                    if (hasSaveBtn) {
                        saveBtn.prop('disabled', false).html(originalHtml);
                    }
                    console.error('saveSkills AJAX error:', xhr.status, xhr.responseText);
                    if (xhr.status === 422 && xhr.responseJSON?.errors?.skills) {
                        $('#skillsInput').addClass('is-invalid');
                        $('#skillsInputError').text(xhr.responseJSON.errors.skills[0]);
                    }
                    showToast('error', xhr.responseJSON?.message || 'Failed to save skills.');
                    done(false);
                });
        };

        function validateStep1() {
            let valid = true;

            $('#name, #phone, #date_of_birth, #has_passport, #passport_number, #passport_expiry_date, #place_of_issue, #region, #district, #ward, #shehia, #resident_number').removeClass('is-invalid');
            $('#nameError, #phoneError, #date_of_birthError, #has_passportError, #passport_numberError, #passport_expiry_dateError, #place_of_issueError, #regionError, #districtError, #wardError, #shehiaError, #residentNumberError').text('');

            const residentNumber = fieldValue('#resident_number');
            if (!residentNumber) {
                $('#resident_number').addClass('is-invalid');
                $('#residentNumberError').text('Please enter your resident number.');
                valid = false;
            }

            const name = fieldValue('#name');
            if (!name) {
                $('#name').addClass('is-invalid');
                $('#nameError').text('Please enter your full name.');
                valid = false;
            }

            const phone = fieldValue('#phone');
            if (!phone) {
                $('#phone').addClass('is-invalid');
                $('#phoneError').text('Please enter your phone number.');
                valid = false;
            }

            if (!$('#date_of_birth').val()) {
                $('#date_of_birth').addClass('is-invalid');
                $('#date_of_birthError').text('Please enter your date of birth.');
                valid = false;
            }

            if ($('#has_passport').val() === 'yes') {
                if (!$('#place_of_issue').val()) {
                    $('#place_of_issue').addClass('is-invalid');
                    $('#place_of_issueError').text('Please select place of issue.');
                    valid = false;
                }

                if (!fieldValue('#passport_number')) {
                    $('#passport_number').addClass('is-invalid');
                    $('#passport_numberError').text('Please enter your passport number.');
                    valid = false;
                }

                if (!$('#passport_expiry_date').val()) {
                    $('#passport_expiry_date').addClass('is-invalid');
                    $('#passport_expiry_dateError').text('Please enter your passport expiry date.');
                    valid = false;
                }
            }

            if (!$('#region').val()) {
                $('#region').addClass('is-invalid');
                $('#regionError').text('Please select a region.');
                valid = false;
            }

            if (!$('#district').val()) {
                $('#district').addClass('is-invalid');
                $('#districtError').text('Please select a district.');
                valid = false;
            }

            if (!$('#ward').val()) {
                $('#ward').addClass('is-invalid');
                $('#wardError').text('Please select a ward.');
                valid = false;
            }

            if (!$('#shehia').val()) {
                $('#shehia').addClass('is-invalid');
                $('#shehiaError').text('Please select a shehia.');
                valid = false;
            }

            return valid;
        }

        function saveStep1(callback) {
            const done = typeof callback === 'function' ? callback : function() {};

            if (!validateStep1()) {
                done(false);
                return;
            }

            const currentValues = {
                resident_number: fieldValue('#resident_number'),
                name: fieldValue('#name'),
                phone: fieldValue('#phone'),
                gender: $('#gender').val() || '',
                date_of_birth: $('#date_of_birth').val() || '',
                disability: $('#disability').val() || 'no',
                disability_description: fieldValue('#disability_description'),
                has_passport: $('#has_passport').val() || 'no',
                passport_number: fieldValue('#passport_number'),
                passport_expiry_date: $('#passport_expiry_date').val() || '',
                place_of_issue: $('#place_of_issue').val() || '',
                region_id: $('#region').val() || '',
                district_id: $('#district').val() || '',
                ward_id: $('#ward').val() || '',
                shehia_id: $('#shehia').val() || ''
            };

            const resumeFile = $('#resume')[0] ? $('#resume')[0].files[0] : null;
            const profileImageFile = $('#profile_image')[0] ? $('#profile_image')[0].files[0] : null;

            // If already saved and no fields/files changed, proceed directly
            if (window.step1Saved && window.step1Data && !resumeFile && !profileImageFile) {
                const hasChanged = Object.keys(currentValues).some(key => String(currentValues[key]) !== String(window.step1Data[key] || ''));
                if (!hasChanged) {
                    done(true);
                    return;
                }
            }

            const formData = new FormData();
            Object.keys(currentValues).forEach(key => {
                formData.append(key, currentValues[key]);
            });

            if (resumeFile) {
                formData.append('resume', resumeFile);
            }
            if (profileImageFile) {
                formData.append('profile_image', profileImageFile);
            }

            const saveBtn = $('button[onclick*="nextStep(2)"]');
            const originalText = saveBtn.html();
            saveBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Saving...');

            window.jobseekerApi.updateProfile(formData)
                .done(function(response) {
                    saveBtn.prop('disabled', false).html(originalText);
                    if (response.success) {
                        window.step1Saved = true;
                        window.step1Data = currentValues;
                        if ($('#profile_image')[0].files[0]) {
                            if (profileImageObjectUrl) {
                                URL.revokeObjectURL(profileImageObjectUrl);
                                profileImageObjectUrl = null;
                            }
                            savedProfileImageUrl = profileImageViewUrl;
                            renderProfileImagePreview(getFreshProfileImageUrl(), 'Current photo uploaded');
                        }
                        showToast('success', response.message || 'General information saved.');
                        done(true);
                    } else {
                        showToast('error', response.message || 'Unable to save general information.');
                        done(false);
                    }
                })
                .fail(function(xhr) {
                    saveBtn.prop('disabled', false).html(originalText);

                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        const fieldMap = {
                            region_id: 'region',
                            district_id: 'district',
                            ward_id: 'ward',
                            shehia_id: 'shehia'
                        };

                        Object.keys(errors).forEach(function(key) {
                            const fieldId = fieldMap[key] || key;
                            const field = $('#' + fieldId);
                            if (field.length) {
                                field.addClass('is-invalid');
                                $('#' + fieldId + 'Error').text(errors[key][0]);
                            }
                        });
                        showToast('error', 'Please fix the validation errors.');
                    } else {
                        showToast('error', xhr.responseJSON?.message || 'Unable to save general information.');
                    }

                    done(false);
                });
        }

        // ============================================
        // LOAD DASHBOARD STATS
        // ============================================
        function loadDashboardStats() {
            window.jobseekerApi.getApplications()
                .done(function(response) {
                    if (response.success) {
                        const apps = response.applications || [];
                        const total = apps.length;
                        const shortlisted = apps.filter(a => a.status === 'shortlisted').length;
                        const pending = apps.filter(a => a.status === 'submitted' || a.status === 'pending' || a.status === 'reviewed').length;

                        animateNumber('#totalApplications', total);
                        animateNumber('#shortlistedApps', shortlisted);
                        animateNumber('#pendingApps', pending);
                    }
                })
                .fail(function() {
                    showToast('error', 'Error loading dashboard stats.');
                });
        }

        function animateNumber(element, target) {
            $(element).prop('Counter', 0).animate({
                Counter: target
            }, {
                duration: 800,
                easing: 'swing',
                step: function(now) {
                    $(this).text(Math.ceil(now));
                }
            });
        }

        // ============================================
        // LOAD RECENT JOBS
        // ============================================
        function loadRecentJobs() {
            window.jobseekerApi.getJobs({ per_page: 4 })
                .done(function(response) {
                    if (response.success && response.jobs && response.jobs.data) {
                        const jobs = response.jobs.data;
                        let html = '';
                        if (jobs.length === 0) {
                            html = `
                                <div class="empty-state">
                                    <i class="fas fa-briefcase"></i>
                                    <h5>No Jobs Available</h5>
                                    <p>Check back later for new job opportunities.</p>
                                </div>
                            `;
                        } else {
                            jobs.forEach(job => {
                                html += createJobCard(job);
                            });
                        }
                        $('#recentJobsList').html(html);
                    }
                })
                .fail(function() {
                    $('#recentJobsList').html('<div class="alert alert-danger">Error loading jobs.</div>');
                });
        }

        // ============================================
        // LOAD JOBS WITH FILTERS
        // ============================================
        function loadJobs(page) {
            page = page || 1;
            const search = $('#searchJob').val();
            const employmentType = $('#employmentType').val();
            const location = $('#locationFilter').val();

            window.jobseekerApi.getJobs({ 
                    search, 
                    employment_type: employmentType,
                    location,
                    page,
                    per_page: 10
                })
                .done(function(response) {
                    if (response.success && response.jobs) {
                        const jobs = response.jobs.data || [];
                        let html = '';
                        if (jobs.length === 0) {
                            html = `
                                <div class="empty-state">
                                    <i class="fas fa-search"></i>
                                    <h5>No Jobs Found</h5>
                                    <p>No jobs match your search criteria.</p>
                                    <button class="btn btn-primary-gradient mt-3" onclick="$('#searchJob').val(''); $('#searchJobsBtn').click();">
                                        <i class="fas fa-undo me-2"></i> Clear Filters
                                    </button>
                                </div>
                            `;
                        } else {
                            jobs.forEach(job => {
                                html += createJobCard(job, true);
                            });
                        }
                        $('#jobsList').html(html);
                        
                        if (response.jobs.links) {
                            let paginationHtml = '<ul class="pagination">';
                            response.jobs.links.forEach(link => {
                                const active = link.active ? 'active' : '';
                                const disabled = link.url === null ? 'disabled' : '';
                                const url = link.url ? `onclick="loadJobsPage('${link.url}')"` : '';
                                paginationHtml += `
                                    <li class="page-item ${active} ${disabled}">
                                        <a class="page-link" href="#" ${url}>
                                            ${link.label.replace('&laquo;', '«').replace('&raquo;', '»')}
                                        </a>
                                    </li>
                                `;
                            });
                            paginationHtml += '</ul>';
                            $('#paginationLinks').html(paginationHtml);
                        }
                    }
                })
                .fail(function() {
                    $('#jobsList').html('<div class="alert alert-danger">Error loading jobs.</div>');
                });
        }

        window.loadJobsPage = function(url) {
            if (url) {
                const page = new URL(url).searchParams.get('page');
                loadJobs(page);
            }
        };

        // ============================================
        // LOAD MY APPLICATIONS
        // ============================================
        function loadMyApplications() {
            window.jobseekerApi.getApplications()
                .done(function(response) {
                    if (response.success) {
                        const applications = response.applications || [];
                        let html = '';
                        if (applications.length === 0) {
                            html = `
                                <div class="empty-state">
                                    <i class="fas fa-file-alt"></i>
                                    <h5>No Applications Yet</h5>
                                    <p>Start applying for jobs to see your applications here.</p>
                                    <button class="btn btn-primary-gradient mt-3" onclick="showBrowseJobs();">
                                        <i class="fas fa-search me-2"></i> Browse Jobs
                                    </button>
                                </div>
                            `;
                        } else {
                            applications.forEach(app => {
                                const statusClass = `status-${app.status}`;
                                const statusIcons = {
                                    submitted: 'fa-clock',
                                    reviewed: 'fa-check-circle',
                                    shortlisted: 'fa-star',
                                    rejected: 'fa-times-circle',
                                    hired: 'fa-check-double'
                                };
                                html += `
                                    <div class="job-card application-card ${statusClass} card">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h5 class="card-title">${app.job_title}</h5>
                                                    <h6 class="card-subtitle mb-2 text-muted">
                                                        <i class="fas fa-building me-1"></i> ${app.company_name}
                                                    </h6>
                                                    <div class="job-meta">
                                                        <span><i class="fas fa-map-marker-alt"></i> ${app.location}</span>
                                                        <span><i class="fas fa-calendar-alt"></i> Applied: ${new Date(app.created_at).toLocaleDateString()}</span>
                                                    </div>
                                                    ${app.cover_letter ? `
                                                        <div class="mt-2">
                                                            <small class="text-muted"><strong>Cover Letter:</strong></small>
                                                            <p class="text-muted small mt-1">${app.cover_letter.substring(0, 200)}${app.cover_letter.length > 200 ? '...' : ''}</p>
                                                        </div>
                                                    ` : ''}
                                                </div>
                                                <div class="text-end">
                                                    <span class="status-badge ${statusClass}">
                                                        <i class="fas ${statusIcons[app.status] || 'fa-circle'}"></i>
                                                        ${app.status.charAt(0).toUpperCase() + app.status.slice(1)}
                                                    </span>
                                                    ${app.status === 'submitted' || app.status === 'pending' ? `
                                                        <br>
                                                        <button class="btn btn-action withdraw mt-2" onclick="withdrawApplication(${app.id})">
                                                            <i class="fas fa-times"></i> Withdraw
                                                        </button>
                                                    ` : ''}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });
                        }
                        $('#applicationsList').html(html);
                    }
                })
                .fail(function() {
                    $('#applicationsList').html('<div class="alert alert-danger">Error loading applications.</div>');
                });
        }

        // ============================================
        // CREATE JOB CARD
        // ============================================
        function createJobCard(job, showApply) {
            showApply = showApply || false;
            const date = new Date(job.created_at).toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric' 
            });

            let applyButton = '';
            if (showApply) {
                applyButton = `
                    <button class="btn btn-success-gradient btn-sm" onclick="openApplyModal(${job.id})">
                        <i class="fas fa-paper-plane me-1"></i> Apply Now
                    </button>
                `;
            }

            const attachmentButton = job.has_attachment && job.attachment_url
                ? `<a class="btn btn-action view" href="${job.attachment_url}" target="_blank" rel="noopener"><i class="fas fa-paperclip"></i> Attachment</a>`
                : '';

            return `
                <div class="job-card animate-fade-in">
                    <div class="card-header-custom">
                        <div>
                            <h5 class="job-title">${job.title}</h5>
                            <span class="job-company"><i class="fas fa-building me-1"></i> ${job.company_name}</span>
                        </div>
                        <span class="status-badge active">
                            <i class="fas fa-check-circle"></i> Active
                        </span>
                    </div>
                    <div class="card-body-custom">
                        <div class="job-meta">
                            <span><i class="fas fa-map-marker-alt"></i> ${job.location}</span>
                            <span><i class="fas fa-clock"></i> ${job.employment_type}</span>
                            <span><i class="fas fa-calendar-alt"></i> Posted: ${date}</span>
                        </div>
                        <div class="job-description">
                            ${job.description ? job.description.substring(0, 200) + (job.description.length > 200 ? '...' : '') : ''}
                        </div>
                        <div class="job-footer">
                            <div>
                                <i class="fas fa-building text-muted me-1"></i>
                                <span class="text-muted" style="font-size: 0.9rem;">
                                    ${job.employer ? job.employer.name : 'Company'}
                                </span>
                            </div>
                            <div class="job-actions">
                                <button class="btn-action view" onclick="viewJob(${job.id})">
                                    <i class="fas fa-eye"></i> Details
                                </button>
                                ${attachmentButton}
                                ${applyButton}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        function escapeHtml(value) {
            return String(value || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function renderAttachmentPreview(job) {
            if (!job || !job.has_attachment || !job.attachment_url) {
                return '<p class="text-muted mb-0">No attachment uploaded.</p>';
            }

            const name = escapeHtml(job.attachment_name || 'Attachment');
            const url = escapeHtml(job.attachment_url);
            const mime = (job.attachment_mime || '').toLowerCase();
            const lowerName = String(job.attachment_name || '').toLowerCase();
            const isImage = mime.startsWith('image/') || /\.(png|jpe?g|gif|webp)$/.test(lowerName);
            const isPdf = mime === 'application/pdf' || lowerName.endsWith('.pdf');

            if (isImage) {
                return `
                    <div class="mt-2">
                        <a href="${url}" target="_blank" rel="noopener" class="d-inline-block mb-2">${name}</a>
                        <img src="${url}" alt="Attachment preview" class="img-fluid rounded border" style="max-height: 320px;">
                    </div>
                `;
            }

            if (isPdf) {
                return `
                    <div class="mt-2">
                        <a href="${url}" target="_blank" rel="noopener" class="d-inline-block mb-2">${name}</a>
                        <iframe src="${url}" style="width: 100%; height: 420px; border: 1px solid #e2e8f0; border-radius: 8px;"></iframe>
                    </div>
                `;
            }

            return `<a href="${url}" target="_blank" rel="noopener">${name}</a>`;
        }

        // ============================================
        // OPEN APPLY MODAL
        // ============================================
        window.openApplyModal = function(jobId) {
            window.jobseekerApi.getProfileStatus()
                .done(function(response) {
                    if (response.success && response.profile_completed) {
                        $('#applyJobId').val(jobId);
                        $('#cover_letter').val('');
                        $('#cover_letter').removeClass('is-invalid');
                        $('#cover_letterError').text('');
                        $('#fileInfo').hide();
                        $('#applyLoading').hide();
                        $('#submitApplication').prop('disabled', false);
                        $('#submitApplication').html('<i class="fas fa-paper-plane me-2"></i> Submit Application');
                        $('#applyJobModal').modal('show');
                    } else {
                        showToast('warning', 'Please complete your profile before applying for jobs.');
                        showProfile();
                    }
                })
                .fail(function() {
                    if (profileCompletedState) {
                        $('#applyJobId').val(jobId);
                        $('#cover_letter').val('');
                        $('#cover_letter').removeClass('is-invalid');
                        $('#cover_letterError').text('');
                        $('#fileInfo').hide();
                        $('#applyLoading').hide();
                        $('#submitApplication').prop('disabled', false);
                        $('#submitApplication').html('<i class="fas fa-paper-plane me-2"></i> Submit Application');
                        $('#applyJobModal').modal('show');
                    } else {
                        showToast('warning', 'Please complete your profile before applying for jobs.');
                        showProfile();
                    }
                });
        };

        // ============================================
        // SUBMIT APPLICATION
        // ============================================
        function submitApplication() {
            const jobId = $('#applyJobId').val();
            const fileInput = $('#cover_letter')[0];
            const file = fileInput.files[0];

            if (!file) {
                $('#cover_letter').addClass('is-invalid');
                $('#cover_letterError').text('Please upload a cover letter PDF.');
                showToast('error', 'Please upload a cover letter PDF.');
                return;
            }

            if (file.type !== 'application/pdf') {
                $('#cover_letter').addClass('is-invalid');
                $('#cover_letterError').text('Please upload a PDF file.');
                showToast('error', 'Cover letter must be a PDF file.');
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                $('#cover_letter').addClass('is-invalid');
                $('#cover_letterError').text('File size must be less than 2MB.');
                showToast('error', 'Cover letter file size must be less than 2MB.');
                return;
            }

            const formData = new FormData();
            formData.append('job_id', jobId);
            formData.append('cover_letter', file);

            $('#submitApplication').prop('disabled', true);
            $('#submitApplication').html('<span class="spinner-border spinner-border-sm me-2"></span> Submitting...');
            $('#applyLoading').show();

            window.jobseekerApi.applyForJob(jobId, formData)
                .done(function(response) {
                    $('#submitApplication').prop('disabled', false);
                    $('#submitApplication').html('<i class="fas fa-paper-plane me-2"></i> Submit Application');
                    $('#applyLoading').hide();
                    
                    if (response.success) {
                        showToast('success', response.message);
                        $('#applyJobModal').modal('hide');
                        $('#cover_letter').val('');
                        $('#fileInfo').hide();
                        $('#cover_letter').removeClass('is-invalid');
                        $('#cover_letterError').text('');
                        loadDashboardStats();
                        if ($('#browseJobsContent').is(':visible')) {
                            loadJobs();
                        }
                        if ($('#myApplicationsContent').is(':visible')) {
                            loadMyApplications();
                        }
                    }
                })
                .fail(function(xhr) {
                    $('#submitApplication').prop('disabled', false);
                    $('#submitApplication').html('<i class="fas fa-paper-plane me-2"></i> Submit Application');
                    $('#applyLoading').hide();
                    
                    if (xhr.status === 403) {
                        showToast('warning', 'Please complete your profile first.');
                        showProfile();
                    } else if (xhr.status === 400) {
                        showToast('error', xhr.responseJSON ? xhr.responseJSON.message : 'Already applied.');
                        $('#applyJobModal').modal('hide');
                    } else {
                        showToast('error', 'An error occurred. Please try again.');
                    }
                });
        }

        // ============================================
        // WITHDRAW APPLICATION
        // ============================================
        window.withdrawApplication = function(applicationId) {
            if (confirm('Are you sure you want to withdraw this application? This action cannot be undone.')) {
                window.jobseekerApi.withdrawApplication(applicationId)
                    .done(function(response) {
                        if (response.success) {
                            showToast('success', response.message);
                            loadMyApplications();
                            loadDashboardStats();
                        }
                    })
                    .fail(function() {
                        showToast('error', 'Error withdrawing application.');
                    });
            }
        };

        // ============================================
        // VIEW JOB
        // ============================================
        window.viewJob = function(id) {
            window.jobseekerApi.getJobDetails(id)
                .done(function(response) {
                    if (response.success) {
                        const job = response.job;
                        const hasApplied = response.hasApplied || false;
                        const modalContent = `
                            <div class="p-2">
                                <h4 class="mb-3">${job.title}</h4>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <p><strong><i class="fas fa-building me-2"></i>Company:</strong> ${job.company_name}</p>
                                        <p><strong><i class="fas fa-map-marker-alt me-2"></i>Location:</strong> ${job.location}</p>
                                        <p><strong><i class="fas fa-clock me-2"></i>Type:</strong> ${job.employment_type}</p>
                                        <p><strong><i class="fas fa-user me-2"></i>Employer:</strong> ${job.employer.name}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong><i class="fas fa-calendar-alt me-2"></i>Posted:</strong> ${new Date(job.created_at).toLocaleDateString()}</p>
                                        <p><strong><i class="fas fa-hourglass-end me-2"></i>Deadline:</strong> ${job.application_deadline ? new Date(job.application_deadline).toLocaleDateString() : 'No deadline'}</p>
                                        <p><strong><i class="fas fa-paperclip me-2"></i>Attachment:</strong> ${job.has_attachment ? `<a href="${job.attachment_url}" target="_blank" rel="noopener">${job.attachment_name || 'Download attachment'}</a>` : 'None'}</p>
                                    </div>
                                </div>
                                <hr>
                                <h5><i class="fas fa-align-left me-2"></i>Description</h5>
                                <p>${job.description}</p>
                                <h5><i class="fas fa-list-check me-2"></i>Requirements</h5>
                                <p>${job.requirements}</p>
                                <h5><i class="fas fa-file me-2"></i>Attachment Preview</h5>
                                ${renderAttachmentPreview(job)}
                                ${job.benefits ? `<h5><i class="fas fa-gift me-2"></i>Benefits</h5><p>${job.benefits}</p>` : ''}
                                <div class="mt-3">
                                    ${hasApplied ? `
                                        <div class="alert alert-info">
                                            <i class="fas fa-check-circle me-2"></i> 
                                            You have already applied for this position.
                                        </div>
                                    ` : `
                                        <button class="btn btn-success-gradient" onclick="openApplyModal(${job.id}); $('#viewJobModal').modal('hide');">
                                            <i class="fas fa-paper-plane me-2"></i> Apply Now
                                        </button>
                                    `}
                                </div>
                            </div>
                        `;
                        $('#viewJobContent').html(modalContent);
                        $('#viewJobModal').modal('show');
                    }
                })
                .fail(function() {
                    showToast('error', 'Error loading job details.');
                });
        };

        // ============================================
        // PROFILE PREVIEW
        // ============================================
        window.showProfilePreview = function() {
            if (!profileCompletedState) {
                showToast('warning', 'Complete your profile first to view full profile preview.');
                return;
            }

            // Load education data
            window.jobseekerApi.getEducation()
                .done(function(response) {
                    if (response.success) {
                        const educations = response.education || [];
                        renderProfilePreview(educations);
                    } else {
                        renderProfilePreview([]);
                    }
                })
                .fail(function() {
                    renderProfilePreview([]);
                });
        };

        function renderProfilePreview(educations) {
            const user = {
                name: $('#name').val() || userName,
                email: $('#email').val() || userEmail,
                phone: $('#phone').val() || '',
                gender: $('#gender').val() || '',
                date_of_birth: $('#date_of_birth').val() || '',
                disability: $('#disability').val() || '',
                has_passport: $('#has_passport').val() || '',
                passport_number: $('#passport_number').val() || '',
                passport_expiry_date: $('#passport_expiry_date').val() || '',
                place_of_issue: $('#place_of_issue').val() || '',
                region: $('#region option:selected').text() || '',
                district: $('#district option:selected').text() || '',
                ward: $('#ward option:selected').text() || '',
                shehia: $('#shehia option:selected').text() || ''
            };
            const previewImageUrl = getPreviewProfileImageUrl();

            // Get skills
            const skills = [];
            $('#selectedSkills .skill-tag').each(function() {
                const text = safeTrim(($(this).attr('data-skill') || $(this).clone().children().remove().end().text()) || '');
                if (text) {
                    skills.push(text);
                }
            });

            let html = `
                <div class="profile-preview">
                    <div class="preview-header">
                        <h4><i class="fas fa-id-card me-2"></i> Profile Preview</h4>
                        <div class="preview-actions">
                            <button class="btn btn-sm btn-success-gradient" onclick="downloadProfile()">
                                <i class="fas fa-file-pdf me-1"></i> Download PDF
                            </button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="mt-3">
                                        <div class="preview-label">Profile Photo</div>
                                        ${previewImageUrl ? `
                                            <a href="${previewImageUrl}" target="_blank" class="d-inline-block mt-1 preview-profile-photo-link">
                                                <img src="${previewImageUrl}" alt="Profile photo" class="img-thumbnail preview-profile-photo" style="max-height: 180px; width: auto; object-fit: cover;">
                                            </a>
                                        ` : '<div class="text-muted small mt-1">No profile photo uploaded yet.</div>'}
                                    </div>
                            <div class="status-badge-preview ${educations.length > 0 && skills.length > 0 ? 'completed' : 'pending'}">
                                ${educations.length > 0 && skills.length > 0 ? '✅ Profile Complete' : '⏳ Pending'}
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="preview-section">
                                        <div class="preview-label">Full Name</div>
                                        <div class="preview-value"><strong>${user.name}</strong></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="preview-section">
                                        <div class="preview-label">Email</div>
                                        <div class="preview-value">${user.email}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="preview-section">
                                        <div class="preview-label">Phone</div>
                                        <div class="preview-value">${user.phone || 'Not provided'}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="preview-section">
                                        <div class="preview-label">Gender</div>
                                        <div class="preview-value">${user.gender || 'Not provided'}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="preview-section">
                                        <div class="preview-label">Date of Birth</div>
                                        <div class="preview-value">${user.date_of_birth ? new Date(user.date_of_birth).toLocaleDateString() : 'Not provided'}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="preview-section">
                                        <div class="preview-label">Disability Status</div>
                                        <div class="preview-value">${user.disability || 'Not provided'}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="preview-section">
                                        <div class="preview-label">Passport</div>
                                        <div class="preview-value">${user.has_passport === 'yes' ? 'Yes' : user.has_passport === 'no' ? 'No' : 'Not provided'}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="preview-section">
                                        <div class="preview-label">Place of Issue</div>
                                        <div class="preview-value">${user.place_of_issue === 'tanzania_mainland' ? 'Tanzania Mainland' : user.place_of_issue === 'zanzibar' ? 'Zanzibar' : 'Not provided'}</div>
                                    </div>
                                </div>
                            </div>
                            ${user.has_passport === 'yes' ? `
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="preview-section">
                                            <div class="preview-label">Passport Number</div>
                                            <div class="preview-value">${user.passport_number || 'Not provided'}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="preview-section">
                                            <div class="preview-label">Passport Expiry Date</div>
                                            <div class="preview-value">${user.passport_expiry_date ? new Date(user.passport_expiry_date).toLocaleDateString() : 'Not provided'}</div>
                                        </div>
                                    </div>
                                </div>
                            ` : ''}
                        </div>
                    </div>

                    <div class="preview-divider"></div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="preview-section">
                                <div class="preview-label">Location</div>
                                <div class="preview-value">
                                    ${user.region ? user.region : 'Not provided'}
                                    ${user.district ? ' → ' + user.district : ''}
                                    ${user.ward ? ' → ' + user.ward : ''}
                                    ${user.shehia ? ' → ' + user.shehia : ''}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="preview-divider"></div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="preview-section">
                                <div class="preview-label">Education</div>
                                ${educations.length > 0 ? `
                                    ${educations.map(edu => `
                                        <div class="education-item">
                                            <div class="edu-title">
                                                ${edu.type.charAt(0).toUpperCase() + edu.type.slice(1)}
                                                ${edu.type === 'informal' ? ' - ' + (edu.description || '') : ''}
                                                ${edu.type === 'primary' || edu.type === 'secondary' ? ' - ' + (edu.school_name || '') : ''}
                                                ${edu.type === 'university' ? ' - ' + (edu.institution || '') + ' (' + (edu.course || '') + ')' : ''}
                                            </div>
                                            <div class="edu-detail">
                                                ${edu.start_date ? new Date(edu.start_date).toLocaleDateString() : ''}
                                                ${edu.end_date ? ' - ' + new Date(edu.end_date).toLocaleDateString() : ' - Present'}
                                            </div>
                                        </div>
                                    `).join('')}
                                ` : `
                                    <div class="text-muted">No education added yet</div>
                                `}
                            </div>
                        </div>
                    </div>

                    <div class="preview-divider"></div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="preview-section">
                                <div class="preview-label">Skills</div>
                                <div>
                                    ${skills.length > 0 ? `
                                        ${skills.map(skill => `<span class="skill-tag">${skill}</span>`).join('')}
                                    ` : `
                                        <span class="text-muted">No skills added yet</span>
                                    `}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="preview-divider"></div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="preview-section">
                                <div class="preview-label">Profile Status</div>
                                <div>
                                    <span class="badge bg-${educations.length > 0 && skills.length > 0 ? 'success' : 'warning'}">
                                        ${educations.length > 0 && skills.length > 0 ? '✅ Complete' : '⏳ Incomplete'}
                                    </span>
                                    <span class="text-muted ms-2">
                                        ${educations.length} education(s), ${skills.length} skill(s)
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="preview-divider"></div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="preview-section">
                                <div class="preview-label">Documents</div>
                                <div class="d-flex flex-wrap gap-2 align-items-center">
                                    ${savedResumeUrl ? `
                                        <a class="btn btn-sm btn-outline-gradient" href="${savedResumeUrl}" target="_blank">
                                            <i class="fas fa-file-pdf me-1"></i> View CV (${savedResumeName || 'File'})
                                        </a>
                                    ` : `<span class="text-muted">No CV uploaded</span>`}
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            `;

            $('#profilePreviewContent').html(html);
            $('#profilePreviewModal').modal('show');
        }

        // ============================================
        // DOWNLOAD PROFILE AS PDF
        // ============================================
        window.downloadProfile = function() {
            const content = $('#profilePreviewContent').html();
            const user = {
                name: $('#name').val() || userName,
                email: $('#email').val() || userEmail
            };

            const printWindow = window.open('', '_blank', 'width=900,height=1200');
            if (!printWindow) {
                showToast('warning', 'Please allow popups to generate PDF.');
                return;
            }

            const printable = `
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <title>Profile - ${user.name}</title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                    <style>
                        @page { size: A4; margin: 12mm; }
                        body { padding: 0; background: #fff; font-family: 'Inter', sans-serif; color: #0f172a; font-size: 12px; }
                        .profile-preview { max-width: 100%; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px; }
                        .preview-actions { display: none !important; }
                        .preview-label { font-size: 10px; text-transform: uppercase; color: #64748b; margin-bottom: 2px; }
                        .preview-value { font-size: 12px; }
                        .preview-divider { height: 1px; background: #e2e8f0; margin: 10px 0; }
                        .education-item { border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 10px; margin-bottom: 6px; }
                        .edu-title { font-size: 12px; font-weight: 600; }
                        .edu-detail { font-size: 11px; color: #64748b; }
                        .skill-tag { display: inline-block; margin: 2px; border-radius: 999px; background: #eef2ff; color: #3730a3; border: 1px solid #c7d2fe; padding: 2px 8px; font-size: 10px; }
                        .generated-at { margin-top: 10px; color: #64748b; font-size: 10px; text-align: right; }
                        img { max-width: 100%; height: auto; }
                    </style>
                </head>
                <body>
                    ${content}
                    <div class="generated-at">Generated on ${new Date().toLocaleString()}</div>
                    <script>
                        window.onload = function() {
                            window.focus();
                            window.print();
                        }
                    <\/script>
                </body>
                </html>
            `;

            printWindow.document.open();
            printWindow.document.write(printable);
            printWindow.document.close();
        };

        $('#has_passport').on('change', togglePassportFields);
        togglePassportFields();

        function toggleDisabilityDescription() {
            if ($('#disability').val() === 'yes') {
                $('#disabilityDescriptionRow').stop(true, true).slideDown(150);
            } else {
                $('#disabilityDescriptionRow').stop(true, true).slideUp(150);
                $('#disability_description').val('');
            }
        }
        $('#disability').on('change', toggleDisabilityDescription);
        toggleDisabilityDescription();

       
        // ============================================
        // COMPLETE PROFILE
        // ============================================
        function completeProfile() {
            const formData = new FormData($('#profileForm')[0]);
            const skills = fieldValue('#skillsInput');
            formData.set('skills', skills);
            
            const region = $('#region').val();
            const district = $('#district').val();
            const ward = $('#ward').val();
            const shehia = $('#shehia').val();

            if (!validateStep1()) {
                goToStep(1);
                return;
            }
            
            if (!region) {
                showToast('error', 'Please select a Region.');
                $('#region').addClass('is-invalid');
                $('#regionError').text('Region is required.');
                goToStep(1);
                return;
            }
            
            if (!district) {
                showToast('error', 'Please select a District.');
                $('#district').addClass('is-invalid');
                $('#districtError').text('District is required.');
                goToStep(1);
                return;
            }
            
            if (!ward) {
                showToast('error', 'Please select a Ward.');
                $('#ward').addClass('is-invalid');
                $('#wardError').text('Ward is required.');
                goToStep(1);
                return;
            }
            
            if (!shehia) {
                showToast('error', 'Please select a Shehia.');
                $('#shehia').addClass('is-invalid');
                $('#shehiaError').text('Shehia is required.');
                goToStep(1);
                return;
            }
            
            if (!skills) {
                showToast('error', 'Please add at least one skill.');
                $('#skillsInput').addClass('is-invalid');
                $('#skillsInputError').text('Please add at least one skill.');
                return;
            }
            
            const educationCount = $('#educationList .entry-card').length;
            if (educationCount === 0) {
                if (!confirm('You haven\'t added any education. You can add it later. Continue?')) {
                    goToStep(2);
                    return;
                }
            }
            
            $('#submitProfileBtn').prop('disabled', true);
            $('#submitProfileBtn').html('<span class="spinner-border spinner-border-sm me-2"></span> Saving...');

            saveSkills(function(skillsSaved) {
                if (!skillsSaved) {
                    $('#submitProfileBtn').prop('disabled', false);
                    $('#submitProfileBtn').html('<i class="fas fa-check me-2"></i> Complete Profile');
                    return;
                }

                window.jobseekerApi.completeProfile(formData)
                    .done(function(response) {
                        $('#submitProfileBtn').prop('disabled', false);
                        $('#submitProfileBtn').html('<i class="fas fa-check me-2"></i> Complete Profile');

                        if (response.success) {
                            showToast('success', response.message || 'Profile completed successfully!');
                            $('#profileLink .badge').removeClass('bg-warning').addClass('bg-success');
                            $('#profileLink .badge i').removeClass('fa-exclamation-triangle').addClass('fa-check');
                            profileCompletedState = true;
                            togglePreviewAccess();

                            setTimeout(function() {
                                showProfilePreview();
                            }, 600);
                        }
                    })
                    .fail(function(xhr) {
                        $('#submitProfileBtn').prop('disabled', false);
                        $('#submitProfileBtn').html('<i class="fas fa-check me-2"></i> Complete Profile');
                        console.error('completeProfile AJAX error:', xhr.status, xhr.responseText);

                        if (xhr.status === 422) {
                            if (xhr.responseJSON?.errors) {
                                const errors = xhr.responseJSON.errors;
                                Object.keys(errors).forEach(function(key) {
                                    const field = $('#' + key);
                                    if (field.length) {
                                        field.addClass('is-invalid');
                                        $('#' + key + 'Error').text(errors[key][0]);
                                    }
                                });
                            }
                            showToast('error', xhr.responseJSON?.message || 'Please fix the validation errors.');
                        } else {
                            const errDetail = xhr.responseJSON?.message ? `: ${xhr.responseJSON.message}` : '';
                            showToast('error', `Error completing profile${errDetail}. Please check browser console.`);
                        }
                    });
            }, true);
        }

      
       
       

    

       

       

     

        // ============================================
        // MULTI-STEP NAVIGATION
        // ============================================
        let currentStep = 1;
        let totalSteps = 3;

        function updateProgress() {
            var progress = (currentStep / totalSteps) * 100;
            $('#progressFill').css('width', progress + '%');
            $('.step').each(function() {
                var step = parseInt($(this).data('step'));
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

        window.nextStep = function(step) {
            if (step === 2) {
                if (!validateStep1()) {
                    showToast('error', 'Please fill in all required fields in General Information.');
                    return;
                }
                saveStep1(function(success) {
                    if (success) {
                        goToStep(2);
                    }
                });
                return;
            }

            if (step === 3) {
                var educations = $('#educationList .entry-card').length;
                if (educations === 0) {
                    if (!confirm('You haven\'t added any education yet. You can add it later. Continue?')) {
                        return;
                    }
                }
                goToStep(3);
                return;
            }

            goToStep(step);
        };

        window.prevStep = function(step) {
            goToStep(step);
        };

        function clearValidation(prefix) {
            $('[id^="' + prefix + '"]').removeClass('is-invalid');
            $('[id$="Error"]').text('');
        }

        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            var date = new Date(dateString);
            return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short' });
        }

        // ============================================
        // NAVIGATION FUNCTIONS
        // ============================================
        function showDashboard() {
            $('#dashboardContent').show();
            $('#browseJobsContent').hide();
            $('#myApplicationsContent').hide();
            $('#profileContent').hide();
            $('.nav-link').removeClass('active');
            $('#dashboardLink').addClass('active');
            loadDashboardStats();
            loadRecentJobs();
        }

        function showBrowseJobs() {
            $('#dashboardContent').hide();
            $('#browseJobsContent').show();
            $('#myApplicationsContent').hide();
            $('#profileContent').hide();
            $('.nav-link').removeClass('active');
            $('#browseJobsLink').addClass('active');
            loadJobs();
        }

        function showMyApplications() {
            $('#dashboardContent').hide();
            $('#browseJobsContent').hide();
            $('#myApplicationsContent').show();
            $('#profileContent').hide();
            $('.nav-link').removeClass('active');
            $('#myApplicationsLink').addClass('active');
            loadMyApplications();
        }

        function showProfile() {
            $('#dashboardContent').hide();
            $('#browseJobsContent').hide();
            $('#myApplicationsContent').hide();
            $('#profileContent').show();
            $('.nav-link').removeClass('active');
            $('#profileLink').addClass('active');
            loadEducation();
        }

        // ============================================
        // TOAST NOTIFICATION
        // ============================================
        function showToast(type, message) {
            var colors = {
                success: 'bg-success text-white',
                error: 'bg-danger text-white',
                info: 'bg-info text-white',
                warning: 'bg-warning text-dark'
            };

            var icons = {
                success: 'fa-check-circle',
                error: 'fa-exclamation-circle',
                info: 'fa-info-circle',
                warning: 'fa-exclamation-triangle'
            };

            var toastHtml = `
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

            var container = $('.toast-container');
            var toast = $(toastHtml);
            container.append(toast);

            setTimeout(function() {
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

        // Expose helpers for split modules loaded via app.js.
        window.initSkillsDropdown = initSkillsDropdown;
        window.validateStep1 = validateStep1;
        window.saveStep1 = saveStep1;
        window.clearValidation = clearValidation;
        window.formatDate = formatDate;
        window.showToast = showToast;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
    
