    import $ from 'jquery';
    import 'summernote/dist/summernote-bs5.min.css';
    import 'summernote/dist/summernote-bs5.min.js';
    import './api.js';

    window.jQuery = $;
    window.$ = $;

    $(document).ready(function() {
        const employerRoot = document.getElementById('jobForm') || document.getElementById('postJobLink') || document.getElementById('myJobsLink');
        if (!employerRoot) {
            return;
        }

        let currentApplicationsJobId = null;
        let currentApplicationsJobTitle = '';
        let currentApplications = [];
        let currentApplicationsTabStatus = 'submitted';
        let currentApplicationsPage = 1;
        let applicantsPerPage = 10;
        let currentMyJobs = [];
        let myJobsPage = 1;
        let myJobsPerPage = 10;
        let summernoteAvailable = false;

        function initializeSummernoteEditors() {
            if (typeof $.fn.summernote !== 'function') {
                summernoteAvailable = false;
                console.warn('Summernote plugin not available; using plain textarea fields.');
                return;
            }

            summernoteAvailable = true;
            const baseToolbar = [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link']],
                ['view', ['codeview']]
            ];

            $('#description').summernote({
                height: 180,
                placeholder: 'Describe the job role, responsibilities, and expectations...',
                toolbar: baseToolbar,
                callbacks: {
                    onChange: function(contents) {
                        $(this).val(contents);
                        $(this).next('.note-editor').removeClass('is-invalid');
                        $('#descriptionError').removeClass('d-block').text('');
                    }
                }
            });

            $('#requirements').summernote({
                height: 160,
                placeholder: 'List the required skills, qualifications, and experience...',
                toolbar: baseToolbar,
                callbacks: {
                    onChange: function(contents) {
                        $(this).val(contents);
                        $(this).next('.note-editor').removeClass('is-invalid');
                        $('#requirementsError').removeClass('d-block').text('');
                    }
                }
            });
        }

        function syncSummernoteFields() {
            if (!summernoteAvailable) {
                return;
            }

            if ($('#description').length) {
                $('#description').val($('#description').summernote('code'));
            }

            if ($('#requirements').length) {
                $('#requirements').val($('#requirements').summernote('code'));
            }
        }

        function resetSummernoteFields() {
            if (summernoteAvailable) {
                if ($('#description').length) {
                    $('#description').summernote('code', '');
                }
                if ($('#requirements').length) {
                    $('#requirements').summernote('code', '');
                }
            }

            $('#description').val('');
            $('#requirements').val('');
        }

        // Initialize
        initializeSummernoteEditors();
        loadDashboardStats();
        loadRecentJobs();

        // Navigation
        $('#postJobLink, #quickPostBtn, #postNewFromListBtn').click(function(e) {
            e.preventDefault();
            showPostJobForm();
        });

        $('#myJobsLink').click(function(e) {
            e.preventDefault();
            showMyJobs();
        });

        $('#dashboardLink').click(function(e) {
            e.preventDefault();
            showDashboard();
        });

        $('#backToDashboardBtn').click(function(e) {
            e.preventDefault();
            showDashboard();
        });

        $('#backToMyJobsBtn').click(function(e) {
            e.preventDefault();
            showMyJobs();
        });

        $('#applicationsTabs [data-status]').click(function() {
            currentApplicationsTabStatus = $(this).data('status');
            currentApplicationsPage = 1;
            $('#applicationsTabs [data-status]').removeClass('btn-primary-gradient').addClass('btn-secondary-gradient');
            $(this).removeClass('btn-secondary-gradient').addClass('btn-primary-gradient');
            renderApplicationsList(currentApplicationsTabStatus, currentApplicationsPage);
        });

        // Submit job form
        $('#jobForm').on('submit', function(e) {
            e.preventDefault();
            saveJob();
        });

        $('#jobForm').on('reset', function() {
            setTimeout(function() {
                resetSummernoteFields();
                $('.form-control').removeClass('is-invalid');
                $('.note-editor').removeClass('is-invalid');
                $('.invalid-feedback').removeClass('d-block').text('');
            }, 0);
        });

        // Load dashboard stats
        function loadDashboardStats() {
            window.employerApi.getJobs()
                .done(function(response) {
                    if (response.success) {
                        const jobs = response.jobs && response.jobs.data ? response.jobs.data : (Array.isArray(response.jobs) ? response.jobs : []);
                        const total = jobs.length;
                        const active = jobs.filter(j => j.is_active).length;
                        const pending = jobs.filter(j => !j.is_approved && !j.is_rejected).length;

                        animateNumber('#totalJobs', total);
                        animateNumber('#activeJobs', active);
                        animateNumber('#pendingJobs', pending);
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

        // Load recent jobs
        function loadRecentJobs() {
            window.employerApi.getJobs()
                .done(function(response) {
                    if (response.success) {
                        const jobs = response.jobs && response.jobs.data ? response.jobs.data : (Array.isArray(response.jobs) ? response.jobs : []);
                        const recentJobs = jobs.slice(0, 5);
                        let html = '';
                        if (recentJobs.length === 0) {
                            html = `
                                <div class="empty-state">
                                    <i class="fas fa-briefcase"></i>
                                    <h5>No Jobs Posted Yet</h5>
                                    <p>Start your recruitment journey by posting your first job.</p>
                                    <button class="btn btn-primary-gradient" onclick="$('#quickPostBtn').click()">
                                        <i class="fas fa-plus me-2"></i> Post Your First Job
                                    </button>
                                </div>
                            `;
                        } else {
                            recentJobs.forEach(job => {
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

        // Load all jobs for My Jobs view
        function loadAllJobs() {
            window.employerApi.getJobs()
                .done(function(response) {
                    if (response.success) {
                        currentMyJobs = response.jobs && response.jobs.data ? response.jobs.data : (Array.isArray(response.jobs) ? response.jobs : []);
                        myJobsPage = 1;
                        renderMyJobsList();
                    }
                })
                .fail(function() {
                    $('#jobsList').html('<div class="alert alert-danger">Error loading jobs.</div>');
                });
        }

        function renderMyJobsList() {
            const jobs = currentMyJobs || [];
            let html = '';

            if (jobs.length === 0) {
                html = `
                    <div class="empty-state">
                        <i class="fas fa-briefcase"></i>
                        <h5>No Jobs Posted</h5>
                        <p>You haven't posted any jobs yet. Create your first job listing now.</p>
                        <button class="btn btn-primary-gradient" onclick="$('#postNewFromListBtn').click()">
                            <i class="fas fa-plus me-2"></i> Post Your First Job
                        </button>
                    </div>
                `;
                $('#jobsList').html(html);
                return;
            }

            const totalPages = Math.max(1, Math.ceil(jobs.length / myJobsPerPage));
            myJobsPage = Math.min(Math.max(myJobsPage, 1), totalPages);
            const startIndex = (myJobsPage - 1) * myJobsPerPage;
            const paginatedJobs = jobs.slice(startIndex, startIndex + myJobsPerPage);

            paginatedJobs.forEach(job => {
                html += createJobCard(job, true);
            });

            html += `
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
                    <div class="d-flex align-items-center gap-2">
                        <label for="myJobsPageSize" class="text-muted mb-0" style="font-size: 0.875rem;">Rows:</label>
                        <select id="myJobsPageSize" class="form-select form-select-sm" style="width: 90px;" onchange="changeMyJobsPageSize(this.value)">
                            <option value="5" ${myJobsPerPage === 5 ? 'selected' : ''}>5</option>
                            <option value="10" ${myJobsPerPage === 10 ? 'selected' : ''}>10</option>
                            <option value="20" ${myJobsPerPage === 20 ? 'selected' : ''}>20</option>
                        </select>
                        <small class="text-muted mb-0">Showing ${startIndex + 1}-${Math.min(startIndex + paginatedJobs.length, jobs.length)} of ${jobs.length}</small>
                    </div>
                    ${totalPages > 1 ? `
                    <nav aria-label="My Jobs pagination">
                        <ul class="pagination pagination-sm mb-0">
                            <li class="page-item ${myJobsPage === 1 ? 'disabled' : ''}">
                                <button class="page-link" onclick="changeMyJobsPage(${myJobsPage - 1})">Previous</button>
                            </li>
                            ${Array.from({ length: totalPages }, (_, i) => i + 1).map(pageNumber => `
                                <li class="page-item ${pageNumber === myJobsPage ? 'active' : ''}">
                                    <button class="page-link" onclick="changeMyJobsPage(${pageNumber})">${pageNumber}</button>
                                </li>
                            `).join('')}
                            <li class="page-item ${myJobsPage === totalPages ? 'disabled' : ''}">
                                <button class="page-link" onclick="changeMyJobsPage(${myJobsPage + 1})">Next</button>
                            </li>
                        </ul>
                    </nav>
                    ` : ''}
                </div>
            `;

            $('#jobsList').html(html);
        }

        window.changeMyJobsPage = function(page) {
            const totalPages = Math.max(1, Math.ceil((currentMyJobs || []).length / myJobsPerPage));
            myJobsPage = Math.min(Math.max(page, 1), totalPages);
            renderMyJobsList();
        };

        window.changeMyJobsPageSize = function(size) {
            const parsed = parseInt(size, 10);
            myJobsPerPage = [5, 10, 20].includes(parsed) ? parsed : 10;
            myJobsPage = 1;
            renderMyJobsList();
        };

        // Create job card HTML
        function createJobCard(job, showActions = false) {
            let statusClass = 'active';
            let statusText = 'Active';
            let statusIcon = 'fa-check-circle';
            
            if (job.is_rejected) {
                statusClass = 'rejected';
                statusText = 'Rejected';
                statusIcon = 'fa-times-circle';
            } else if (!job.is_approved) {
                statusClass = 'pending';
                statusText = 'Pending Approval';
                statusIcon = 'fa-clock';
            } else if (!job.is_active) {
                statusClass = 'inactive';
                statusText = 'Inactive';
                statusIcon = 'fa-pause-circle';
            }
            
            const date = new Date(job.created_at).toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric' 
            });

            let actions = '';
            if (showActions) {
                actions = `
                    <div class="job-actions">
                        <button class="btn-action view" onclick="viewJob(${job.id})">
                            <i class="fas fa-eye"></i> View
                        </button>
                        <button class="btn-action edit" onclick="editJob(${job.id})">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn-action applications" onclick='viewApplications(${job.id}, ${JSON.stringify(job.title)})'>
                            <i class="fas fa-users"></i> Applicants
                        </button>
                        ${job.is_approved ? `
                            <button class="btn-action toggle" onclick="toggleStatus(${job.id})">
                                <i class="fas ${job.is_active ? 'fa-pause' : 'fa-play'}"></i>
                                ${job.is_active ? 'Deactivate' : 'Activate'}
                            </button>
                        ` : ''}
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
                        </div>
                        <span class="status-badge ${statusClass}">
                            <i class="fas ${statusIcon}"></i> ${statusText}
                        </span>
                    </div>
                    <div class="card-body-custom">
                        <div class="job-meta">
                            <span><i class="fas fa-map-marker-alt"></i> ${job.location}</span>
                            <span><i class="fas fa-clock"></i> ${formatEmploymentType(job.employment_type)}</span>
                           
                            <span><i class="fas fa-calendar-alt"></i> Posted: ${date}</span>
                            ${job.application_deadline ? `<span><i class="fas fa-hourglass-end"></i> Deadline: ${new Date(job.application_deadline).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</span>` : ''}
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

        // Save job (create or update)
        function saveJob() {
            syncSummernoteFields();
            const formElement = document.getElementById('jobForm');
            const formData = new FormData(formElement);
            const jobId = $('#jobId').val();
            const method = jobId ? 'PUT' : 'POST';
            const url = jobId ? `/employer/jobs/${jobId}` : '/employer/jobs';
            formData.set('_method', method);

            // Reset errors
            $('.form-control').removeClass('is-invalid');
            $('.note-editor').removeClass('is-invalid');
            $('.invalid-feedback').removeClass('d-block').text('');

            // Show loading
            $('#loadingSpinner').show();
            $('#submitBtn').prop('disabled', true);

            window.employerApi.saveJob(url, formData)
                .done(function(response) {
                    $('#loadingSpinner').hide();
                    $('#submitBtn').prop('disabled', false);
                    
                    if (response.success) {
                        showToast('success', response.message);
                        $('#jobForm')[0].reset();
                        resetSummernoteFields();
                        $('#jobId').val('');
                        $('#submitBtnText').text('Post Job');
                        $('#formMethod').val('POST');
                        $('#jobForm').find('input[name="_method"]').remove();
                        $('#formTitle').text('Post New Job');
                        $('#existingAttachmentInfo').html('');
                        
                        // Refresh data
                        loadDashboardStats();
                        loadRecentJobs();
                        if ($('#myJobsContent').is(':visible')) {
                            loadAllJobs();
                        }
                        
                        setTimeout(() => {
                            showDashboard();
                        }, 1500);
                    }
                })
                .fail(function(xhr) {
                    $('#loadingSpinner').hide();
                    $('#submitBtn').prop('disabled', false);
                    
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(key => {
                            const field = $(`#${key}`);
                            field.addClass('is-invalid');
                            if (key === 'description' || key === 'requirements') {
                                field.next('.note-editor').addClass('is-invalid');
                            }
                            $(`#${key}Error`).addClass('d-block').text(errors[key][0]);
                        });
                        showToast('error', 'Please fix the validation errors.');
                    } else {
                        showToast('error', 'An error occurred. Please try again.');
                    }
                });
        }

        // View job
        window.viewJob = function(id) {
            window.employerApi.getJob(id)
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

        // View applications
        window.viewApplications = function(jobId, jobTitle, activeStatus = null) {
            currentApplicationsJobId = jobId;
            currentApplicationsJobTitle = jobTitle || 'Selected Job';
            currentApplicationsTabStatus = activeStatus || currentApplicationsTabStatus || 'submitted';
            if (currentApplicationsTabStatus === 'pending') {
                currentApplicationsTabStatus = 'submitted';
            }
            currentApplicationsPage = 1;

            $('#applicationsJobTitle').text(`Applications for: ${currentApplicationsJobTitle}`);
            $('#applicationsListContainer').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-3 text-muted mb-0">Loading applications...</p>
                </div>
            `);

            window.employerApi.getApplications(jobId)
                .done(function(response) {
                    if (response.success) {
                        const rawApplications = response.applications || [];
                        const seenApplicationIds = new Set();
                        currentApplications = rawApplications.filter(app => {
                            if (seenApplicationIds.has(app.id)) {
                                return false;
                            }
                            seenApplicationIds.add(app.id);
                            return true;
                        });
                        $('#applicationsCountBadge').text(`${currentApplications.length} Applicants`);

                        $('#applicationsTabs [data-status]').removeClass('btn-primary-gradient').addClass('btn-secondary-gradient');
                        $('#applicationsTabs [data-status="' + currentApplicationsTabStatus + '"]').removeClass('btn-secondary-gradient').addClass('btn-primary-gradient');

                        renderApplicationsList(currentApplicationsTabStatus, currentApplicationsPage);
                        showApplicationsBoard();
                    }
                })
                .fail(function() {
                    showToast('error', 'Error loading applications.');
                });
        };

        function renderApplicationsList(statusFilter, page = 1) {
            const statusColors = {
                submitted: 'warning',
                reviewed: 'info',
                shortlisted: 'primary',
                rejected: 'danger',
                hired: 'success'
            };

            const list = currentApplications.filter(app => app.status === statusFilter);
            const totalPages = Math.max(1, Math.ceil(list.length / applicantsPerPage));
            const safePage = Math.min(Math.max(page, 1), totalPages);
            currentApplicationsPage = safePage;
            const startIndex = (safePage - 1) * applicantsPerPage;
            const paginatedList = list.slice(startIndex, startIndex + applicantsPerPage);
            const exportButtonHtml = (statusFilter === 'shortlisted' || statusFilter === 'hired')
                ? `
                    <div class="d-flex justify-content-end mb-3">
                        <button type="button" class="btn btn-outline-${statusFilter === 'shortlisted' ? 'primary' : 'success'} btn-sm" onclick="exportApplicantsByStatus('${statusFilter}')">
                            <i class="fas fa-file-csv me-1"></i> Export ${statusFilter.charAt(0).toUpperCase() + statusFilter.slice(1)}
                        </button>
                    </div>
                `
                : '';

            if (list.length === 0) {
                $('#applicationsListContainer').html(`
                    <div class="text-center py-5">
                        <i class="fas fa-users" style="font-size: 4rem; color: #CBD5E1;"></i>
                        <h5 class="mt-3">No ${statusFilter} applicants found</h5>
                        <p class="text-muted">Try another tab or update candidate statuses.</p>
                    </div>
                `);
                return;
            }

            let html = `
                ${exportButtonHtml}
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Applicant</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Profile</th>
                                <th>Attachments</th>
                                <th style="min-width: 50px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            paginatedList.forEach(app => {
                html += `
                    <tr>
                        <td><strong>${app.jobseeker_name}</strong></td>
                        <td>${app.jobseeker_email}</td>
                         <td>${app.jobseeker_phone || 'N/A'}</td>
                         
                        <td><span class="badge bg-${statusColors[app.status] || 'secondary'}">${app.status}</span></td>
                       
                        <td>
                            <button type="button" class="btn btn-sm btn-action view" title="View Applicant Profile" onclick="viewApplicantProfile(${app.id})">
                                
                                <i class="fas fa-eye"></i> View
                            </button>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                ${app.resume_url ? `<a href="${app.resume_url}" target="_blank" rel="noopener" class="badge bg-success text-decoration-none">Resume</a>` : '<span class="badge bg-secondary">No Resume</span>'}
                                ${app.cover_letter_url ? `<a href="${app.cover_letter_url}" target="_blank" rel="noopener" class="badge bg-primary text-decoration-none">Cover Letter</a>` : '<span class="badge bg-secondary">No Cover Letter</span>'}
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column gap-2">
                                <select class="form-select form-select-sm" onchange="updateApplicationStatus(${app.id}, this.value)">
                                    <option value="submitted" ${app.status === 'submitted' ? 'selected' : ''}>Submitted</option>
                                   
                                    <option value="shortlisted" ${app.status === 'shortlisted' ? 'selected' : ''}>Shortlisted</option>
                                    <option value="rejected" ${app.status === 'rejected' ? 'selected' : ''}>Rejected</option>
                                    <option value="hired" ${app.status === 'hired' ? 'selected' : ''}>Hired</option>
                                </select>
                            </div>
                        </td>
                    </tr>
                `;
            });

            html += '</tbody></table></div>';

            if (totalPages > 1) {
                html += `
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
                        <div class="d-flex align-items-center gap-2">
                            <label for="applicantsPageSize" class="text-muted mb-0" style="font-size: 0.875rem;">Rows:</label>
                            <select id="applicantsPageSize" class="form-select form-select-sm" style="width: 90px;" onchange="changeApplicantsPageSize(this.value)">
                                <option value="5" ${applicantsPerPage === 5 ? 'selected' : ''}>5</option>
                                <option value="10" ${applicantsPerPage === 10 ? 'selected' : ''}>10</option>
                                <option value="20" ${applicantsPerPage === 20 ? 'selected' : ''}>20</option>
                            </select>
                            <small class="text-muted mb-0">Showing ${startIndex + 1}-${Math.min(startIndex + paginatedList.length, list.length)} of ${list.length}</small>
                        </div>
                        <nav aria-label="Applicants pagination">
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item ${safePage === 1 ? 'disabled' : ''}">
                                    <button class="page-link" onclick="changeApplicantsPage(${safePage - 1})">Previous</button>
                                </li>
                                ${Array.from({ length: totalPages }, (_, i) => i + 1).map(pageNumber => `
                                    <li class="page-item ${pageNumber === safePage ? 'active' : ''}">
                                        <button class="page-link" onclick="changeApplicantsPage(${pageNumber})">${pageNumber}</button>
                                    </li>
                                `).join('')}
                                <li class="page-item ${safePage === totalPages ? 'disabled' : ''}">
                                    <button class="page-link" onclick="changeApplicantsPage(${safePage + 1})">Next</button>
                                </li>
                            </ul>
                        </nav>
                    </div>
                `;
            } else {
                html += `
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
                        <div class="d-flex align-items-center gap-2">
                            <label for="applicantsPageSize" class="text-muted mb-0" style="font-size: 0.875rem;">Rows:</label>
                            <select id="applicantsPageSize" class="form-select form-select-sm" style="width: 90px;" onchange="changeApplicantsPageSize(this.value)">
                                <option value="5" ${applicantsPerPage === 5 ? 'selected' : ''}>5</option>
                                <option value="10" ${applicantsPerPage === 10 ? 'selected' : ''}>10</option>
                                <option value="20" ${applicantsPerPage === 20 ? 'selected' : ''}>20</option>
                            </select>
                        </div>
                        <small class="text-muted mb-0">Showing ${startIndex + 1}-${Math.min(startIndex + paginatedList.length, list.length)} of ${list.length}</small>
                    </div>
                `;
            }

            $('#applicationsListContainer').html(html);
        }

        window.changeApplicantsPage = function(page) {
            renderApplicationsList(currentApplicationsTabStatus, page);
        };

        window.changeApplicantsPageSize = function(size) {
            const parsed = parseInt(size, 10);
            applicantsPerPage = [5, 10, 20].includes(parsed) ? parsed : 10;
            currentApplicationsPage = 1;
            renderApplicationsList(currentApplicationsTabStatus, currentApplicationsPage);
        };

        window.exportApplicantsByStatus = function(status) {
            if (!currentApplicationsJobId) {
                showToast('warning', 'Open a job applications board first.');
                return;
            }

            if (!['shortlisted', 'hired'].includes(status)) {
                showToast('error', 'Unsupported export type.');
                return;
            }

            const matching = (currentApplications || []).filter(app => app.status === status);
            if (matching.length === 0) {
                const label = status.charAt(0).toUpperCase() + status.slice(1);
                showToast('info', `No ${label} applicants to export.`);
                return;
            }

            const exportUrl = `/employer/jobs/${currentApplicationsJobId}/applications/export/${status}`;
            window.open(exportUrl, '_blank');
        };

        window.viewApplicantProfile = function(applicationId) {
            $('#viewApplicantProfileContent').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-3 text-muted mb-0">Loading applicant profile...</p>
                </div>
            `);
            $('#viewApplicantProfileModal').modal('show');

            window.employerApi.getApplicantProfile(applicationId)
                .done(function(response) {
                    if (!response.success) {
                        $('#viewApplicantProfileContent').html('<div class="alert alert-danger mb-0">Unable to load applicant profile.</div>');
                        return;
                    }

                    const applicant = response.application.applicant;
                    const skills = applicant.skills || [];
                    const education = applicant.education || [];
                    const experiences = applicant.work_experiences || [];

                    const avatar = applicant.profile_image_url
                        ? `<img src="${applicant.profile_image_url}" alt="${applicant.name}" class="rounded-circle border" style="width:120px;height:120px;object-fit:cover;">`
                        : `<div class="rounded-circle border d-flex align-items-center justify-content-center bg-light text-muted" style="width:120px;height:120px;"><i class="fas fa-user fa-3x"></i></div>`;

                    const attachments = [];
                    if (applicant.resume_url) {
                        attachments.push(`<a class="btn btn-outline-primary btn-sm" href="${applicant.resume_url}" target="_blank"><i class="fas fa-file-pdf me-1"></i> Resume</a>`);
                    }
                    if (applicant.profile_image_url) {
                        attachments.push(`<a class="btn btn-outline-secondary btn-sm" href="${applicant.profile_image_url}" target="_blank"><i class="fas fa-image me-1"></i> Photo</a>`);
                    }

                    const skillsHtml = skills.length
                        ? skills.map(skill => `<span class="badge bg-primary me-1 mb-1">${skill}</span>`).join('')
                        : '<span class="text-muted">No skills listed.</span>';

                    const educationHtml = education.length
                        ? education.map(item => `
                            <div class="border rounded-3 p-3 mb-2 bg-light">
                                <div class="fw-semibold">${item.institution_display || 'N/A'}</div>
                                <div class="text-muted small">${item.type_label || 'Education'}${item.level_label ? ' • ' + item.level_label : ''}</div>
                                <div class="small">${item.course_display || ''}</div>
                                <div class="small text-muted">${item.duration || ''}</div>
                            </div>
                        `).join('')
                        : '<div class="text-muted">No education details available.</div>';

                    const experienceHtml = experiences.length
                        ? experiences.map(item => `
                            <div class="border rounded-3 p-3 mb-2 bg-light">
                                <div class="fw-semibold">${item.position || 'N/A'}</div>
                                <div class="text-muted small">${item.company_name || 'N/A'}${item.location ? ' • ' + item.location : ''}</div>
                                <div class="small text-muted">${item.employment_type_label || ''}${item.duration ? ' • ' + item.duration : ''}</div>
                                <div class="small mt-2">${item.description || ''}</div>
                            </div>
                        `).join('')
                        : '<div class="text-muted">No work experience details available.</div>';

                    const html = `
                        <div class="row g-4">
                            <div class="col-lg-4">
                                <div class="text-center mb-3">
                                    ${avatar}
                                    <h4 class="mt-3 mb-1">${applicant.name}</h4>
                                    <div class="text-muted">${applicant.email}</div>
                                    <div class="mt-2"><span class="badge bg-info text-dark">${response.application.status}</span></div>
                                </div>

                                <div class="border rounded-3 p-3 mb-3 bg-light">
                                    <h6 class="fw-bold mb-3"><i class="fas fa-id-badge me-2"></i>Quick Details</h6>
                                    <div class="row g-2 small">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-between gap-3">
                                                <span class="text-muted">Phone</span>
                                                <span class="fw-semibold text-end">${applicant.phone || 'N/A'}</span>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="d-flex justify-content-between gap-3">
                                                <span class="text-muted">Gender</span>
                                                <span class="fw-semibold text-end">${applicant.gender_label || applicant.gender || 'N/A'}</span>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="d-flex justify-content-between gap-3">
                                                <span class="text-muted">Age</span>
                                                <span class="fw-semibold text-end">${applicant.age || 'N/A'}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="border rounded-3 p-3 mb-3">
                                    <h6 class="fw-bold mb-3"><i class="fas fa-paperclip me-2"></i>Attachments</h6>
                                    <div class="d-flex flex-wrap gap-2">
                                        ${attachments.length ? attachments.join('') : '<span class="text-muted">No attachments available.</span>'}
                                    </div>
                                </div>

                                <div class="border rounded-3 p-3">
                                    <h6 class="fw-bold mb-3"><i class="fas fa-user-tag me-2"></i>Basic Info</h6>
                                    <div class="small mb-2"><strong>Phone:</strong> ${applicant.phone || 'N/A'}</div>
                                    <div class="small mb-2"><strong>Gender:</strong> ${applicant.gender_label || applicant.gender || 'N/A'}</div>
                                    <div class="small mb-2"><strong>Age:</strong> ${applicant.age || 'N/A'}</div>
                                    <div class="small mb-2"><strong>Address:</strong> ${applicant.address || 'N/A'}</div>
                                    <div class="small mb-2"><strong>Region:</strong> ${applicant.region || 'N/A'}</div>
                                    <div class="small mb-2"><strong>District:</strong> ${applicant.district || 'N/A'}</div>
                                    <div class="small mb-2"><strong>Ward:</strong> ${applicant.ward || 'N/A'}</div>
                                    <div class="small"><strong>Shehia:</strong> ${applicant.shehia || 'N/A'}</div>
                                </div>
                            </div>

                            <div class="col-lg-8">
                                <div class="border rounded-3 p-3 mb-3">
                                    <h6 class="fw-bold mb-3"><i class="fas fa-align-left me-2"></i>Cover Letter / Bio</h6>
                                    <p class="mb-0">${response.application.cover_letter || applicant.bio || 'No cover letter or bio provided.'}</p>
                                </div>

                                <div class="border rounded-3 p-3 mb-3">
                                    <h6 class="fw-bold mb-3"><i class="fas fa-cogs me-2"></i>Skills</h6>
                                    ${skillsHtml}
                                </div>

                                <div class="border rounded-3 p-3 mb-3">
                                    <h6 class="fw-bold mb-3"><i class="fas fa-graduation-cap me-2"></i>Education</h6>
                                    ${educationHtml}
                                </div>

                                <div class="border rounded-3 p-3">
                                    <h6 class="fw-bold mb-3"><i class="fas fa-briefcase me-2"></i>Work Experience</h6>
                                    ${experienceHtml}
                                </div>
                            </div>
                        </div>
                    `;

                    $('#viewApplicantProfileContent').html(html);
                })
                .fail(function() {
                    $('#viewApplicantProfileContent').html('<div class="alert alert-danger mb-0">Error loading applicant profile.</div>');
                });
        };

        // Update application status
        window.updateApplicationStatus = function(applicationId, status) {
            window.employerApi.updateApplicationStatus(applicationId, status)
                .done(function(response) {
                    if (response.success) {
                        showToast('success', 'Application status updated!');
                        if (currentApplicationsJobId) {
                            viewApplications(currentApplicationsJobId, currentApplicationsJobTitle, currentApplicationsTabStatus);
                        }
                    }
                })
                .fail(function() {
                    showToast('error', 'Error updating application status.');
                });
        };

        // Edit job
        window.editJob = function(id) {
            window.employerApi.getJob(id)
                .done(function(response) {
                    if (response.success) {
                        const job = response.job;
                        // Pre-set the ID so showPostJobForm does not run a reset that clears editors.
                        $('#jobId').val(job.id);
                        showPostJobForm();
                        populateForm(job);
                        showToast('info', 'Edit the job details and save.');
                    }
                })
                .fail(function() {
                    showToast('error', 'Error loading job details for editing.');
                });
        };

        function populateForm(job) {
            $('#jobId').val(job.id);
            $('#title').val(job.title);
            $('#company_name').val(job.company_name);
            $('#location').val(job.location);
            $('#employment_type').val(normalizeEmploymentType(job.employment_type));
            $('#posts').val(job.posts || '');
            if (summernoteAvailable) {
                $('#description').summernote('code', job.description || '');
                $('#requirements').summernote('code', job.requirements || '');
            } else {
                $('#description').val(job.description || '');
                $('#requirements').val(job.requirements || '');
            }
            syncSummernoteFields();
            $('#benefits').val(job.benefits || '');
            $('#application_deadline').val(job.application_deadline || '');
            $('#attachment').val('');
            if (job.has_attachment && job.attachment_url) {
                $('#existingAttachmentInfo').html(`<small class="text-muted">Current file: <a href="${job.attachment_url}" target="_blank" rel="noopener">${job.attachment_name || 'View attachment'}</a></small>`);
                $('#removeAttachmentWrapper').show();
            } else {
                $('#existingAttachmentInfo').html('<small class="text-muted">No attachment uploaded for this job.</small>');
                $('#removeAttachmentWrapper').hide();
            }
            $('#remove_attachment').prop('checked', false);
            $('#is_active').prop('checked', job.is_active);
            $('#submitBtnText').text('Update Job');
            $('#formTitle').text('Edit Job');
            
            if (!$('#formMethod').length) {
                $('#jobForm').append('<input type="hidden" id="formMethod" name="_method" value="PUT">');
            } else {
                $('#formMethod').val('PUT');
            }
        }

        // Toggle job status
        window.toggleStatus = function(id) {
            if (confirm('Are you sure you want to toggle the status of this job?')) {
                window.employerApi.toggleJobStatus(id)
                    .done(function(response) {
                        if (response.success) {
                            showToast('success', response.message);
                            loadAllJobs();
                            loadDashboardStats();
                            loadRecentJobs();
                        }
                    })
                    .fail(function() {
                        showToast('error', 'Error toggling job status.');
                    });
            }
        };

        // Delete job
        window.deleteJob = function(id) {
            if (confirm('Are you sure you want to delete this job? This action cannot be undone.')) {
                window.employerApi.deleteJob(id)
                    .done(function(response) {
                        if (response.success) {
                            showToast('success', response.message);
                            loadAllJobs();
                            loadDashboardStats();
                            loadRecentJobs();
                        }
                    })
                    .fail(function() {
                        showToast('error', 'Error deleting job.');
                    });
            }
        };

        // Navigation functions
        function showDashboard() {
            $('#dashboardContent').show();
            $('#postJobContent').hide();
            $('#myJobsContent').hide();
            $('#applicationsContent').hide();
            loadDashboardStats();
            loadRecentJobs();
            // Update active nav
            $('.nav-link').removeClass('active');
            $('#dashboardLink').addClass('active');
        }

        function showPostJobForm() {
            $('#dashboardContent').hide();
            $('#postJobContent').show();
            $('#myJobsContent').hide();
            $('#applicationsContent').hide();
            // Update active nav
            $('.nav-link').removeClass('active');
            $('#postJobLink').addClass('active');
            
            if (!$('#jobId').val()) {
                $('#jobForm')[0].reset();
                resetSummernoteFields();
                $('#submitBtnText').text('Post Job');
                $('#formMethod').val('POST');
                $('#jobId').val('');
                $('#formTitle').text('Post New Job');
                $('#existingAttachmentInfo').html('');
                $('#remove_attachment').prop('checked', false);
                $('#removeAttachmentWrapper').hide();
            }
        }

        function showMyJobs() {
            $('#dashboardContent').hide();
            $('#postJobContent').hide();
            $('#myJobsContent').show();
            $('#applicationsContent').hide();
            loadAllJobs();
            // Update active nav
            $('.nav-link').removeClass('active');
            $('#myJobsLink').addClass('active');
        }

        function showApplicationsBoard() {
            $('#dashboardContent').hide();
            $('#postJobContent').hide();
            $('#myJobsContent').hide();
            $('#applicationsContent').show();
            $('.nav-link').removeClass('active');
            $('#myJobsLink').addClass('active');
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
    
