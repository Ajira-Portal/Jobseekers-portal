import './api.js';

$(document).ready(function() {
            const bodyData = document.body.dataset;
            const isLoggedIn = bodyData.isAuthenticated === '1';
            const loginUrl = bodyData.loginUrl || '/login';
            const dashboardUrl = bodyData.jobseekerDashboardUrl || '/jobseeker/dashboard';

            // Load jobs
            loadJobs();

            // Open graduate registration modal from desktop and mobile links
            $('#graduatesNavLink, #graduatesNavLinkMobile').on('click', function(e) {
                e.preventDefault();
                $('#graduateRegisterModal').modal('show');
            });

            // Submit graduate registration from welcome page
            $('#submitGraduateRegisterBtn').on('click', function() {
                submitGraduateRegistration();
            });

            // Section B education type toggle
            $('#gradEducationPath').on('change', function() {
                updateEducationSection($(this).val());
            });

            initGraduateLocationDropdowns();

            // Search form submission
            $('#searchForm').on('submit', function(e) {
                e.preventDefault();
                loadJobs();
            });

            // Navbar scroll effect
            $(window).scroll(function() {
                if ($(this).scrollTop() > 50) {
                    $('#navbar').addClass('scrolled');
                } else {
                    $('#navbar').removeClass('scrolled');
                }
            });

            // Load jobs
            function loadJobs(page = 1) {
                const keyword = $('#searchKeyword').val();
                const location = $('#searchLocation').val();
                const type = $('#searchType').val();

                $('#loadingSpinner').show();
                $('#jobsList').html('');

                window.welcomeApi.getJobs({
                        search: keyword,
                        location: location,
                        employment_type: type,
                        page: page,
                        per_page: 9
                    })
                    .done(function(response) {
                        $('#loadingSpinner').hide();
                        
                        if (response.success && response.jobs && response.jobs.data) {
                            const jobs = response.jobs.data;
                            let html = '';
                            
                            if (jobs.length === 0) {
                                html = `
                                    <div class="text-center py-5">
                                        <i class="fas fa-search" style="font-size: 4rem; color: #CBD5E1;"></i>
                                        <h4 class="mt-3">No Jobs Found</h4>
                                        <p class="text-muted">Try adjusting your search filters</p>
                                    </div>
                                `;
                            } else {
                                jobs.forEach(job => {
                                    const date = new Date(job.created_at).toLocaleDateString('en-US', {
                                        year: 'numeric',
                                        month: 'short',
                                        day: 'numeric'
                                    });
                                    
                                    let applyButton = '';
                                    
                                    if (isLoggedIn) {
                                        applyButton = `
                                            <button class="btn-apply" onclick="redirectToApply(${job.id})">
                                                <i class="fas fa-paper-plane me-1"></i> Apply Now
                                            </button>
                                        `;
                                    } else {
                                        applyButton = `
                                            <a href="${loginUrl}" class="btn-apply outline">
                                                <i class="fas fa-sign-in-alt me-1"></i> Login to Apply
                                            </a>
                                        `;
                                    }
                                    
                                    html += `
                                        <div class="job-card animate-fade-in">
                                            <div class="job-header">
                                                <div>
                                                    <h5 class="job-title">${job.title}</h5>
                                                    <span class="job-company"><i class="fas fa-building me-1"></i> ${job.company_name}</span>
                                                </div>
                                                <span class="badge bg-success">Active</span>
                                            </div>
                                            <div class="job-meta">
                                                <span><i class="fas fa-map-marker-alt"></i> ${job.location}</span>
                                                <span><i class="fas fa-clock"></i> ${job.employment_type}</span>
                                                ${job.salary_range ? `<span><i class="fas fa-dollar-sign"></i> ${job.salary_range}</span>` : ''}
                                                <span><i class="fas fa-calendar-alt"></i> Posted: ${date}</span>
                                            </div>
                                            <div class="job-description">
                                                ${job.description ? job.description.substring(0, 200) + (job.description.length > 200 ? '...' : '') : ''}
                                            </div>
                                            <div class="job-footer">
                                                <div class="job-tags">
                                                    <span class="tag active"><i class="fas fa-check-circle"></i> Active</span>
                                                    ${job.employment_type ? `<span class="tag">${job.employment_type}</span>` : ''}
                                                    ${job.location ? `<span class="tag">${job.location}</span>` : ''}
                                                </div>
                                                ${applyButton}
                                            </div>
                                        </div>
                                    `;
                                });
                            }
                            
                            $('#jobsList').html(html);
                            
                            // Pagination
                            if (response.jobs.links) {
                                let paginationHtml = '<nav><ul class="pagination">';
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
                                paginationHtml += '</ul></nav>';
                                $('#paginationLinks').html(paginationHtml);
                            }
                        } else {
                            $('#jobsList').html('<div class="alert alert-danger">Error loading jobs. Please try again.</div>');
                        }
                    })
                    .fail(function() {
                        $('#loadingSpinner').hide();
                        $('#jobsList').html('<div class="alert alert-danger">Error loading jobs. Please try again.</div>');
                    });
            }

            window.loadJobsPage = function(url) {
                if (url) {
                    const page = new URL(url).searchParams.get('page');
                    loadJobs(page);
                }
            };

            // Redirect to apply (for logged in users)
            window.redirectToApply = function(jobId) {
                window.location.href = dashboardUrl;
            };

            function updateEducationSection(type) {
                const sections = {
                    university: $('#universityFields'),
                    vta: $('#vtaFields'),
                    apprenticeship: $('#apprenticeshipFields')
                };

                Object.values(sections).forEach($section => {
                    $section.hide();
                    $section.find('input, select').prop('required', false).removeClass('is-invalid is-valid').val('');
                });

                if (sections[type]) {
                    sections[type].show();
                    sections[type].find('input, select').prop('required', true);
                }
            }

            function initGraduateLocationDropdowns() {
                loadGradRegions();

                $('#gradRegion').on('change', function() {
                    const regionId = $(this).val();
                    $('#gradDistrict').html('<option value="">Select District</option>').prop('disabled', true);
                    $('#gradWard').html('<option value="">Select Ward</option>').prop('disabled', true);
                    $('#gradShehia').html('<option value="">Select Shehia</option>').prop('disabled', true);

                    if (regionId) {
                        loadGradDistricts(regionId);
                    }
                });

                $('#gradDistrict').on('change', function() {
                    const districtId = $(this).val();
                    $('#gradWard').html('<option value="">Select Ward</option>').prop('disabled', true);
                    $('#gradShehia').html('<option value="">Select Shehia</option>').prop('disabled', true);

                    if (districtId) {
                        loadGradWards(districtId);
                    }
                });

                $('#gradWard').on('change', function() {
                    const wardId = $(this).val();
                    $('#gradShehia').html('<option value="">Select Shehia</option>').prop('disabled', true);

                    if (wardId) {
                        loadGradShehias(wardId);
                    }
                });
            }

            function loadGradRegions() {
                window.welcomeApi.getRegions().done(function(response) {
                    if (!response.success) return;
                    let options = '<option value="">Select Region</option>';
                    response.data.forEach(function(region) {
                        options += `<option value="${region.id}">${region.name}</option>`;
                    });
                    $('#gradRegion').html(options).prop('disabled', false);
                });
            }

            function loadGradDistricts(regionId) {
                window.welcomeApi.getDistricts(regionId).done(function(response) {
                    if (!response.success) return;
                    let options = '<option value="">Select District</option>';
                    response.data.forEach(function(district) {
                        options += `<option value="${district.id}">${district.name}</option>`;
                    });
                    $('#gradDistrict').html(options).prop('disabled', false);
                });
            }

            function loadGradWards(districtId) {
                window.welcomeApi.getWards(districtId).done(function(response) {
                    if (!response.success) return;
                    let options = '<option value="">Select Ward</option>';
                    response.data.forEach(function(ward) {
                        options += `<option value="${ward.id}">${ward.name}</option>`;
                    });
                    $('#gradWard').html(options).prop('disabled', false);
                });
            }

            function loadGradShehias(wardId) {
                window.welcomeApi.getShehias(wardId).done(function(response) {
                    if (!response.success) return;
                    let options = '<option value="">Select Shehia</option>';
                    response.data.forEach(function(shehia) {
                        options += `<option value="${shehia.id}">${shehia.name}</option>`;
                    });
                    $('#gradShehia').html(options).prop('disabled', false);
                });
            }

            // Keep section B fields hidden until a type is selected.
            updateEducationSection($('#gradEducationPath').val());

            function submitGraduateRegistration() {
                const $btn = $('#submitGraduateRegisterBtn');
                const form = $('#graduateRegisterForm')[0];
                const requiredFields = form.querySelectorAll('input[required], select[required], textarea[required]');
                let allFilled = true;

                $('#graduateRegisterForm .form-control, #graduateRegisterForm .form-check-input, #graduateRegisterForm .form-select').removeClass('is-invalid is-valid');

                requiredFields.forEach(field => {
                    if (field.type === 'radio') {
                        return;
                    }

                    if (!field.value.trim()) {
                        allFilled = false;
                        field.classList.add('is-invalid');
                    } else {
                        field.classList.add('is-valid');
                    }
                });

                if ($('input[name="gender"]:checked').length === 0) {
                    allFilled = false;
                }

                if (!allFilled) {
                    showToast('error', 'Please fill in all required fields.');
                    return;
                }

                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Submitting...');

                window.welcomeApi.registerGraduate($('#graduateRegisterForm').serialize())
                    .done(function(response) {
                        $btn.prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i> Submit Form');
                        if (response.success) {
                            $('#graduateRegisterModal').modal('hide');
                            form.reset();
                            updateEducationSection('');
                            $('#gradDistrict').html('<option value="">Select District</option>').prop('disabled', true);
                            $('#gradWard').html('<option value="">Select Ward</option>').prop('disabled', true);
                            $('#gradShehia').html('<option value="">Select Shehia</option>').prop('disabled', true);
                            $('#graduateRegisterForm .form-control, #graduateRegisterForm .form-check-input, #graduateRegisterForm .form-select').removeClass('is-valid is-invalid');
                            showToast('success', response.message || 'Form submitted successfully.');
                        } else {
                            showToast('error', response.message || 'Unable to submit form.');
                        }
                    })
                    .fail(function(xhr) {
                        $btn.prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i> Submit Form');

                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            const errors = xhr.responseJSON.errors;
                            const fieldMap = {
                                full_name: '#gradFullName',
                                gender: 'input[name="gender"]',
                                resident_number: '#gradResidentNumber',
                                place_of_birth: '#gradPlaceBirth',
                                date_of_birth: '#gradDob',
                                region: '#gradRegion',
                                region_id: '#gradRegion',
                                district_id: '#gradDistrict',
                                ward_id: '#gradWard',
                                shehia_id: '#gradShehia',
                                phone: '#gradPhone',
                                email: '#gradEmail',
                                next_of_kin_phone: '#gradNextOfKin',
                                education_type: '#gradEducationPath',
                                college_name: '#gradCollegeName',
                                university_course: '#gradUniversityCourse',
                                completed_year: '#gradCompletedYear',
                                gpa: '#gradGpa',
                                nta_level: '#gradNtaLevel',
                                vta_course: '#gradVtaCourse',
                                started_year: '#gradStartedYear',
                                ending_year: '#gradEndingYear',
                                apprenticeship_mode: '#gradApprenticeshipMode',
                                apprenticeship_specification: '#gradApprenticeshipSpec'
                            };

                            Object.keys(errors).forEach(function(fieldName) {
                                const selector = fieldMap[fieldName] || null;
                                const $field = selector ? $(selector) : $();
                                if ($field.length) {
                                    $field.addClass('is-invalid');
                                }
                            });
                            showToast('error', xhr.responseJSON.message || 'Please correct the highlighted fields.');
                            return;
                        }

                        showToast('error', xhr.responseJSON?.message || 'Error submitting graduate form.');
                    });
            }

            function showToast(type, message) {
                const colors = {
                    success: 'bg-success text-white',
                    error: 'bg-danger text-white'
                };

                const icons = {
                    success: 'fa-check-circle',
                    error: 'fa-exclamation-circle'
                };

                const toastHtml = `
                    <div class="toast align-items-center ${colors[type]} border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="fas ${icons[type]} me-2"></i>${message}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                `;

                const toast = $(toastHtml);
                $('.toast-container').append(toast);

                setTimeout(() => {
                    toast.fadeOut(300, function() {
                        $(this).remove();
                    });
                }, 4000);
            }
        });

// Mobile menu toggle
window.toggleMobileMenu = function() {
            const menu = document.getElementById('mobileMenu');
            if (menu.style.display === 'none') {
                menu.style.display = 'flex';
                menu.style.flexDirection = 'column';
            } else {
                menu.style.display = 'none';
            }
};
    
