<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>Complete Profile - JobPortal</title>
    <link rel="icon" type="image/png" href="{{ asset('images/emblem.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4F46E5;
            --primary-light: #818CF8;
            --primary-dark: #3730A3;
            --secondary: #7C3AED;
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
            --info: #3B82F6;
        }

        * {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #F1F5F9;
            min-height: 100vh;
            padding: 40px 20px;
        }

        .profile-container {
            max-width: 900px;
            margin: 0 auto;
        }

        .profile-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        }

        .profile-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .profile-header .icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #4F46E5, #7C3AED);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            margin-bottom: 15px;
            box-shadow: 0 10px 30px rgba(79, 70, 229, 0.3);
        }

        .profile-header h2 {
            font-weight: 800;
            color: #0F172A;
            margin: 0;
        }

        .profile-header p {
            color: #64748B;
            margin-top: 5px;
        }

        /* Progress Bar */
        .progress-container {
            margin-bottom: 30px;
        }

        .progress-container .steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-bottom: 20px;
        }

        .progress-container .steps::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background: #E2E8F0;
            transform: translateY(-50%);
            z-index: 0;
        }

        .progress-container .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 1;
            cursor: pointer;
        }

        .progress-container .step .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #E2E8F0;
            color: #94A3B8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .progress-container .step.active .step-number {
            background: #4F46E5;
            color: white;
            border-color: #4F46E5;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
        }

        .progress-container .step.completed .step-number {
            background: #10B981;
            color: white;
            border-color: #10B981;
        }

        .progress-container .step .step-label {
            margin-top: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            color: #94A3B8;
            text-align: center;
        }

        .progress-container .step.active .step-label {
            color: #4F46E5;
        }

        .progress-container .step.completed .step-label {
            color: #10B981;
        }

        .progress-container .progress-bar {
            height: 6px;
            border-radius: 3px;
            background: #E2E8F0;
            overflow: hidden;
        }

        .progress-container .progress-bar .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #4F46E5, #7C3AED);
            border-radius: 3px;
            transition: width 0.5s ease;
            width: 33.33%;
        }

        /* Step Content */
        .step-content {
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .step-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .step-content .section-title {
            font-weight: 700;
            color: #0F172A;
            font-size: 1.1rem;
            margin-bottom: 20px;
        }

        .step-content .section-title i {
            color: #4F46E5;
            margin-right: 10px;
        }

        .step-content .form-label {
            font-weight: 600;
            color: #0F172A;
            font-size: 0.9rem;
        }

        .step-content .form-control,
        .step-content .form-select {
            border-radius: 10px;
            border: 2px solid #E2E8F0;
            padding: 10px 14px;
            transition: all 0.3s ease;
        }

        .step-content .form-control:focus,
        .step-content .form-select:focus {
            border-color: #4F46E5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        .step-content .form-control.is-invalid,
        .step-content .form-select.is-invalid {
            border-color: #EF4444;
        }

        .step-content .form-control.is-invalid:focus {
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
        }

        .step-content .invalid-feedback {
            font-size: 0.8rem;
        }

        /* Education/Experience Cards */
        .entry-card {
            background: #F8FAFC;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 12px;
            border: 1px solid #E2E8F0;
            transition: all 0.3s ease;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .entry-card:hover {
            border-color: #4F46E5;
            box-shadow: 0 2px 10px rgba(79, 70, 229, 0.08);
        }

        .entry-card .entry-info h6 {
            font-weight: 700;
            color: #0F172A;
            margin: 0;
        }

        .entry-card .entry-info p {
            color: #64748B;
            font-size: 0.85rem;
            margin: 2px 0 0;
        }

        .entry-card .entry-actions .btn {
            padding: 4px 10px;
            font-size: 0.75rem;
            border-radius: 6px;
        }

        .btn-primary-gradient {
            background: linear-gradient(135deg, #4F46E5, #7C3AED);
            border: none;
            padding: 10px 24px;
            border-radius: 12px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
        }

        .btn-primary-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.4);
            color: white;
        }

        .btn-secondary-gradient {
            background: linear-gradient(135deg, #64748B, #475569);
            border: none;
            padding: 10px 24px;
            border-radius: 12px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-secondary-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(100, 116, 139, 0.3);
            color: white;
        }

        .btn-outline-primary {
            border: 2px solid #4F46E5;
            color: #4F46E5;
            background: transparent;
            padding: 6px 16px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background: #4F46E5;
            color: white;
        }

        .btn-sm {
            padding: 4px 12px;
            font-size: 0.8rem;
            border-radius: 6px;
        }

        .btn-danger-sm {
            background: #FEE2E2;
            color: #DC2626;
            border: none;
            padding: 4px 12px;
            font-size: 0.75rem;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-danger-sm:hover {
            background: #FECACA;
            color: #991B1B;
        }

        .btn-edit-sm {
            background: #DBEAFE;
            color: #2563EB;
            border: none;
            padding: 4px 12px;
            font-size: 0.75rem;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-edit-sm:hover {
            background: #BFDBFE;
            color: #1E40AF;
        }

        .empty-state {
            text-align: center;
            padding: 30px;
            color: #94A3B8;
        }

        .empty-state i {
            font-size: 2.5rem;
            margin-bottom: 10px;
            display: block;
        }

        /* Navigation Buttons */
        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #E2E8F0;
        }

        /* Toast */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }

        .toast {
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            border: none;
            min-width: 320px;
        }

        .toast .toast-body {
            padding: 16px 20px;
            font-weight: 500;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .profile-card {
                padding: 20px;
            }

            .progress-container .step .step-label {
                font-size: 0.6rem;
            }

            .progress-container .step .step-number {
                width: 30px;
                height: 30px;
                font-size: 0.7rem;
            }

            .entry-card {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            .entry-card .entry-actions {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-card">
            <div class="profile-header">
                <div class="icon">
                    <i class="fas fa-user-edit"></i>
                </div>
                <h2>Complete Your Profile</h2>
                <p>Please fill in all required information to start applying for jobs</p>
            </div>

            <!-- Progress Bar -->
            <div class="progress-container">
                <div class="steps">
                    <div class="step active" data-step="1">
                        <div class="step-number">1</div>
                        <span class="step-label">General Info</span>
                    </div>
                    <div class="step" data-step="2">
                        <div class="step-number">2</div>
                        <span class="step-label">Education</span>
                    </div>
                    <div class="step" data-step="3">
                        <div class="step-number">3</div>
                        <span class="step-label">Work Experience</span>
                    </div>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill" style="width: 33.33%;"></div>
                </div>
            </div>

            <!-- Form -->
            <form id="profileForm" enctype="multipart/form-data">
                @csrf

                <!-- Step 1: General Information -->
                <div class="step-content active" id="step1">
                    <h5 class="section-title"><i class="fas fa-user"></i> General Information</h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="resident_number" class="form-label">Resident Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="resident_number" name="resident_number" placeholder="Your Zanzibari Resident Number" required>
                                <button type="button" class="btn btn-outline-primary" id="btnVerifyZanId">
                                    <i class="fas fa-id-card me-1"></i> Verify ZANID
                                </button>
                            </div>
                            <div class="invalid-feedback d-block" id="residentNumberError"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ Auth::user()->name }}" required>
                            <div class="invalid-feedback" id="nameError"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ Auth::user()->email }}" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ Auth::user()->phone }}">
                            <div class="invalid-feedback" id="phoneError"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" id="gender" name="gender">
                                <option value="">Select Gender</option>
                                <option value="male" {{ Auth::user()->gender == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ Auth::user()->gender == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ Auth::user()->gender == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ Auth::user()->date_of_birth ? Auth::user()->date_of_birth->format('Y-m-d') : '' }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="address" name="address" value="{{ Auth::user()->address }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="bio" class="form-label">Professional Summary</label>
                        <textarea class="form-control" id="bio" name="bio" rows="3">{{ Auth::user()->bio }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="skills" class="form-label">Skills <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="skills" name="skills" value="{{ Auth::user()->skills }}" placeholder="e.g., PHP, Laravel, JavaScript">
                            <small class="text-muted">Separate skills with commas</small>
                            <div class="invalid-feedback" id="skillsError"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="experience_years" class="form-label">Years of Experience</label>
                            <select class="form-select" id="experience_years" name="experience_years">
                                <option value="0" {{ Auth::user()->experience_years == 0 ? 'selected' : '' }}>Fresher (0 years)</option>
                                <option value="1" {{ Auth::user()->experience_years == 1 ? 'selected' : '' }}>1 Year</option>
                                <option value="2" {{ Auth::user()->experience_years == 2 ? 'selected' : '' }}>2 Years</option>
                                <option value="3" {{ Auth::user()->experience_years == 3 ? 'selected' : '' }}>3 Years</option>
                                <option value="4" {{ Auth::user()->experience_years == 4 ? 'selected' : '' }}>4 Years</option>
                                <option value="5" {{ Auth::user()->experience_years == 5 ? 'selected' : '' }}>5 Years</option>
                                <option value="6" {{ Auth::user()->experience_years == 6 ? 'selected' : '' }}>6 Years</option>
                                <option value="7" {{ Auth::user()->experience_years == 7 ? 'selected' : '' }}>7 Years</option>
                                <option value="8" {{ Auth::user()->experience_years == 8 ? 'selected' : '' }}>8 Years</option>
                                <option value="9" {{ Auth::user()->experience_years == 9 ? 'selected' : '' }}>9 Years</option>
                                <option value="10" {{ Auth::user()->experience_years == 10 ? 'selected' : '' }}>10+ Years</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="linkedin_url" class="form-label"><i class="fab fa-linkedin"></i> LinkedIn</label>
                            <input type="url" class="form-control" id="linkedin_url" name="linkedin_url" value="{{ Auth::user()->linkedin_url }}" placeholder="https://linkedin.com/in/username">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="github_url" class="form-label"><i class="fab fa-github"></i> GitHub</label>
                            <input type="url" class="form-control" id="github_url" name="github_url" value="{{ Auth::user()->github_url }}" placeholder="https://github.com/username">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="website_url" class="form-label"><i class="fas fa-globe"></i> Website</label>
                            <input type="url" class="form-control" id="website_url" name="website_url" value="{{ Auth::user()->website_url }}" placeholder="https://yourwebsite.com">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="resume" class="form-label"><i class="fas fa-file-pdf"></i> Resume / CV</label>
                            <input type="file" class="form-control" id="resume" name="resume" accept=".pdf,.doc,.docx">
                            <div id="resumePreviewContainer" class="mt-2">
                                @php
                                    $currentProfile = Auth::user()->jobseekerProfile;
                                @endphp
                                @if($currentProfile && $currentProfile->resume)
                                    @php
                                        $resumeUrl = route('profile.resume.view');
                                        $resumeExt = strtolower(pathinfo($currentProfile->resume, PATHINFO_EXTENSION));
                                        $isPdfResume = $resumeExt === 'pdf';
                                    @endphp
                                    <small class="text-muted d-block mb-2">Current: <a href="{{ $resumeUrl }}" target="_blank">{{ $currentProfile->resume }}</a></small>
                                    @if($isPdfResume)
                                        <iframe src="{{ $resumeUrl }}#toolbar=0" class="w-100 border rounded" style="height: 220px;" title="Current CV Preview"></iframe>
                                    @else
                                        <small class="text-muted d-block">Preview not available for .{{ $resumeExt }} files. Open the file using the link above.</small>
                                    @endif
                                @else
                                    <small class="text-muted d-block">No resume uploaded yet.</small>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="profile_image" class="form-label"><i class="fas fa-image"></i> Profile Photo</label>
                            <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*">
                            <div id="profileImagePreviewContainer" class="mt-2">
                                @if($currentProfile && $currentProfile->profile_image)
                                    @php
                                        $profileImageUrl = route('profile.image.view');
                                    @endphp
                                    <small class="text-muted d-block mb-2">Current photo uploaded</small>
                                    <a href="{{ $profileImageUrl }}" target="_blank" class="d-inline-block">
                                        <img src="{{ $profileImageUrl }}" alt="Current profile photo" class="img-thumbnail" style="max-height: 220px; width: auto; object-fit: cover;">
                                    </a>
                                @else
                                    <small class="text-muted d-block">No profile photo uploaded yet.</small>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="nav-buttons">
                        <div></div>
                        <button type="button" class="btn btn-primary-gradient" onclick="nextStep(2)">
                            Next <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 2: Education -->
                <div class="step-content" id="step2">
                    <h5 class="section-title"><i class="fas fa-graduation-cap"></i> Education</h5>
                    
                    <div id="educationList">
                        <!-- Education entries will be loaded here -->
                    </div>

                    <button type="button" class="btn btn-outline-primary mt-2" onclick="showEducationForm()">
                        <i class="fas fa-plus me-2"></i> Add Education
                    </button>

                    <div id="educationFormContainer" style="display: none; margin-top: 15px;">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edu_institution" class="form-label">Institution <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edu_institution" placeholder="University/College Name">
                                <div class="invalid-feedback" id="edu_institutionError"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edu_degree" class="form-label">Degree <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edu_degree" placeholder="e.g., Bachelor of Science">
                                <div class="invalid-feedback" id="edu_degreeError"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edu_field" class="form-label">Field of Study</label>
                                <input type="text" class="form-control" id="edu_field" placeholder="e.g., Computer Science">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edu_description" class="form-label">Description</label>
                                <textarea class="form-control" id="edu_description" rows="2" placeholder="Additional details about your education"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="edu_start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="edu_start_date">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="edu_end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="edu_end_date">
                            </div>
                            <div class="col-md-4 mb-3 d-flex align-items-center">
                                <div class="form-check mt-3">
                                    <input type="checkbox" class="form-check-input" id="edu_is_current">
                                    <label class="form-check-label" for="edu_is_current">Currently Studying</label>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="edu_id">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-primary-gradient" onclick="saveEducation()">
                                <i class="fas fa-save me-2"></i> Save Education
                            </button>
                            <button type="button" class="btn btn-secondary-gradient" onclick="hideEducationForm()">
                                Cancel
                            </button>
                        </div>
                    </div>

                    <div class="nav-buttons">
                        <button type="button" class="btn btn-secondary-gradient" onclick="prevStep(1)">
                            <i class="fas fa-arrow-left me-2"></i> Back
                        </button>
                        <button type="button" class="btn btn-primary-gradient" onclick="nextStep(3)">
                            Next <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>

                <!-- Step 3: Work Experience -->
                <div class="step-content" id="step3">
                    <h5 class="section-title"><i class="fas fa-briefcase"></i> Work Experience</h5>

                    <div id="experienceList">
                        <!-- Work experiences will be loaded here -->
                    </div>

                    <button type="button" class="btn btn-outline-primary mt-2" onclick="showExperienceForm()">
                        <i class="fas fa-plus me-2"></i> Add Work Experience
                    </button>

                    <div id="experienceFormContainer" style="display: none; margin-top: 15px;">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="exp_company" class="form-label">Company Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="exp_company" placeholder="Company Name">
                                <div class="invalid-feedback" id="exp_companyError"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="exp_position" class="form-label">Position <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="exp_position" placeholder="e.g., Software Engineer">
                                <div class="invalid-feedback" id="exp_positionError"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="exp_location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="exp_location" placeholder="City, Country">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="exp_type" class="form-label">Employment Type</label>
                                <select class="form-select" id="exp_type">
                                    <option value="">Select Type</option>
                                    <option value="full-time">Full Time</option>
                                    <option value="part-time">Part Time</option>
                                    <option value="contract">Contract</option>
                                    <option value="internship">Internship</option>
                                    <option value="freelance">Freelance</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="exp_description" class="form-label">Description</label>
                            <textarea class="form-control" id="exp_description" rows="2" placeholder="Describe your responsibilities and achievements"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="exp_start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="exp_start_date">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="exp_end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="exp_end_date">
                            </div>
                            <div class="col-md-4 mb-3 d-flex align-items-center">
                                <div class="form-check mt-3">
                                    <input type="checkbox" class="form-check-input" id="exp_is_current">
                                    <label class="form-check-label" for="exp_is_current">Currently Working</label>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="exp_id">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-primary-gradient" onclick="saveExperience()">
                                <i class="fas fa-save me-2"></i> Save Experience
                            </button>
                            <button type="button" class="btn btn-secondary-gradient" onclick="hideExperienceForm()">
                                Cancel
                            </button>
                        </div>
                    </div>

                    <div class="nav-buttons">
                        <button type="button" class="btn btn-secondary-gradient" onclick="prevStep(2)">
                            <i class="fas fa-arrow-left me-2"></i> Back
                        </button>
                        <button type="submit" class="btn btn-primary-gradient" id="submitProfileBtn">
                            <i class="fas fa-check me-2"></i> Complete Profile
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container"></div>

    <!-- Education Edit Modal -->
    <div class="modal fade" id="educationEditModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Education</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Form fields will be populated dynamically -->
                </div>
            </div>
        </div>
    </div>

    <div
        id="jobseekerCompleteProfileConfig"
        data-initial-resume-url="{{ optional(Auth::user()->jobseekerProfile)->resume ? route('profile.resume.view') : '' }}"
        data-initial-resume-name="{{ optional(Auth::user()->jobseekerProfile)->resume ?? '' }}"
        data-initial-profile-image-url="{{ optional(Auth::user()->jobseekerProfile)->profile_image ? route('profile.image.view') : '' }}"
        hidden
    ></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @vite(['resources/js/app.js'])
</body>
</html>
