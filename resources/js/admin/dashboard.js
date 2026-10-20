    import './api.js';

    $(document).ready(function() {
            const adminRoot = document.getElementById('pendingJobsLink') || document.getElementById('graduatesLink') || document.getElementById('usersLink');
            if (!adminRoot) {
                return;
            }

            let currentUsers = [];
            let usersPage = 1;
            let usersPerPage = 10;
            let currentGraduateCategory = '';

            function setGraduatesMenuExpanded(expanded) {
                const graduatesMenu = $('#graduatesMenu');
                if (graduatesMenu.length) {
                    graduatesMenu.stop(true, true);
                    if (expanded) {
                        graduatesMenu.addClass('show').css('display', 'block');
                    } else {
                        graduatesMenu.removeClass('show').css('display', 'none');
                    }
                }

                $('#graduatesLink').attr('aria-expanded', expanded ? 'true' : 'false');
            }

            // Initialize
            loadStats();
            loadRecentApplications();
            loadGraduateStats();

            // Navigation
            $('#dashboardLink').click(function(e) {
                e.preventDefault();
                showDashboard();
            });

            $('#pendingJobsLink').click(function(e) {
                e.preventDefault();
                showPendingJobs();
            });

            $('#allJobsLink').click(function(e) {
                e.preventDefault();
                showAllJobs();
            });

            $('#employersLink').click(function(e) {
                e.preventDefault();
                showEmployers();
            });

            $('#jobseekersLink').click(function(e) {
                e.preventDefault();
                showJobseekers();
            });

            $('#graduatesLink').click(function(e) {
                e.preventDefault();
                e.stopPropagation();
                showGraduates('');
            });

            $('#graduatesMenu [data-graduate-category]').click(function(e) {
                e.preventDefault();
                const category = $(this).data('graduate-category') || '';
                showGraduates(category);
            });

            $('#usersLink').click(function(e) {
                e.preventDefault();
                showUsers();
            });

            $('#reportsLink').click(function(e) {
                e.preventDefault();
                showReports();
            });

            $('#backToDashboardBtn, #backToDashboardBtn2, #backToDashboardBtn3, #backToDashboardBtn4, #backToDashboardBtn5, #backToDashboardBtn6, #backToDashboardBtn7').click(function(e) {
                e.preventDefault();
                showDashboard();
            });

            // Report type change
            $('#reportType').change(function() {
                if ($(this).val() === 'quarterly') {
                    $('#quarterField').show();
                } else {
                    $('#quarterField').hide();
                }
            });

            // Filter jobs
            $('#filterJobsBtn').click(function() {
                loadAllJobs();
            });

            $('#searchJob').keypress(function(e) {
                if (e.which === 13) {
                    loadAllJobs();
                }
            });

            // Filter users
            $('#filterUsersBtn').click(function() {
                loadUsers();
            });

            $('#searchUser').keypress(function(e) {
                if (e.which === 13) {
                    loadUsers();
                }
            });

            // Filter graduates
            $('#filterGraduatesBtn').click(function() {
                loadGraduates();
            });

            $('#searchGraduate').keypress(function(e) {
                if (e.which === 13) {
                    loadGraduates();
                }
            });

            // Generate Report
            $('#generateReportBtn').click(function() {
                generateReport();
            });

            // Create User
            $('#createUserBtn').click(function() {
                resetUserForm();
                $('#userModalTitle').text('Create New User');
                $('#passwordField').show();
                $('#userModal').modal('show');
            });

            $('#userRole').change(function() {
                toggleEmployerFields($(this).val());
            });

            $('#userModal').on('hide.bs.modal', function() {
                if (this.contains(document.activeElement)) {
                    $('#createUserBtn').trigger('focus');
                }
            });

            $('#userModal').on('hidden.bs.modal', function() {
                if (this.contains(document.activeElement)) {
                    document.activeElement.blur();
                }
            });

            // Save User
            $('#saveUserBtn').click(function() {
                saveUser();
            });

            // Add Graduate
            $('#addGraduateBtn').click(function() {
                resetGraduateForm();
                $('#graduateModalTitle').text('Add Graduate');
                $('#graduateModal').modal('show');
            });

            // Save Graduate
            $('#saveGraduateBtn').click(function() {
                saveGraduate();
            });

            $('#gradEducationPath').change(function() {
                updateGraduateEducationSection($(this).val());
            });

            initGraduateLocationDropdowns();

            // Import Graduates
            $('#importGraduatesBtn').click(function() {
                $('#importForm')[0].reset();
                $('#importFile').removeClass('is-invalid');
                $('#importFileError').text('');
                $('#importProgress').hide();
                $('#importGraduatesModal').modal('show');
            });

            $('#importFileBtn').click(function() {
                importGraduates();
            });

            // Download Template
            $('#downloadTemplateBtn').click(function(e) {
                e.preventDefault();
                downloadTemplate();
            });

            // ============================================
            // STATS FUNCTIONS
            // ============================================
            function loadStats() {
                window.adminApi.getStats()
                    .done(function(response) {
                        if (response.success) {
                            const stats = response.stats;
                            animateNumber('#totalUsers', stats.total_users || 0);
                            animateNumber('#totalJobs', stats.total_jobs || 0);
                            animateNumber('#pendingJobs', stats.pending_jobs || 0);
                            animateNumber('#totalApplications', stats.total_applications || 0);
                            $('#pendingBadge').text(stats.pending_jobs || 0);
                        }
                    })
                    .fail(function() {
                        showToast('error', 'Error loading statistics.');
                    });
            }

            function loadGraduateStats() {
                window.adminApi.getGraduateStats()
                    .done(function(response) {
                        if (response.success) {
                            const stats = response.stats;
                            animateNumber('#gradTotal', stats.total || 0);
                            animateNumber('#gradActive', stats.active || 0);
                            animateNumber('#gradEmployed', stats.employed || 0);
                            animateNumber('#gradUnemployed', stats.unemployed || 0);
                            $('#graduateBadge').text(stats.total || 0);
                        }
                    })
                    .fail(function() {
                        console.error('Error loading graduate stats');
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

            function normalizeEmploymentType(value) {
                switch (value) {
                    case 'full-time':
                        return 'permanent-contract';
                    case 'part-time':
                        return 'part-time-contract';
                    case 'contract':
                        return 'daily-paid-contract';
                    default:
                        return value;
                }
            }

            function formatEmploymentType(value) {
                const normalized = normalizeEmploymentType(value);

                switch (normalized) {
                    case 'permanent-contract':
                        return 'Permanent Contract';
                    case 'temporary-contract':
                        return 'Temporary Contract';
                    case 'part-time-contract':
                        return 'Part-time Contract';
                    case 'daily-paid-contract':
                        return 'Daily Paid Contract';
                    default:
                        return value || 'N/A';
                }
            }

            // ============================================
            // RECENT APPLICATIONS
            // ============================================
            function loadRecentApplications() {
                window.adminApi.getStats()
                    .done(function(response) {
                        if (response.success) {
                            const applications = response.stats.recent_applications || [];
                            let html = '';
                            if (applications.length === 0) {
                                html = `
                                    <div class="empty-state">
                                        <i class="fas fa-file-alt"></i>
                                        <h5>No Recent Applications</h5>
                                        <p>No applications have been submitted recently.</p>
                                    </div>
                                `;
                            } else {
                                html = `
                                    <div class="table-responsive-custom">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Job Title</th>
                                                    <th>Applicant</th>
                                                    <th>Status</th>
                                                    <th>Applied Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                `;
                                applications.forEach(app => {
                                    const statusColors = {
                                        submitted: 'pending',
                                        reviewed: 'approved',
                                        shortlisted: 'active',
                                        rejected: 'rejected',
                                        hired: 'active'
                                    };
                                    html += `
                                        <tr>
                                            <td><strong>${app.job_title}</strong></td>
                                            <td>${app.applicant_name}</td>
                                            <td><span class="status-badge ${statusColors[app.status] || 'pending'}">${app.status}</span></td>
                                            <td>${new Date(app.created_at).toLocaleDateString()}</td>
                                        </tr>
                                    `;
                                });
                                html += '</tbody></table></div>';
                            }
                            $('#recentApplicationsList').html(html);
                        }
                    })
                    .fail(function() {
                        $('#recentApplicationsList').html('<div class="alert alert-danger">Error loading applications.</div>');
                    });
            }

            // ============================================
            // PENDING JOBS
            // ============================================
            function loadPendingJobs() {
                window.adminApi.getPendingJobs()
                    .done(function(response) {
                        if (response.success) {
                            const jobs = response.jobs || [];
                            let html = '';
                            if (jobs.length === 0) {
                                html = `
                                    <div class="empty-state">
                                        <i class="fas fa-check-circle text-success"></i>
                                        <h5>All Clear!</h5>
                                        <p>No pending jobs for approval.</p>
                                    </div>
                                `;
                            } else {
                                jobs.forEach(job => {
                                    html += createJobCard(job, true);
                                });
                            }
                            $('#pendingJobsList').html(html);
                        }
                    })
                    .fail(function() {
                        $('#pendingJobsList').html('<div class="alert alert-danger">Error loading pending jobs.</div>');
                    });
            }

            // ============================================
            // ALL JOBS
            // ============================================
            function loadAllJobs() {
                const search = $('#searchJob').val();
                const status = $('#filterStatus').val();
                
                window.adminApi.getAllJobs({ search, status })
                    .done(function(response) {
                        if (response.success) {
                            const jobs = response.jobs || [];
                            let html = '';
                            if (jobs.length === 0) {
                                html = `
                                    <div class="empty-state">
                                        <i class="fas fa-briefcase"></i>
                                        <h5>No Jobs Found</h5>
                                        <p>No jobs match your search criteria.</p>
                                    </div>
                                `;
                            } else {
                                jobs.forEach(job => {
                                    html += createJobCard(job, false, true);
                                });
                            }
                            $('#allJobsList').html(html);
                        }
                    })
                    .fail(function() {
                        $('#allJobsList').html('<div class="alert alert-danger">Error loading jobs.</div>');
                    });
            }

            // ============================================
            // EMPLOYERS
            // ============================================
            function loadEmployers() {
                window.adminApi.getEmployers()
                    .done(function(response) {
                        if (response.success) {
                            const employers = response.employers || [];
                            let html = '';
                            if (employers.length === 0) {
                                html = `
                                    <div class="empty-state">
                                        <i class="fas fa-building"></i>
                                        <h5>No Employers</h5>
                                        <p>No employer accounts registered yet.</p>
                                    </div>
                                `;
                            } else {
                                html = `
                                    <div class="table-responsive-custom">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Company</th>
                                                    <th>Jobs Posted</th>
                                                    <th>Status</th>
                                                    <th>Joined</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                `;
                                employers.forEach(emp => {
                                    const statusClass = emp.is_active ? 'active' : 'inactive';
                                    const statusText = emp.is_active ? 'Active' : 'Inactive';
                                    html += `
                                        <tr>
                                            <td><strong>${emp.name}</strong></td>
                                            <td>${emp.email}</td>
                                            <td>${emp.company_name || 'N/A'}</td>
                                            <td><span class="badge bg-primary">${emp.jobs_count}</span></td>
                                            <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                                            <td>${new Date(emp.created_at).toLocaleDateString()}</td>
                                        </tr>
                                    `;
                                });
                                html += '</tbody></table></div>';
                            }
                            $('#employersList').html(html);
                        }
                    })
                    .fail(function() {
                        $('#employersList').html('<div class="alert alert-danger">Error loading employers.</div>');
                    });
            }

            // ============================================
            // JOBSEEKERS
            // ============================================
            function loadJobseekers() {
                window.adminApi.getJobseekers()
                    .done(function(response) {
                        if (response.success) {
                            const jobseekers = response.jobseekers || [];
                            let html = '';
                            if (jobseekers.length === 0) {
                                html = `
                                    <div class="empty-state">
                                        <i class="fas fa-users"></i>
                                        <h5>No Job Seekers</h5>
                                        <p>No job seeker accounts registered yet.</p>
                                    </div>
                                `;
                            } else {
                                html = `
                                    <div class="table-responsive-custom">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Applications</th>
                                                    <th>Status</th>
                                                    <th>Joined</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                `;
                                jobseekers.forEach(js => {
                                    const statusClass = js.is_active ? 'active' : 'inactive';
                                    const statusText = js.is_active ? 'Active' : 'Inactive';
                                    html += `
                                        <tr>
                                            <td><strong>${js.name}</strong></td>
                                            <td>${js.email}</td>
                                            <td><span class="badge bg-info">${js.applications_count}</span></td>
                                            <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                                            <td>${new Date(js.created_at).toLocaleDateString()}</td>
                                        </tr>
                                    `;
                                });
                                html += '</tbody></table></div>';
                            }
                            $('#jobseekersList').html(html);
                        }
                    })
                    .fail(function() {
                        $('#jobseekersList').html('<div class="alert alert-danger">Error loading job seekers.</div>');
                    });
            }

            // ============================================
            // GRADUATES
            // ============================================
            function loadGraduates(page = 1) {
                const search = $('#searchGraduate').val();
                const program = $('#filterProgram').val();
                const status = $('#filterGraduateStatus').val();
                const category = currentGraduateCategory;

                window.adminApi.getGraduates({ search, program, status, category, page, per_page: 10 })
                    .done(function(response) {
                        if (response.success) {
                            const graduates = response.graduates.data || [];
                            const programs = response.programs || [];
                            
                            // Update program filter options
                            let programOptions = '<option value="">All Programs</option>';
                            programs.forEach(p => {
                                programOptions += `<option value="${p}">${p}</option>`;
                            });
                            $('#filterProgram').html(programOptions);
                            
                            // Render graduates list
                            let html = '';
                            if (graduates.length === 0) {
                                html = `
                                    <div class="empty-state">
                                        <i class="fas fa-user-graduate"></i>
                                        <h5>No Graduates Found</h5>
                                        <p>${search ? 'No graduates match your search criteria.' : 'No graduates have been added yet.'}</p>
                                        <button class="btn btn-primary-gradient mt-3" onclick="$('#addGraduateBtn').click();">
                                            <i class="fas fa-user-plus me-2"></i> Add First Graduate
                                        </button>
                                    </div>
                                `;
                            } else {
                                html = `
                                    <div class="table-responsive-custom">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Student #</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Program</th>
                                                    <th>Year</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                `;
                                graduates.forEach(grad => {
                                    const statusClass = grad.status === 'active' ? 'active' : 
                                                        grad.status === 'employed' ? 'approved' : 
                                                        grad.status === 'unemployed' ? 'warning' : 'inactive';
                                    const statusBadge = grad.status.charAt(0).toUpperCase() + grad.status.slice(1);
                                    const gradName = grad.full_name || 'N/A';
                                    
                                    html += `
                                        <tr>
                                            <td><code>${grad.student_number}</code></td>
                                            <td><strong>${gradName}</strong></td>
                                            <td>${grad.email}</td>
                                            <td>${grad.program_of_study}</td>
                                            <td>${grad.year_of_graduation}</td>
                                            <td><span class="status-badge ${statusClass}">${statusBadge}</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-action view" onclick="viewGraduate(${grad.id})">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-action edit" onclick="editGraduate(${grad.id})">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-action delete" onclick="deleteGraduate(${grad.id})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    `;
                                });
                                html += '</tbody></table></div>';
                            }
                            $('#graduatesList').html(html);
                            
                            // Pagination
                            if (response.graduates.links) {
                                let paginationHtml = '<ul class="pagination">';
                                response.graduates.links.forEach(link => {
                                    const active = link.active ? 'active' : '';
                                    const disabled = link.url === null ? 'disabled' : '';
                                    const url = link.url ? `onclick="loadGraduatesPage('${link.url}')"` : '';
                                    paginationHtml += `
                                        <li class="page-item ${active} ${disabled}">
                                            <a class="page-link" href="#" ${url}>
                                                ${link.label.replace('&laquo;', '«').replace('&raquo;', '»')}
                                            </a>
                                        </li>
                                    `;
                                });
                                paginationHtml += '</ul>';
                                $('#graduatesPagination').html(paginationHtml);
                            }
                        }
                    })
                    .fail(function() {
                        $('#graduatesList').html('<div class="alert alert-danger">Error loading graduates.</div>');
                    });
            }

            window.loadGraduatesPage = function(url) {
                if (url) {
                    const page = new URL(url).searchParams.get('page');
                    loadGraduates(page);
                }
            };

            // ============================================
            // GRADUATE CRUD OPERATIONS
            // ============================================
            function resetGraduateForm() {
                $('#graduateForm')[0].reset();
                $('#graduateId').val('');
                $('#gradFormMethod').val('POST');
                $('.invalid-feedback').text('');
                $('#graduateForm .form-control, #graduateForm .form-check-input, #graduateForm .form-select').removeClass('is-invalid is-valid');
                $('input[name="gender"][value="Male"]').prop('checked', true);
                updateGraduateEducationSection('');
            }

            function updateGraduateEducationSection(type) {
                const sections = {
                    university: $('#gradUniversityFields'),
                    vta: $('#gradVtaFields'),
                    apprenticeship: $('#gradApprenticeshipFields')
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

            function loadGradRegions(selected = null, callback = null) {
                window.adminApi.getRegions().done(function(response) {
                    if (!response.success) return;
                    let options = '<option value="">Select Region</option>';
                    response.data.forEach(function(region) {
                        options += `<option value="${region.id}">${region.name}</option>`;
                    });
                    $('#gradRegion').html(options).prop('disabled', false);

                    if (selected) {
                        setSelectByValueOrText($('#gradRegion'), selected);
                    }
                    if (typeof callback === 'function') callback();
                });
            }

            function loadGradDistricts(regionId, selected = null, callback = null) {
                window.adminApi.getDistricts(regionId).done(function(response) {
                    if (!response.success) return;
                    let options = '<option value="">Select District</option>';
                    response.data.forEach(function(district) {
                        options += `<option value="${district.id}">${district.name}</option>`;
                    });
                    $('#gradDistrict').html(options).prop('disabled', false);

                    if (selected) {
                        setSelectByValueOrText($('#gradDistrict'), selected);
                    }
                    if (typeof callback === 'function') callback();
                });
            }

            function loadGradWards(districtId, selected = null, callback = null) {
                window.adminApi.getWards(districtId).done(function(response) {
                    if (!response.success) return;
                    let options = '<option value="">Select Ward</option>';
                    response.data.forEach(function(ward) {
                        options += `<option value="${ward.id}">${ward.name}</option>`;
                    });
                    $('#gradWard').html(options).prop('disabled', false);

                    if (selected) {
                        setSelectByValueOrText($('#gradWard'), selected);
                    }
                    if (typeof callback === 'function') callback();
                });
            }

            function loadGradShehias(wardId, selected = null) {
                window.adminApi.getShehias(wardId).done(function(response) {
                    if (!response.success) return;
                    let options = '<option value="">Select Shehia</option>';
                    response.data.forEach(function(shehia) {
                        options += `<option value="${shehia.id}">${shehia.name}</option>`;
                    });
                    $('#gradShehia').html(options).prop('disabled', false);

                    if (selected) {
                        setSelectByValueOrText($('#gradShehia'), selected);
                    }
                });
            }

            function setSelectByValueOrText($select, selected) {
                if (selected === null || selected === undefined || selected === '') return;

                const selectedText = String(selected).trim().toLowerCase();
                if ($select.find(`option[value="${selected}"]`).length) {
                    $select.val(String(selected));
                    return;
                }

                $select.find('option').each(function() {
                    if ($(this).text().trim().toLowerCase() === selectedText) {
                        $select.val($(this).val());
                        return false;
                    }
                });
            }

            function saveGraduate() {
                const id = $('#graduateId').val();
                const method = id ? 'PUT' : 'POST';
                const url = id ? `/admin/graduates/${id}` : '/admin/graduates';
                const formData = new FormData($('#graduateForm')[0]);
                formData.append('_method', method);
                
                // Reset errors
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                
                $('#saveGraduateBtn').prop('disabled', true);
                $('#saveGraduateBtn').html('<span class="spinner-border spinner-border-sm me-2"></span> Saving...');
                
                window.adminApi.saveGraduate(url, formData)
                    .done(function(response) {
                        $('#saveGraduateBtn').prop('disabled', false);
                        $('#saveGraduateBtn').html('<i class="fas fa-save me-2"></i> Save Graduate');
                        
                        if (response.success) {
                            showToast('success', response.message);
                            $('#graduateModal').modal('hide');
                            loadGraduates();
                            loadGraduateStats();
                        }
                    })
                    .fail(function(xhr) {
                        $('#saveGraduateBtn').prop('disabled', false);
                        $('#saveGraduateBtn').html('<i class="fas fa-save me-2"></i> Save Graduate');
                        
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            const fieldMap = {
                                full_name: '#gradFullName',
                                gender: 'input[name="gender"]',
                                resident_number: '#gradResidentNumber',
                                place_of_birth: '#gradPlaceBirth',
                                date_of_birth: '#gradDob',
                                region_id: '#gradRegion',
                                district_id: '#gradDistrict',
                                ward_id: '#gradWard',
                                shehia_id: '#gradShehia',
                                email: '#gradEmail',
                                phone: '#gradPhone',
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

                            Object.keys(errors).forEach(key => {
                                const fieldSelector = fieldMap[key] || null;
                                const field = fieldSelector ? $(fieldSelector) : $();
                                if (field.length) {
                                    field.addClass('is-invalid');
                                    const errorTarget = fieldSelector ? `${fieldSelector}Error` : '';
                                    if (errorTarget && $(errorTarget).length) {
                                        $(errorTarget).text(errors[key][0]);
                                    }
                                }
                            });
                            showToast('error', 'Please fix the validation errors.');
                        } else {
                            showToast('error', xhr.responseJSON?.message || 'Error saving graduate.');
                        }
                    });
            }

            window.editGraduate = function(id) {
                const normalizeDateForInput = function(value) {
                    if (!value) {
                        return '';
                    }

                    const isoMatch = String(value).match(/^(\d{4})-(\d{2})-(\d{2})/);
                    if (isoMatch) {
                        return `${isoMatch[1]}-${isoMatch[2]}-${isoMatch[3]}`;
                    }

                    const slashMatch = String(value).match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/);
                    if (slashMatch) {
                        const dd = slashMatch[1].padStart(2, '0');
                        const mm = slashMatch[2].padStart(2, '0');
                        const yyyy = slashMatch[3];
                        return `${yyyy}-${mm}-${dd}`;
                    }

                    return '';
                };

                window.adminApi.getGraduate(id)
                    .done(function(response) {
                        if (response.success) {
                            const grad = response.graduate;
                            $('#graduateId').val(grad.id);
                            $('#gradFormMethod').val('PUT');
                            $('#gradFullName').val(grad.full_name || '');
                            $('input[name="gender"][value="Male"]').prop('checked', (grad.gender || '') !== 'Female');
                            $('input[name="gender"][value="Female"]').prop('checked', (grad.gender || '') === 'Female');
                            $('#gradResidentNumber').val(grad.resident_number || '');
                            $('#gradPlaceBirth').val(grad.place_of_birth || '');
                            $('#gradDob').val(normalizeDateForInput(grad.date_of_birth));
                            loadGradRegions(grad.region || '', function() {
                                const selectedRegionId = $('#gradRegion').val();
                                if (selectedRegionId) {
                                    loadGradDistricts(selectedRegionId, grad.district || '', function() {
                                        const selectedDistrictId = $('#gradDistrict').val();
                                        if (selectedDistrictId) {
                                            loadGradWards(selectedDistrictId, (grad.ward || ''), function() {
                                                const selectedWardId = $('#gradWard').val();
                                                if (selectedWardId) {
                                                    loadGradShehias(selectedWardId, grad.shehia || '');
                                                }
                                            });
                                        }
                                    });
                                }
                            });
                            $('#gradEmail').val(grad.email);
                            $('#gradPhone').val(grad.phone || '');
                            $('#gradNextOfKin').val(grad.next_of_kin_phone || '');
                            $('#gradEducationPath').val(grad.education_type || '');
                            updateGraduateEducationSection(grad.education_type || '');
                            $('#gradCollegeName').val(grad.college_name || '');
                            $('#gradUniversityCourse').val(grad.university_course || '');
                            $('#gradCompletedYear').val(grad.completed_year || '');
                            $('#gradGpa').val(grad.gpa || grad.cgpa || '');
                            $('#gradNtaLevel').val(grad.nta_level || '');
                            $('#gradVtaCourse').val(grad.vta_course || '');
                            $('#gradStartedYear').val(grad.started_year || '');
                            $('#gradEndingYear').val(grad.ending_year || grad.year_of_graduation || '');
                            $('#gradApprenticeshipMode').val(grad.apprenticeship_mode || '');
                            $('#gradApprenticeshipSpec').val(grad.apprenticeship_specification || '');
                            
                            $('#graduateModalTitle').text('Edit Graduate');
                            $('#graduateModal').modal('show');
                        }
                    })
                    .fail(function() {
                        showToast('error', 'Error loading graduate details.');
                    });
            };

            window.viewGraduate = function(id) {
                $('#viewGraduateModal').modal('show');
                $('#viewGraduateContent').html(`
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);

                window.adminApi.getGraduate(id)
                    .done(function(response) {
                        if (response.success) {
                            const grad = response.graduate;
                            const valueOrNA = (value) => {
                                if (value === null || value === undefined) {
                                    return 'N/A';
                                }
                                const text = String(value).trim();
                                return text ? text : 'N/A';
                            };

                            const hasValue = (value) => valueOrNA(value) !== 'N/A';

                            const formatDate = (value) => {
                                if (!value) {
                                    return 'N/A';
                                }
                                const date = new Date(value);
                                if (Number.isNaN(date.getTime())) {
                                    return valueOrNA(value);
                                }
                                return date.toLocaleDateString();
                            };

                            const joinBadges = (value, badgeClass) => {
                                const items = String(value || '')
                                    .split(',')
                                    .map(item => item.trim())
                                    .filter(Boolean);

                                if (!items.length) {
                                    return '';
                                }

                                return `
                                    <div class="d-flex flex-wrap gap-2 mt-2">
                                        ${items.map(item => `<span class="badge ${badgeClass}">${item}</span>`).join('')}
                                    </div>
                                `;
                            };

                            const educationTypeLabel = {
                                university: 'University Graduate',
                                vta: 'VTA Graduate',
                                apprenticeship: 'Apprenticeship Graduate'
                            }[grad.education_type] || valueOrNA(grad.education_type);

                            const statusValue = valueOrNA(grad.status).toLowerCase();
                            const statusClass = statusValue !== 'n/a' ? statusValue : 'inactive';
                            const statusLabel = statusValue !== 'n/a'
                                ? statusValue.charAt(0).toUpperCase() + statusValue.slice(1)
                                : 'N/A';

                            const gradName = grad.full_name || 'N/A';
                            const initials = gradName
                                .split(' ')
                                .filter(Boolean)
                                .slice(0, 2)
                                .map(part => part.charAt(0).toUpperCase())
                                .join('') || 'G';
                            const photoHtml = grad.profile_photo ? 
                                `<img src="/uploads/graduates/photos/${grad.profile_photo}" alt="Profile Photo" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 3px solid #4F46E5; padding: 3px;">` :
                                `<div style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, #4F46E5, #7C3AED); display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem; font-weight: 700;">${initials}</div>`;

                            const hasSkills = hasValue(grad.skills);
                            const hasCertifications = hasValue(grad.certifications);
                            const hasSkillSection = hasSkills || hasCertifications;

                            const educationDetailsHtml = (() => {
                                if (grad.education_type === 'university') {
                                    return `
                                        <p><strong>College/University:</strong> ${valueOrNA(grad.college_name)}</p>
                                        <p><strong>Course:</strong> ${valueOrNA(grad.university_course)}</p>
                                        <p><strong>Completed Year:</strong> ${valueOrNA(grad.completed_year)}</p>
                                        <p><strong>GPA:</strong> ${valueOrNA(grad.gpa || grad.cgpa)}</p>
                                    `;
                                }

                                if (grad.education_type === 'vta') {
                                    return `
                                        <p><strong>NTA Level:</strong> ${valueOrNA(grad.nta_level)}</p>
                                        <p><strong>Course:</strong> ${valueOrNA(grad.vta_course)}</p>
                                        <p><strong>Started Year:</strong> ${valueOrNA(grad.started_year)}</p>
                                        <p><strong>Ending Year:</strong> ${valueOrNA(grad.ending_year)}</p>
                                    `;
                                }

                                if (grad.education_type === 'apprenticeship') {
                                    return `
                                        <p><strong>Apprenticeship Type:</strong> ${valueOrNA(grad.apprenticeship_mode)}</p>
                                        <p><strong>Specification:</strong> ${valueOrNA(grad.apprenticeship_specification)}</p>
                                    `;
                                }

                                return '<p class="text-muted mb-0">No education details available.</p>';
                            })();
                            
                            let html = `
                                <div class="text-center mb-4">
                                    ${photoHtml}
                                    <h3 class="mt-3">${gradName}</h3>
                                    <p class="text-muted"><i class="fas fa-envelope me-2"></i>${valueOrNA(grad.email)}</p>
                                    <span class="status-badge ${statusClass}">${statusLabel}</span>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="bg-light p-3 rounded">
                                            <h6><i class="fas fa-id-card text-primary me-2"></i>Personal Identification</h6>
                                            <p><strong>Student Number:</strong> ${valueOrNA(grad.student_number)}</p>
                                            <p><strong>Full Name:</strong> ${valueOrNA(grad.full_name)}</p>
                                            <p><strong>Gender:</strong> ${valueOrNA(grad.gender)}</p>
                                            <p><strong>Resident Number:</strong> ${valueOrNA(grad.resident_number)}</p>
                                            <p><strong>Place of Birth:</strong> ${valueOrNA(grad.place_of_birth)}</p>
                                            <p><strong>Date of Birth:</strong> ${formatDate(grad.date_of_birth)}</p>
                                            <p><strong>Phone:</strong> ${valueOrNA(grad.phone)}</p>
                                            <p><strong>Email:</strong> ${valueOrNA(grad.email)}</p>
                                            <p><strong>Next of Kin Phone:</strong> ${valueOrNA(grad.next_of_kin_phone)}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="bg-light p-3 rounded">
                                            <h6><i class="fas fa-map-marker-alt text-primary me-2"></i>Residence</h6>
                                            <p><strong>Region:</strong> ${valueOrNA(grad.region)}</p>
                                            <p><strong>District:</strong> ${valueOrNA(grad.district)}</p>
                                            <p><strong>Ward:</strong> ${valueOrNA(grad.ward)}</p>
                                            <p><strong>Shehia:</strong> ${valueOrNA(grad.shehia)}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3 mt-1">
                                    <div class="col-md-12">
                                        <div class="bg-light p-3 rounded">
                                            <h6><i class="fas fa-graduation-cap text-primary me-2"></i>Education</h6>
                                            <p><strong>Education Type:</strong> ${educationTypeLabel}</p>
                                            <p><strong>Category:</strong> ${valueOrNA(grad.graduate_category)}</p>
                                            <p><strong>Program of Study:</strong> ${valueOrNA(grad.program_of_study)}</p>
                                            <p><strong>Year of Graduation:</strong> ${valueOrNA(grad.year_of_graduation)}</p>
                                            <p><strong>CGPA:</strong> ${valueOrNA(grad.cgpa || grad.gpa)}</p>
                                            ${educationDetailsHtml}
                                        </div>
                                    </div>
                                </div>

                                ${hasSkillSection ? `
                                    <div class="mt-3">
                                        <div class="bg-light p-3 rounded">
                                            <h6><i class="fas fa-tasks text-primary me-2"></i>Skills & Certifications</h6>
                                            ${hasSkills ? `<p><strong>Skills:</strong>${joinBadges(grad.skills, 'bg-primary')}</p>` : ''}
                                            ${hasCertifications ? `<p class="mt-2"><strong>Certifications:</strong>${joinBadges(grad.certifications, 'bg-success')}</p>` : ''}
                                        </div>
                                    </div>
                                ` : ''}

                                ${grad.additional_notes ? `
                                    <div class="mt-3">
                                        <div class="bg-light p-3 rounded">
                                            <h6><i class="fas fa-sticky-note text-primary me-2"></i>Additional Notes</h6>
                                            <p>${grad.additional_notes}</p>
                                        </div>
                                    </div>
                                ` : ''}

                                ${grad.resume ? `
                                    <div class="mt-3">
                                        <a href="/uploads/graduates/resumes/${grad.resume}" target="_blank" class="btn btn-outline-gradient">
                                            <i class="fas fa-file-pdf me-2"></i> View Resume
                                        </a>
                                    </div>
                                ` : ''}

                                <div class="mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        Added: ${new Date(grad.created_at).toLocaleString()}
                                        ${grad.updated_at ? ` | Updated: ${new Date(grad.updated_at).toLocaleString()}` : ''}
                                    </small>
                                </div>
                            `;
                            $('#viewGraduateContent').html(html);
                        }
                    })
                    .fail(function() {
                        $('#viewGraduateContent').html('<div class="alert alert-danger">Error loading graduate details.</div>');
                    });
            };

            window.deleteGraduate = function(id) {
                if (confirm('Are you sure you want to delete this graduate? This action cannot be undone.')) {
                    window.adminApi.deleteGraduate(id)
                        .done(function(response) {
                            if (response.success) {
                                showToast('success', response.message);
                                loadGraduates();
                                loadGraduateStats();
                            }
                        })
                        .fail(function() {
                            showToast('error', 'Error deleting graduate.');
                        });
                }
            };

            // ============================================
            // BULK IMPORT GRADUATES
            // ============================================
            function importGraduates() {
                const fileInput = $('#importFile')[0];
                const file = fileInput.files[0];
                
                if (!file) {
                    $('#importFile').addClass('is-invalid');
                    $('#importFileError').text('Please select a file to import.');
                    return;
                }
                
                const formData = new FormData($('#importForm')[0]);
                
                $('#importFileBtn').prop('disabled', true);
                $('#importFileBtn').html('<span class="spinner-border spinner-border-sm me-2"></span> Importing...');
                $('#importProgress').show();
                $('#importProgressBar').css('width', '30%');
                $('#importStatus').text('Uploading file...');
                
                window.adminApi.importGraduates(formData, function() {
                        const xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener('progress', function(e) {
                            if (e.lengthComputable) {
                                const percentComplete = (e.loaded / e.total) * 70;
                                $('#importProgressBar').css('width', percentComplete + '%');
                                $('#importStatus').text(`Uploading... ${Math.round(percentComplete)}%`);
                            }
                        });
                        return xhr;
                    })
                    .done(function(response) {
                        $('#importProgressBar').css('width', '100%');
                        $('#importStatus').text('Import complete!');
                        
                        setTimeout(() => {
                            $('#importFileBtn').prop('disabled', false);
                            $('#importFileBtn').html('<i class="fas fa-upload me-2"></i> Import');
                            $('#importProgress').hide();
                            $('#importProgressBar').css('width', '0%');
                            
                            if (response.success) {
                                let message = response.message;
                                if (response.errors && response.errors.length > 0) {
                                    showToast('warning', `Imported ${response.imported_count || 0} records with ${response.errors.length} errors.`);
                                } else {
                                    showToast('success', `Successfully imported ${response.imported_count || 0} graduates!`);
                                }
                                $('#importGraduatesModal').modal('hide');
                                loadGraduates();
                                loadGraduateStats();
                            }
                        }, 500);
                    })
                    .fail(function(xhr) {
                        $('#importFileBtn').prop('disabled', false);
                        $('#importFileBtn').html('<i class="fas fa-upload me-2"></i> Import');
                        $('#importProgress').hide();
                        $('#importProgressBar').css('width', '0%');
                        
                        showToast('error', xhr.responseJSON?.message || 'Error importing file.');
                    });
            }

            // ============================================
            // DOWNLOAD TEMPLATE
            // ============================================
            function downloadTemplate() {
                window.adminApi.exportGraduateTemplate()
                    .done(function(response) {
                        if (response.success) {
                            const headers = response.headers.join(',');
                            const blob = new Blob([headers + '\n'], { type: 'text/csv' });
                            const url = window.URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.href = url;
                            a.download = 'graduates_template.csv';
                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                            window.URL.revokeObjectURL(url);
                            showToast('success', 'Template downloaded successfully!');
                        }
                    })
                    .fail(function() {
                        showToast('error', 'Error downloading template.');
                    });
            }

            // ============================================
            // USERS MANAGEMENT
            // ============================================
            function loadUsers() {
                const search = $('#searchUser').val();
                const role = $('#filterRole').val();
                const status = $('#filterUserStatus').val();
                
                window.adminApi.getUsers({ search, role, status })
                    .done(function(response) {
                        if (response.success) {
                            currentUsers = response.users || [];
                            usersPage = 1;
                            renderUsersList();
                        }
                    })
                    .fail(function() {
                        $('#usersList').html('<div class="alert alert-danger">Error loading users.</div>');
                    });
            }

            function renderUsersList() {
                const users = currentUsers || [];
                let html = '';

                if (users.length === 0) {
                    html = `
                        <div class="empty-state">
                            <i class="fas fa-users"></i>
                            <h5>No Users Found</h5>
                            <p>No users match your search criteria.</p>
                        </div>
                    `;
                    $('#usersList').html(html);
                    return;
                }

                const totalPages = Math.max(1, Math.ceil(users.length / usersPerPage));
                usersPage = Math.min(Math.max(usersPage, 1), totalPages);
                const startIndex = (usersPage - 1) * usersPerPage;
                const paginatedUsers = users.slice(startIndex, startIndex + usersPerPage);

                html = `
                    <div class="table-responsive-custom">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Company</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                paginatedUsers.forEach(user => {
                    const statusClass = user.is_active ? 'active' : 'inactive';
                    const statusText = user.is_active ? 'Active' : 'Inactive';
                    const roleClass = user.role === 'admin' ? 'role-badge-admin' : (user.role === 'employer' ? 'role-badge-employer' : 'role-badge-jobseeker');
                    html += `
                        <tr>
                            <td><strong>${user.name}</strong></td>
                            <td>${user.email}</td>
                            <td><span class="${roleClass}">${user.role_label}</span></td>
                            <td>${user.company_name || 'N/A'}</td>
                            <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                            <td>${new Date(user.created_at).toLocaleDateString()}</td>
                            <td>
                                <button class="btn btn-sm btn-action edit" onclick="editUser(${user.id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-action toggle" onclick="toggleUserStatus(${user.id})">
                                    <i class="fas ${user.is_active ? 'fa-pause' : 'fa-play'}"></i>
                                </button>
                                ${user.role !== 'admin' ? `
                                    <button class="btn btn-sm btn-action delete" onclick="deleteUser(${user.id})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                ` : ''}
                            </td>
                        </tr>
                    `;
                });

                html += '</tbody></table></div>';

                html += `
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
                        <div class="d-flex align-items-center gap-2">
                            <label for="usersPageSize" class="text-muted mb-0" style="font-size: 0.875rem;">Rows:</label>
                            <select id="usersPageSize" class="form-select form-select-sm" style="width: 90px;" onchange="changeUsersPageSize(this.value)">
                                <option value="5" ${usersPerPage === 5 ? 'selected' : ''}>5</option>
                                <option value="10" ${usersPerPage === 10 ? 'selected' : ''}>10</option>
                                <option value="20" ${usersPerPage === 20 ? 'selected' : ''}>20</option>
                            </select>
                            <small class="text-muted mb-0">Showing ${startIndex + 1}-${Math.min(startIndex + paginatedUsers.length, users.length)} of ${users.length}</small>
                        </div>
                        ${totalPages > 1 ? `
                        <nav aria-label="Users pagination">
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item ${usersPage === 1 ? 'disabled' : ''}">
                                    <button class="page-link" onclick="changeUsersPage(${usersPage - 1})">Previous</button>
                                </li>
                                ${Array.from({ length: totalPages }, (_, i) => i + 1).map(pageNumber => `
                                    <li class="page-item ${pageNumber === usersPage ? 'active' : ''}">
                                        <button class="page-link" onclick="changeUsersPage(${pageNumber})">${pageNumber}</button>
                                    </li>
                                `).join('')}
                                <li class="page-item ${usersPage === totalPages ? 'disabled' : ''}">
                                    <button class="page-link" onclick="changeUsersPage(${usersPage + 1})">Next</button>
                                </li>
                            </ul>
                        </nav>
                        ` : ''}
                    </div>
                `;

                $('#usersList').html(html);
            }

            window.changeUsersPage = function(page) {
                const totalPages = Math.max(1, Math.ceil((currentUsers || []).length / usersPerPage));
                usersPage = Math.min(Math.max(page, 1), totalPages);
                renderUsersList();
            };

            window.changeUsersPageSize = function(size) {
                const parsed = parseInt(size, 10);
                usersPerPage = [5, 10, 20].includes(parsed) ? parsed : 10;
                usersPage = 1;
                renderUsersList();
            };

            function resetUserForm() {
                $('#userForm')[0].reset();
                $('#userId').val('');
                $('#formMethod').val('POST');
                $('#userNameError').text('');
                $('#userEmailError').text('');
                $('#userPasswordError').text('');
                $('#userRoleError').text('');
                $('#userCompanyError').text('');
                $('#userPhoneError').text('');
                $('.form-control').removeClass('is-invalid');
                $('#userActive').prop('checked', true);
                $('#userPassword').prop('required', true);
                toggleEmployerFields('');
            }

            function toggleEmployerFields(role) {
                const isEmployer = role === 'employer';

                $('#userCompanyField').toggle(isEmployer);
                $('#userPhoneField').toggle(isEmployer);

                $('#userCompany').prop('required', isEmployer);
                $('#userPhone').prop('required', isEmployer);

                if (!isEmployer) {
                    $('#userCompany').val('').removeClass('is-invalid');
                    $('#userPhone').val('').removeClass('is-invalid');
                    $('#userCompanyError').text('');
                    $('#userPhoneError').text('');
                }
            }

            function saveUser() {
                const userId = $('#userId').val();
                const method = userId ? 'PUT' : 'POST';
                const url = userId ? `/admin/users/${userId}` : '/admin/users';
                const formData = new FormData($('#userForm')[0]);
                formData.append('_method', method);
                
                $('.form-control').removeClass('is-invalid');
                $('.invalid-feedback').text('');
                
                $('#saveUserBtn').prop('disabled', true);
                $('#saveUserBtn').html('<span class="spinner-border spinner-border-sm me-2"></span> Saving...');
                
                window.adminApi.saveUser(url, formData)
                    .done(function(response) {
                        $('#saveUserBtn').prop('disabled', false);
                        $('#saveUserBtn').html('<i class="fas fa-save me-2"></i> Save User');
                        
                        if (response.success) {
                            showToast('success', response.message);
                            $('#userModal').modal('hide');
                            loadUsers();
                            loadStats();
                        }
                    })
                    .fail(function(xhr) {
                        $('#saveUserBtn').prop('disabled', false);
                        $('#saveUserBtn').html('<i class="fas fa-save me-2"></i> Save User');
                        
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            const fieldMap = {
                                name: '#userName',
                                email: '#userEmail',
                                password: '#userPassword',
                                role: '#userRole',
                                company_name: '#userCompany',
                                phone: '#userPhone'
                            };

                            const errorMap = {
                                name: '#userNameError',
                                email: '#userEmailError',
                                password: '#userPasswordError',
                                role: '#userRoleError',
                                company_name: '#userCompanyError',
                                phone: '#userPhoneError'
                            };

                            Object.keys(errors).forEach(key => {
                                const field = $(fieldMap[key]);
                                if (field.length) {
                                    field.addClass('is-invalid');
                                    $(errorMap[key]).text(errors[key][0]);
                                }
                            });
                            showToast('error', 'Please fix the validation errors.');
                        } else {
                            showToast('error', xhr.responseJSON?.message || 'Error saving user.');
                        }
                    });
            }

            window.editUser = function(id) {
                window.adminApi.getUser(id)
                    .done(function(response) {
                        if (response.success) {
                            const user = response.user;
                            $('#userId').val(user.id);
                            $('#formMethod').val('PUT');
                            $('#userName').val(user.name);
                            $('#userEmail').val(user.email);
                            $('#userRole').val(user.role);
                            $('#userCompany').val(user.company_name || '');
                            $('#userPhone').val(user.phone || '');
                            $('#userActive').prop('checked', user.is_active);
                            $('#userModalTitle').text('Edit User');
                            $('#passwordField').hide();
                            $('#userPassword').prop('required', false);
                            toggleEmployerFields(user.role);
                            $('#userModal').modal('show');
                        }
                    })
                    .fail(function() {
                        showToast('error', 'Error loading user details.');
                    });
            };

            window.toggleUserStatus = function(id) {
                if (confirm('Are you sure you want to toggle this user\'s status?')) {
                    window.adminApi.toggleUserStatus(id)
                        .done(function(response) {
                            if (response.success) {
                                showToast('success', response.message);
                                loadUsers();
                                loadStats();
                            }
                        })
                        .fail(function() {
                            showToast('error', 'Error toggling user status.');
                        });
                }
            };

            window.deleteUser = function(id) {
                if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                    window.adminApi.deleteUser(id)
                        .done(function(response) {
                            if (response.success) {
                                showToast('success', response.message);
                                loadUsers();
                                loadStats();
                            }
                        })
                        .fail(function() {
                            showToast('error', 'Error deleting user.');
                        });
                }
            };

            // ============================================
            // JOB ACTIONS
            // ============================================
            window.approveJob = function(id) {
                if (confirm('Approve this job?')) {
                    window.adminApi.approveJob(id)
                        .done(function(response) {
                            if (response.success) {
                                showToast('success', response.message);
                                loadPendingJobs();
                                loadStats();
                            }
                        })
                        .fail(function() {
                            showToast('error', 'Error approving job.');
                        });
                }
            };

            window.rejectJob = function(id) {
                $('#rejectJobId').val(id);
                $('#rejectReason').val('').removeClass('is-invalid');
                $('#rejectReasonError').text('');
                $('#rejectJobModal').modal('show');
            };

            $('#confirmRejectJobBtn').on('click', function() {
                const id = $('#rejectJobId').val();
                const reason = $('#rejectReason').val().trim();

                $('#rejectReason').removeClass('is-invalid');
                $('#rejectReasonError').text('');

                if (reason.length < 10) {
                    $('#rejectReason').addClass('is-invalid');
                    $('#rejectReasonError').text('Please provide at least 10 characters.');
                    return;
                }

                const btn = $(this);
                const originalHtml = btn.html();
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Rejecting...');

                window.adminApi.rejectJob(id, reason)
                    .done(function(response) {
                        btn.prop('disabled', false).html(originalHtml);
                        if (response.success) {
                            $('#rejectJobModal').modal('hide');
                            showToast('warning', response.message);
                            loadPendingJobs();
                            loadAllJobs();
                            loadStats();
                        }
                    })
                    .fail(function(xhr) {
                        btn.prop('disabled', false).html(originalHtml);

                        if (xhr.status === 422 && xhr.responseJSON?.errors?.rejection_reason) {
                            $('#rejectReason').addClass('is-invalid');
                            $('#rejectReasonError').text(xhr.responseJSON.errors.rejection_reason[0]);
                            return;
                        }

                        showToast('error', xhr.responseJSON?.message || 'Error rejecting job.');
                    });
            });

            window.deleteJob = function(id) {
                if (confirm('Delete this job permanently?')) {
                    window.adminApi.deleteJob(id)
                        .done(function(response) {
                            if (response.success) {
                                showToast('success', response.message);
                                loadAllJobs();
                                loadStats();
                            }
                        })
                        .fail(function() {
                            showToast('error', 'Error deleting job.');
                        });
                }
            };

            window.viewJob = function(id) {
                window.adminApi.getJob(id)
                    .done(function(response) {
                        if (response.success) {
                            const job = response.job;
                            const modalContent = `
                                <div class="p-2">
                                    <h4 class="mb-3">${job.title}</h4>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <p><strong><i class="fas fa-building me-2"></i>Company:</strong> ${job.company_name}</p>
                                            <p><strong><i class="fas fa-map-marker-alt me-2"></i>Location:</strong> ${job.location}</p>
                                            <p><strong><i class="fas fa-clock me-2"></i>Type:</strong> ${formatEmploymentType(job.employment_type)}</p>
                                            <p><strong><i class="fas fa-user me-2"></i>Posted by:</strong> ${job.employer.name}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong><i class="fas fa-dollar-sign me-2"></i>Salary:</strong> ${job.salary_range || 'Not specified'}</p>
                                            <p><strong><i class="fas fa-calendar-alt me-2"></i>Posted:</strong> ${new Date(job.created_at).toLocaleDateString()}</p>
                                            <p><strong><i class="fas fa-hourglass-end me-2"></i>Deadline:</strong> ${job.application_deadline ? new Date(job.application_deadline).toLocaleDateString() : 'No deadline'}</p>
                                        </div>
                                    </div>
                                    <hr>
                                    <h5><i class="fas fa-align-left me-2"></i>Description</h5>
                                    <p>${job.description}</p>
                                    <h5><i class="fas fa-list-check me-2"></i>Requirements</h5>
                                    <p>${job.requirements}</p>
                                    ${job.benefits ? `<h5><i class="fas fa-gift me-2"></i>Benefits</h5><p>${job.benefits}</p>` : ''}
                                    <div class="mt-3">
                                        <span class="badge ${job.is_rejected ? 'bg-danger' : (job.is_approved ? 'bg-success' : 'bg-warning')} p-2">
                                            ${job.is_rejected ? 'Rejected' : (job.is_approved ? 'Approved' : 'Pending Approval')}
                                        </span>
                                        <span class="badge ${job.is_active ? 'bg-success' : 'bg-danger'} p-2 ms-2">
                                            ${job.is_active ? 'Active' : 'Inactive'}
                                        </span>
                                    </div>
                                    ${job.is_rejected && job.rejection_reason ? `
                                        <div class="alert alert-danger mt-3 mb-0">
                                            <strong>Rejection Reason:</strong> ${job.rejection_reason}
                                        </div>
                                    ` : ''}
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

            function createJobCard(job, showApprove = false, showDelete = false) {
                let statusClass = 'pending';
                let statusText = 'Pending';
                let statusIcon = 'fa-clock';
                
                if (job.is_rejected) {
                    statusClass = 'rejected';
                    statusText = 'Rejected';
                    statusIcon = 'fa-times-circle';
                } else if (job.is_approved) {
                    statusClass = 'approved';
                    statusText = 'Approved';
                    statusIcon = 'fa-check-circle';
                }

                const date = new Date(job.created_at).toLocaleDateString('en-US', { 
                    year: 'numeric', 
                    month: 'short', 
                    day: 'numeric' 
                });

                let actions = '';
                if (showApprove) {
                    actions = `
                        <div class="job-actions">
                            <button class="btn-action view" onclick="viewJob(${job.id})">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <button class="btn-action approve" onclick="approveJob(${job.id})">
                                <i class="fas fa-check"></i> Approve
                            </button>
                            <button class="btn-action reject" onclick="rejectJob(${job.id})">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        </div>
                    `;
                } else if (showDelete) {
                    actions = `
                        <div class="job-actions">
                            <button class="btn-action view" onclick="viewJob(${job.id})">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <button class="btn-action delete" onclick="deleteJob(${job.id})">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    `;
                }

                return `
                    <div class="job-card animate-fade-in">
                        <div class="card-header-custom">
                            <div>
                                <h5 class="job-title">${job.title}</h5>
                                <span class="job-company"><i class="fas fa-building me-1"></i> ${job.company_name}</span>
                                ${job.employer ? `<span class="job-company ms-3"><i class="fas fa-user me-1"></i> ${job.employer.name}</span>` : ''}
                            </div>
                            <span class="status-badge ${statusClass}">
                                <i class="fas ${statusIcon}"></i> ${statusText}
                            </span>
                        </div>
                        <div class="card-body-custom">
                            <div class="job-meta">
                                <span><i class="fas fa-map-marker-alt"></i> ${job.location}</span>
                                <span><i class="fas fa-hourglass-end"></i> Deadline: ${job.application_deadline ? new Date(job.application_deadline).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : 'No deadline'}</span>
                                <span><i class="fas fa-clock"></i> ${formatEmploymentType(job.employment_type)}</span>
                                ${job.salary_range ? `<span><i class="fas fa-dollar-sign"></i> ${job.salary_range}</span>` : ''}
                                <span><i class="fas fa-calendar-alt"></i> Posted: ${date}</span>
                            </div>
                            <div class="job-description">
                                ${job.description ? job.description.substring(0, 200) + (job.description.length > 200 ? '...' : '') : ''}
                            </div>
                            <div class="job-footer">
                                <div>
                                    <i class="fas fa-file-alt text-muted me-1"></i>
                                    <span class="text-muted" style="font-size: 0.9rem;">
                                        ${job.is_rejected ? 'Rejected by admin' : (job.is_approved ? 'Approved' : 'Awaiting approval')}
                                    </span>
                                    ${job.is_rejected && job.rejection_reason ? `<div class="text-danger mt-1" style="font-size: 0.85rem;"><strong>Reason:</strong> ${job.rejection_reason}</div>` : ''}
                                </div>
                                ${actions}
                            </div>
                        </div>
                    </div>
                `;
            }

            // ============================================
            // REPORT GENERATION
            // ============================================
            function generateReport() {
                const reportType = $('#reportType').val();
                const year = $('#reportYear').val();
                const quarter = $('#reportQuarter').val();
                
                $('#generateReportBtn').prop('disabled', true);
                $('#generateReportBtn').html('<span class="spinner-border spinner-border-sm me-2"></span> Generating...');
                
                window.adminApi.generateReport({
                        report_type: reportType,
                        year: year,
                        quarter: quarter
                    })
                    .done(function(response) {
                        $('#generateReportBtn').prop('disabled', false);
                        $('#generateReportBtn').html('<i class="fas fa-chart-bar me-2"></i> Generate');
                        
                        if (response.success) {
                            const data = response.data;
                            let html = `
                                <div class="report-card animate-fade-in">
                                    <div class="report-title">
                                        <i class="fas fa-chart-line text-primary me-2"></i>
                                        ${data.title}
                                    </div>
                                    <div class="report-stats">
                                        <div class="stat-item primary">
                                            <div class="number">${data.total_jobs || 0}</div>
                                            <div class="label">Total Jobs</div>
                                        </div>
                                        <div class="stat-item success">
                                            <div class="number">${data.active_jobs || 0}</div>
                                            <div class="label">Active Jobs</div>
                                        </div>
                                        <div class="stat-item warning">
                                            <div class="number">${data.pending_jobs || 0}</div>
                                            <div class="label">Pending Jobs</div>
                                        </div>
                                        <div class="stat-item info">
                                            <div class="number">${data.total_employers || 0}</div>
                                            <div class="label">Employers</div>
                                        </div>
                                        <div class="stat-item success">
                                            <div class="number">${data.total_jobseekers || 0}</div>
                                            <div class="label">Job Seekers</div>
                                        </div>
                                        <div class="stat-item primary">
                                            <div class="number">${data.total_applications || 0}</div>
                                            <div class="label">Applications</div>
                                        </div>
                                    </div>
                                    <div class="report-actions">
                                        <button class="btn btn-sm btn-primary-gradient" onclick="window.print()">
                                            <i class="fas fa-print me-1"></i> Print
                                        </button>
                                        <button class="btn btn-sm btn-success-gradient" onclick="showToast('info', 'Export functionality coming soon!')">
                                            <i class="fas fa-file-excel me-1"></i> Export
                                        </button>
                                    </div>
                                </div>
                            `;
                            $('#reportResults').html(html);
                            showToast('success', 'Report generated successfully!');
                        }
                    })
                    .fail(function(xhr) {
                        $('#generateReportBtn').prop('disabled', false);
                        $('#generateReportBtn').html('<i class="fas fa-chart-bar me-2"></i> Generate');
                        showToast('error', xhr.responseJSON?.message || 'Error generating report.');
                    });
            }

            // ============================================
            // NAVIGATION FUNCTIONS
            // ============================================
            function showDashboard() {
                $('#dashboardContent').show();
                $('#pendingJobsContent').hide();
                $('#allJobsContent').hide();
                $('#employersContent').hide();
                $('#jobseekersContent').hide();
                $('#graduatesContent').hide();
                $('#usersContent').hide();
                $('#reportsContent').hide();
                $('.nav-link').removeClass('active');
                $('#dashboardLink').addClass('active');
                setGraduatesMenuExpanded(false);
                loadStats();
                loadRecentApplications();
                loadGraduateStats();
            }

            function showPendingJobs() {
                $('#dashboardContent').hide();
                $('#pendingJobsContent').show();
                $('#allJobsContent').hide();
                $('#employersContent').hide();
                $('#jobseekersContent').hide();
                $('#graduatesContent').hide();
                $('#usersContent').hide();
                $('#reportsContent').hide();
                $('.nav-link').removeClass('active');
                $('#pendingJobsLink').addClass('active');
                setGraduatesMenuExpanded(false);
                loadPendingJobs();
            }

            function showAllJobs() {
                $('#dashboardContent').hide();
                $('#pendingJobsContent').hide();
                $('#allJobsContent').show();
                $('#employersContent').hide();
                $('#jobseekersContent').hide();
                $('#graduatesContent').hide();
                $('#usersContent').hide();
                $('#reportsContent').hide();
                $('.nav-link').removeClass('active');
                $('#allJobsLink').addClass('active');
                setGraduatesMenuExpanded(false);
                loadAllJobs();
            }

            function showEmployers() {
                $('#dashboardContent').hide();
                $('#pendingJobsContent').hide();
                $('#allJobsContent').hide();
                $('#employersContent').show();
                $('#jobseekersContent').hide();
                $('#graduatesContent').hide();
                $('#usersContent').hide();
                $('#reportsContent').hide();
                $('.nav-link').removeClass('active');
                $('#employersLink').addClass('active');
                setGraduatesMenuExpanded(false);
                loadEmployers();
            }

            function showJobseekers() {
                $('#dashboardContent').hide();
                $('#pendingJobsContent').hide();
                $('#allJobsContent').hide();
                $('#employersContent').hide();
                $('#jobseekersContent').show();
                $('#graduatesContent').hide();
                $('#usersContent').hide();
                $('#reportsContent').hide();
                $('.nav-link').removeClass('active');
                $('#jobseekersLink').addClass('active');
                setGraduatesMenuExpanded(false);
                loadJobseekers();
            }

            function showGraduates(category = '') {
                $('#dashboardContent').hide();
                $('#pendingJobsContent').hide();
                $('#allJobsContent').hide();
                $('#employersContent').hide();
                $('#jobseekersContent').hide();
                $('#graduatesContent').show();
                $('#usersContent').hide();
                $('#reportsContent').hide();
                $('.nav-link').removeClass('active');
                $('#graduatesLink').addClass('active');
                setGraduatesMenuExpanded(true);
                currentGraduateCategory = category || '';
                $('#graduatesMenu [data-graduate-category]').removeClass('active');
                $('#graduatesMenu [data-graduate-category="' + currentGraduateCategory + '"]').addClass('active');
                loadGraduates();
                loadGraduateStats();
            }

            function showUsers() {
                $('#dashboardContent').hide();
                $('#pendingJobsContent').hide();
                $('#allJobsContent').hide();
                $('#employersContent').hide();
                $('#jobseekersContent').hide();
                $('#graduatesContent').hide();
                $('#usersContent').show();
                $('#reportsContent').hide();
                $('.nav-link').removeClass('active');
                $('#usersLink').addClass('active');
                setGraduatesMenuExpanded(false);
                loadUsers();
            }

            function showReports() {
                $('#dashboardContent').hide();
                $('#pendingJobsContent').hide();
                $('#allJobsContent').hide();
                $('#employersContent').hide();
                $('#jobseekersContent').hide();
                $('#graduatesContent').hide();
                $('#usersContent').hide();
                $('#reportsContent').show();
                $('.nav-link').removeClass('active');
                $('#reportsLink').addClass('active');
                setGraduatesMenuExpanded(false);
            }

            // ============================================
            // TOAST NOTIFICATION
            // ============================================
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

            // CSRF token setup
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
