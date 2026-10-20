<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>Employer Dashboard - JobPortal</title>
    <link rel="icon" type="image/png" href="{{ asset('images/emblem.png') }}">
    <link rel="preload" href="{{ asset('webfonts/fa-solid-900.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('webfonts/fa-regular-400.woff2') }}" as="font" type="font/woff2" crossorigin>
    
     <link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('css/all.min.css')}}" rel="stylesheet">
    <!-- Summernote assets are loaded via Vite in resources/js/employer/dashboard.js -->
    <link href="{{ asset('css/fonts.css') }}" rel="stylesheet">
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
            --dark: #1E293B;
            --gray: #64748B;
            --light: #F8FAFC;
        }
* {
    font-family: 'Inter', sans-serif;
}


html {
    font-size: 14px;
}


body {
    background: #F1F5F9;
    overflow-x: hidden;
    font-size: 0.875rem; /* 14px */
    line-height: 1.5;
}

        /* Sidebar Styles */
        .sidebar {
            background: linear-gradient(180deg, #0F172A 0%, #1E293B 100%);
            min-height: 100vh;
            padding: 25px 20px;
            position: sticky;
            top: 0;
            box-shadow: 4px 0 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .sidebar .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 15px;
            margin-bottom: 5px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            padding-bottom: 10px;
        }


         .sidebar .brand .brand-logo {
            width: 140px;
            height: auto;
            border-radius: 8px;
        }
        .sidebar .brand i {
            font-size: 2rem;
            background: linear-gradient(135deg, #818CF8, #C084FC);
            padding: 10px;
            border-radius: 12px;
            color: white;
        }

        .sidebar .brand h4 {
            color: white;
            font-weight: 700;
            margin: 0;
            font-size: 1.2rem;
            letter-spacing: -0.5px;
        }

        .sidebar .brand span {
            color: #818CF8;
        }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 12px 18px;
            border-radius: 12px;
            margin-bottom: 5px;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar .nav-link i {
            width: 20px;
            font-size: 1.1rem;
        }

        .sidebar .nav-link:hover {
            color: white;
            background: rgba(255,255,255,0.08);
            transform: translateX(5px);
        }

        .sidebar .nav-link.active {
            color: white;
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.3), rgba(124, 58, 237, 0.3));
            border: 1px solid rgba(129, 140, 248, 0.2);
        }

        .sidebar .nav-link.active i {
            color: #818CF8;
        }

        .sidebar .nav-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            margin: 15px 0;
        }

        /* Main Content */
        .main-content {
            padding: 30px 35px;
            overflow: visible;
        }

        /* Top Header with User Profile */
        .top-header {
            background: white;
            padding: 15px 30px;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            border: 1px solid rgba(0,0,0,0.04);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            position: relative;
            z-index: 1200;
            overflow: visible;
        }

        .top-header .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .top-header .header-left .greeting {
            font-size: 0.95rem;
            color: #64748B;
        }

        .top-header .header-left .greeting strong {
            color: #0F172A;
            font-weight: 700;
        }

        .top-header .header-left .greeting .role-badge {
            background: linear-gradient(135deg, #059669, #10B981);
            color: white;
            padding: 3px 14px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-left: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            vertical-align: middle;
        }

        /* User Profile Dropdown in Top Right */
        .user-profile-dropdown {
            position: relative;
            cursor: pointer;
            z-index: 1201;
        }

        .user-profile-dropdown .profile-trigger {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 16px 8px 12px;
            border-radius: 50px;
            background: #F8FAFC;
            border: 2px solid #E2E8F0;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
        }

        .user-profile-dropdown .profile-trigger:hover {
            border-color: #4F46E5;
            background: #EEF2FF;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.15);
        }

        .user-profile-dropdown .profile-trigger .avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4F46E5, #7C3AED);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .user-profile-dropdown .profile-trigger .user-info-text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .user-profile-dropdown .profile-trigger .user-info-text .user-name {
            font-weight: 600;
            color: #0F172A;
            font-size: 0.9rem;
        }

        .user-profile-dropdown .profile-trigger .user-info-text .user-role {
            font-size: 0.75rem;
            color: #64748B;
        }

        .user-profile-dropdown .profile-trigger .dropdown-arrow {
            color: #94A3B8;
            font-size: 0.8rem;
            transition: transform 0.3s ease;
        }

        .user-profile-dropdown:hover .dropdown-arrow {
            transform: rotate(180deg);
        }

        /* Dropdown Menu */
        .dropdown-menu-custom {
            position: absolute;
            top: calc(100% + 12px);
            right: 0;
            min-width: 220px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            border: 1px solid rgba(0,0,0,0.06);
            padding: 8px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 1300;
        }

        .user-profile-dropdown:hover .dropdown-menu-custom {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-menu-custom .dropdown-item {
            padding: 10px 16px;
            border-radius: 8px;
            color: #1E293B;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            cursor: pointer;
        }

        .dropdown-menu-custom .dropdown-item:hover {
            background: #F1F5F9;
            color: #4F46E5;
        }

        .dropdown-menu-custom .dropdown-item i {
            width: 18px;
            color: #64748B;
        }

        .dropdown-menu-custom .dropdown-item:hover i {
            color: #4F46E5;
        }

        .dropdown-menu-custom .dropdown-divider {
            height: 1px;
            background: #E2E8F0;
            margin: 6px 12px;
        }

        .dropdown-menu-custom .dropdown-item.text-danger {
            color: #EF4444;
        }

        .dropdown-menu-custom .dropdown-item.text-danger:hover {
            background: #FEE2E2;
            color: #DC2626;
        }

        .dropdown-menu-custom .dropdown-item.text-danger i {
            color: #EF4444;
        }

        /* Page Header */
        .page-header {
            background: white;
            padding: 20px 30px;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            border: 1px solid rgba(0,0,0,0.04);
        }

        .page-header h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0F172A;
            margin: 0;
        }

        .page-header h1 small {
            font-size: 0.9rem;
            font-weight: 400;
            color: #64748B;
            display: block;
        }

        .page-header .header-actions {
            display: flex;
            gap: 10px;
            align-items: center;
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

        /* Stat Cards */
        .stat-card {
            border: none;
            border-radius: 16px;
            padding: 25px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
            transform: rotate(45deg);
        }

        .stat-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .stat-card .stat-icon {
            width: 55px;
            height: 55px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
        }

        .stat-card .stat-icon.primary {
            background: linear-gradient(135deg, #4F46E5, #818CF8);
        }

        .stat-card .stat-icon.success {
            background: linear-gradient(135deg, #10B981, #34D399);
        }

        .stat-card .stat-icon.warning {
            background: linear-gradient(135deg, #F59E0B, #FBBF24);
        }

        .stat-card .stat-number {
            font-size: 2.2rem;
            font-weight: 800;
            color: white;
            position: relative;
            z-index: 1;
            margin: 0;
            line-height: 1.2;
        }

        .stat-card .stat-label {
            color: rgba(255,255,255,0.85);
            font-weight: 500;
            font-size: 0.95rem;
            position: relative;
            z-index: 1;
            margin: 0;
        }

        .stat-card .stat-change {
            position: relative;
            z-index: 1;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 20px;
            background: rgba(255,255,255,0.2);
            color: white;
            font-size: 0.8rem;
            font-weight: 600;
            margin-top: 10px;
        }

        /* Job Cards */
        .job-card {
            border: 1px solid rgba(0,0,0,0.06);
            border-radius: 16px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            overflow: hidden;
        }

        .job-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.08);
            border-color: rgba(79, 70, 229, 0.1);
        }

        .job-card .card-header-custom {
            padding: 18px 24px;
            background: linear-gradient(135deg, #F8FAFC, #F1F5F9);
            border-bottom: 1px solid rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .job-card .card-header-custom .job-title {
            font-weight: 700;
            color: #0F172A;
            font-size: 1.1rem;
            margin: 0;
        }

        .job-card .card-header-custom .job-company {
            color: #64748B;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .job-card .card-body-custom {
            padding: 20px 24px;
        }

        .job-card .job-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 12px;
        }

        .job-card .job-meta span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #64748B;
            font-size: 0.9rem;
        }

        .job-card .job-meta span i {
            color: #4F46E5;
            width: 16px;
        }

        .job-card .job-description {
            color: #475569;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .job-card .job-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            padding-top: 15px;
            border-top: 1px solid rgba(0,0,0,0.05);
        }

        .status-badge {
            padding: 5px 16px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .status-badge.active {
            background: #D1FAE5;
            color: #065F46;
        }

        .status-badge.inactive {
            background: #FEE2E2;
            color: #991B1B;
        }

        .status-badge.pending {
            background: #FEF3C7;
            color: #92400E;
        }

        .status-badge.approved {
            background: #DBEAFE;
            color: #1E40AF;
        }

        .status-badge.rejected {
            background: #FEE2E2;
            color: #991B1B;
        }

        .job-actions {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .job-actions .btn-action {
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.2s ease;
            border: none;
        }

        .job-actions .btn-action:hover {
            transform: translateY(-2px);
        }

        .btn-action.view {
            background: #E0E7FF;
            color: #4F46E5;
        }

        .btn-action.view:hover {
            background: #C7D2FE;
        }

        .btn-action.edit {
            background: #FEF3C7;
            color: #D97706;
        }

        .btn-action.edit:hover {
            background: #FDE68A;
        }

        .btn-action.applications {
            background: #D1FAE5;
            color: #059669;
        }

        .btn-action.applications:hover {
            background: #A7F3D0;
        }

        .btn-action.toggle {
            background: #DBEAFE;
            color: #2563EB;
        }

        .btn-action.toggle:hover {
            background: #BFDBFE;
        }

        .btn-action.delete {
            background: #FEE2E2;
            color: #DC2626;
        }

        .btn-action.delete:hover {
            background: #FECACA;
        }

        /* Form Styles */
        .form-card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            border: 1px solid rgba(0,0,0,0.06);
        }

        .form-card .form-label {
            font-weight: 600;
            color: #0F172A;
            font-size: 0.5rem;
        }

        .form-card .form-control {
            border-radius: 10px;
            border: 2px solid #E2E8F0;
            padding: 12px 16px;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .form-card .form-control:focus {
            border-color: #4F46E5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        .form-card .form-control.is-invalid {
            border-color: #EF4444;
        }

        .form-card .form-control.is-invalid:focus {
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
        }

        .form-card .note-editor {
            border: 2px solid #E2E8F0;
            border-radius: 10px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .form-card .note-editor:focus-within {
            border-color: #4F46E5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        .form-card .note-editor.is-invalid {
            border-color: #EF4444;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
        }

        .form-card .form-check-input:checked {
            background-color: #4F46E5;
            border-color: #4F46E5;
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

        /* Modal */
        .modal-content {
            border-radius: 16px;
            border: none;
            box-shadow: 0 25px 60px rgba(0,0,0,0.2);
        }

        .modal-header {
            border-bottom: 1px solid rgba(0,0,0,0.06);
            padding: 20px 25px;
        }

        .modal-header .modal-title {
            font-weight: 700;
            color: #0F172A;
        }

        .modal-body {
            padding: 25px;
        }

        .modal-footer {
            border-top: 1px solid rgba(0,0,0,0.06);
            padding: 15px 25px;
        }

        /* Loading Spinner */
        .loading-spinner {
            display: none;
            text-align: center;
            padding: 30px;
        }

        .loading-spinner .spinner-border {
            width: 50px;
            height: 50px;
            border-width: 3px;
            color: #4F46E5;
        }

        /* Section Header */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .section-header h4 {
            font-weight: 700;
            color: #0F172A;
            margin: 0;
        }

        .section-header h4 i {
            color: #4F46E5;
            margin-right: 10px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 16px;
            border: 2px dashed #E2E8F0;
        }

        .empty-state i {
            font-size: 4rem;
            color: #CBD5E1;
            margin-bottom: 20px;
        }

        .empty-state h5 {
            color: #0F172A;
            font-weight: 600;
        }

        .empty-state p {
            color: #64748B;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeInUp 0.5s ease forwards;
        }

        .stat-card {
            animation: fadeInUp 0.6s ease forwards;
        }

        .stat-card:nth-child(2) { animation-delay: 0.1s; }
        .stat-card:nth-child(3) { animation-delay: 0.2s; }
        .stat-card:nth-child(4) { animation-delay: 0.3s; }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                min-height: auto;
                position: relative;
            }
            .main-content {
                padding: 20px;
            }
            .top-header {
                flex-direction: column;
                align-items: stretch;
                text-align: center;
            }
            .top-header .header-left {
                justify-content: center;
            }
            .user-profile-dropdown .profile-trigger {
                justify-content: center;
            }
            .page-header {
                flex-direction: column;
                align-items: stretch;
                text-align: center;
            }
            .page-header .header-actions {
                justify-content: center;
            }
            .stat-card .stat-number {
                font-size: 1.8rem;
            }
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #F1F5F9;
        }
        ::-webkit-scrollbar-thumb {
            background: #CBD5E1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94A3B8;
        }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <nav class="col-md-2 d-md-block sidebar">
                <div class="position-sticky">
                     <div class="brand">
                        <img src="{{ asset('images/emblem.png') }}" alt="Logo" class="brand-logo">
                        <!-- <h4>Jobseeker<span></span></h4> -->
                    
                     </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#" id="dashboardLink">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="postJobLink">
                                <i class="fas fa-plus-circle"></i> Post New Job
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="myJobsLink">
                                <i class="fas fa-briefcase"></i> My Jobs
                            </a>
                        </li>
                        <li class="nav-divider"></li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-10 ms-sm-auto main-content">
                <!-- Top Header with User Profile -->
                <div class="top-header animate-fade-in">
                    <div class="header-left">
                        <div class="greeting">
                            <i class="fas fa-wave text-primary me-2"></i>
                            Welcome back, <strong>{{ Auth::user()->name }}</strong>
                            <span class="role-badge">Employer</span>
                        </div>
                    </div>
                    <div class="user-profile-dropdown">
                        <div class="profile-trigger">
                            <div class="avatar">{{ substr(Auth::user()->name, 0, 1) }}</div>
                            <div class="user-info-text">
                                <span class="user-name">{{ Auth::user()->name }}</span>
                                <span class="user-role"><i class="fas fa-briefcase me-1"></i> Employer</span>
                            </div>
                            <i class="fas fa-chevron-down dropdown-arrow"></i>
                        </div> 
                        <div class="dropdown-menu-custom">
                            <a href="#" class="dropdown-item" onclick="event.preventDefault();">
                                <i class="fas fa-user"></i> My Profile
                            </a>
                            <a href="#" class="dropdown-item" onclick="event.preventDefault();">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>

                <div id="contentArea">
                    <!-- Dashboard Content -->
                    <div id="dashboardContent">
                        <!-- Page Header -->
                        <div class="page-header animate-fade-in">
                            <div>
                                <h1>
                                    Dashboard
                                    <small>Manage your job listings and applications</small>
                                </h1>
                            </div>
                            <div class="header-actions">
                                <button class="btn btn-primary-gradient" id="quickPostBtn">
                                    <i class="fas fa-plus me-2"></i> Post New Job
                                </button>
                            </div>
                        </div>

                        <!-- Stats -->
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="stat-card" style="background: linear-gradient(100deg, #4F46E5, #7C3AED);">
                                    <div class="stat-icon primary">
                                        <i class="fas fa-briefcase"></i>
                                    </div>
                                    <h2 class="stat-number" id="totalJobs">0</h2>
                                    <p class="stat-label">Total Jobs Posted</p>
                                    <span class="stat-change">
                                        <i class="fas fa-arrow-up"></i> All time
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card" style="background: linear-gradient(135deg, #059669, #10B981);">
                                    <div class="stat-icon success">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <h2 class="stat-number" id="activeJobs">0</h2>
                                    <p class="stat-label">Active Jobs</p>
                                    <span class="stat-change">
                                        <i class="fas fa-check"></i> Currently active
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card" style="background: linear-gradient(135deg, #D97706, #F59E0B);">
                                    <div class="stat-icon warning">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <h2 class="stat-number" id="pendingJobs">0</h2>
                                    <p class="stat-label">Pending Approval</p>
                                    <span class="stat-change">
                                        <i class="fas fa-hourglass-half"></i> Awaiting review
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Jobs -->
                        <div class="mt-5">
                            <div class="section-header">
                                <h4><i class="fas fa-history"></i> Recent Jobs</h4>
                            </div>
                            <div id="recentJobsList"></div>
                        </div>
                    </div>

                    <!-- Post Job Form -->
                    <div id="postJobContent" style="display: none;">
                        <div class="page-header animate-fade-in">
                            <div>
                                <h1>
                                    <span id="formTitle">Post New Job</span>
                                    <small>Create a new job listing</small>
                                </h1>
                            </div>
                            <div class="header-actions">
                                <button class="btn btn-secondary-gradient" id="backToDashboardBtn">
                                    <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                                </button>
                            </div>
                        </div>

                        <div class="form-card">
                            <form id="jobForm" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" id="jobId" name="job_id">
                                <input type="hidden" id="formMethod" name="_method" value="POST">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="title" class="form-label">Job Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="title" name="title" placeholder="e.g., Driver" required>
                                        <div class="invalid-feedback" id="titleError"></div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="company_name" class="form-label">Company Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="company_name" name="company_name" placeholder="Your company name" required>
                                        <div class="invalid-feedback" id="company_nameError"></div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="location" class="form-label">Location <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="location" name="location" placeholder="e.g., Doha, Qatar" required>
                                        <div class="invalid-feedback" id="locationError"></div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="employment_type" class="form-label">Employment Type <span class="text-danger">*</span></label>
                                        <select class="form-control" id="employment_type" name="employment_type" required>
                                            <option value="">Select type</option>
                                            <option value="permanent-contract">Permanent Contract</option>
                                            <option value="temporary-contract">Temporary Contract</option>
                                            <option value="part-time-contract">Part-time Contract</option>
                                            <option value="daily-paid-contract">Daily Paid Contract</option>
                                            
                                        </select>
                                        <div class="invalid-feedback" id="employment_typeError"></div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="posts" class="form-label">Number of Posts</label>
                                        <input type="text" class="form-control" id="posts" name="posts" placeholder="e.g., 20">
                                        <div class="invalid-feedback" id="postsError"></div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="application_deadline" class="form-label">Application Deadline</label>
                                        <input type="date" class="form-control" id="application_deadline" name="application_deadline">
                                        <div class="invalid-feedback" id="application_deadlineError"></div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Job Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="description" name="description" rows="5" placeholder="Describe the job role, responsibilities, and expectations..." required></textarea>
                                    <div class="invalid-feedback" id="descriptionError"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="requirements" class="form-label">Requirements <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="requirements" name="requirements" rows="4" placeholder="List the required skills, qualifications, and experience..." required></textarea>
                                    <div class="invalid-feedback" id="requirementsError"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="attachment" class="form-label">Job Attachment</label>
                                    <input type="file" class="form-control" id="attachment" name="attachment" accept=".pdf">
                                    <small class="text-muted d-block mt-1">Allowed: PDF only (max 5MB)</small>
                                    <div class="invalid-feedback" id="attachmentError"></div>
                                    <div id="existingAttachmentInfo" class="mt-2"></div>
                                    <div class="form-check mt-2" id="removeAttachmentWrapper" style="display: none;">
                                        <input type="checkbox" class="form-check-input" id="remove_attachment" name="remove_attachment" value="1">
                                        <label class="form-check-label" for="remove_attachment">Remove existing attachment</label>
                                    </div>
                                </div>

                               

                                <div class="mb-4 form-check">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" checked>
                                    <label class="form-check-label" for="is_active">
                                        <i class="fas fa-eye text-success me-1"></i> Active (visible to Admin)
                                    </label>
                                </div>

                                <div id="loadingSpinner" class="loading-spinner">
                                    <div class="spinner-border" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2 text-muted">Processing your request...</p>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary-gradient" id="submitBtn">
                                        <i class="fas fa-save me-2"></i> <span id="submitBtnText">Post Job</span>
                                    </button>
                                    <button type="reset" class="btn btn-secondary-gradient">
                                        <i class="fas fa-undo me-2"></i> Reset
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- My Jobs List -->
                    <div id="myJobsContent" style="display: none;">
                        <div class="page-header animate-fade-in">
                            <div>
                                <h1>
                                    My Jobs
                                    <small>Manage all your job listings</small>
                                </h1>
                            </div>
                            <div class="header-actions">
                                <button class="btn btn-primary-gradient" id="postNewFromListBtn">
                                    <i class="fas fa-plus me-2"></i> Post New Job
                                </button>
                            </div>
                        </div>
                        <div id="jobsList"></div>
                    </div>

                    <!-- Applications Board -->
                    <div id="applicationsContent" style="display: none;">
                        <div class="page-header animate-fade-in">
                            <div>
                                <h1>
                                    Applications Board
                                    <small>Review all candidates for the selected job</small>
                                </h1>
                            </div>
                            <div class="header-actions">
                                <button class="btn btn-secondary-gradient" id="backToMyJobsBtn">
                                    <i class="fas fa-arrow-left me-2"></i> Back to My Jobs
                                </button>
                            </div>
                        </div>

                        <div class="form-card">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                                <h5 class="mb-0" id="applicationsJobTitle">Applications</h5>
                                <span class="badge bg-dark" id="applicationsCountBadge">0 Applicants</span>
                            </div>

                            <div class="d-flex flex-wrap gap-2 mb-3" id="applicationsTabs">
                                <button type="button" class="btn btn-primary-gradient" data-status="submitted">All Applicants (Submitted)</button>
                                <button type="button" class="btn btn-secondary-gradient" data-status="shortlisted">Shortlisted</button>
                                <button type="button" class="btn btn-secondary-gradient" data-status="rejected">Rejected</button>
                                <button type="button" class="btn btn-secondary-gradient" data-status="hired">Hired</button>
                            </div>

                            <div id="applicationsListContainer"></div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container"></div>

    <!-- View Job Modal -->
    <div class="modal fade" id="viewJobModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-info-circle text-primary me-2"></i> Job Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="viewJobContent"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary-gradient" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Applicant Profile Modal -->
    <div class="modal fade" id="viewApplicantProfileModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-circle text-primary me-2"></i> Applicant Profile
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="viewApplicantProfileContent"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary-gradient" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    
    <!-- Summernote assets are loaded via Vite in resources/js/employer/dashboard.js -->

    
</body>
</html>
