<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite([ 'resources/css/app.css', 'resources/js/app.js' ])
    <title>Jobseeker Dashboard - JobPortal</title>
    <link rel="icon" type="image/png" href="{{ asset('images/emblem.png') }}">
    <link rel="preload" href="{{ asset('webfonts/fa-solid-900.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('webfonts/fa-regular-400.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/all.min.css') }}" rel="stylesheet">
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

/* Rest of your CSS... */

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

        .main-content {
            padding: 30px 35px;
            overflow: visible;
        }

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
            background: linear-gradient(135deg, #10B981, #34D399);
            color: white;
            padding: 3px 14px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-left: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

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

        .btn-success-gradient {
            background: linear-gradient(135deg, #059669, #10B981);
            border: none;
            padding: 8px 20px;
            border-radius: 10px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-success-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
            color: white;
        }

        .btn-outline-gradient {
            background: transparent;
            border: 2px solid #4F46E5;
            color: #4F46E5;
            padding: 8px 20px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-gradient:hover {
            background: linear-gradient(135deg, #4F46E5, #7C3AED);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.3);
        }

        .stat-card {
            border: none;
            border-radius: 16px;
            padding: 25px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            cursor: default;
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

        .stat-card .stat-icon.info {
            background: linear-gradient(135deg, #3B82F6, #60A5FA);
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
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-header h4 i {
            color: #4F46E5;
        }

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

        .status-badge.pending {
            background: #FEF3C7;
            color: #92400E;
        }

        .status-badge.submitted {
            background: #FEF3C7;
            color: #92400E;
        }

        .status-badge.reviewed {
            background: #DBEAFE;
            color: #1E40AF;
        }

        .status-badge.shortlisted {
            background: #D1FAE5;
            color: #065F46;
        }

        .status-badge.rejected {
            background: #FEE2E2;
            color: #991B1B;
        }

        .status-badge.hired {
            background: #D1FAE5;
            color: #065F46;
        }

        .status-badge.applied {
            background: #E0E7FF;
            color: #4F46E5;
        }

        .application-card {
            border-left: 4px solid #4F46E5;
            transition: all 0.3s ease;
        }

        .application-card:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .application-card.status-pending {
            border-left-color: #F59E0B;
        }

        .application-card.status-submitted {
            border-left-color: #F59E0B;
        }

        .application-card.status-reviewed {
            border-left-color: #3B82F6;
        }

        .application-card.status-shortlisted {
            border-left-color: #10B981;
        }

        .application-card.status-rejected {
            border-left-color: #EF4444;
        }

        .application-card.status-hired {
            border-left-color: #10B981;
        }

        .application-card .card-body {
            padding: 20px 24px;
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

        .btn-action.apply {
            background: #D1FAE5;
            color: #059669;
        }

        .btn-action.apply:hover {
            background: #A7F3D0;
        }

        .btn-action.applied {
            background: #DBEAFE;
            color: #2563EB;
        }

        .btn-action.withdraw {
            background: #FEE2E2;
            color: #DC2626;
        }

        .btn-action.withdraw:hover {
            background: #FECACA;
        }

        .filter-section {
            background: white;
            border-radius: 16px;
            padding: 20px 24px;
            margin-bottom: 20px;
            border: 1px solid rgba(0,0,0,0.06);
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }

        .filter-section .form-control,
        .filter-section .form-select {
            border-radius: 10px;
            border: 2px solid #E2E8F0;
            padding: 10px 16px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .filter-section .form-control:focus,
        .filter-section .form-select:focus {
            border-color: #4F46E5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

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

        .modal-body .form-control {
            border-radius: 10px;
            border: 2px solid #E2E8F0;
            padding: 10px 14px;
            transition: all 0.3s ease;
        }

        .modal-body .form-control:focus {
            border-color: #4F46E5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        .modal-body .form-label {
            font-weight: 600;
            color: #0F172A;
            font-size: 0.9rem;
        }

        .modal .invalid-feedback {
            font-size: 0.8rem;
            color: #EF4444;
        }

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

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
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

        .pulse-on-hover:hover {
            animation: pulse 0.5s ease;
        }

        .profile-container {
            background: white;
            border-radius: 20px;
            padding: 35px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            border: 1px solid rgba(0,0,0,0.06);
        }

        .profile-container .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .profile-container .profile-header .icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #4F46E5, #7C3AED);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.8rem;
            margin-bottom: 10px;
            box-shadow: 0 10px 30px rgba(79, 70, 229, 0.3);
        }

        .profile-container .profile-header h2 {
            font-weight: 800;
            color: #0F172A;
            margin: 0;
        }

        .profile-container .profile-header p {
            color: #64748B;
            margin-top: 5px;
        }

        .profile-container .profile-header .alert {
            margin-top: 15px;
        }

        .progress-container {
            margin-bottom: 30px;
        }

        .progress-container .steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-bottom: 15px;
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
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #E2E8F0;
            color: #94A3B8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
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
            margin-top: 6px;
            font-size: 0.7rem;
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
            width: 33%;
        }

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

        .step-content .invalid-feedback {
            font-size: 0.8rem;
        }

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

        .entry-card .entry-info small {
            color: #94A3B8;
        }

        .entry-card .entry-actions {
            display: flex;
            gap: 6px;
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

        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #E2E8F0;
        }

        .pagination-custom {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-top: 20px;
        }

        .pagination-custom .page-link {
            border-radius: 10px;
            border: 2px solid #E2E8F0;
            color: #0F172A;
            padding: 8px 16px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .pagination-custom .page-link:hover {
            background: #4F46E5;
            color: white;
            border-color: #4F46E5;
        }

        .pagination-custom .page-item.active .page-link {
            background: linear-gradient(135deg, #4F46E5, #7C3AED);
            border-color: #4F46E5;
            color: white;
        }

        .pagination-custom .page-item.disabled .page-link {
            color: #94A3B8;
            cursor: not-allowed;
        }

        .edu-conditional-fields {
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .edu-conditional-fields.show {
            display: block;
        }

        .skills-dropdown-container .selected-skills {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            min-height: 45px;
            padding: 8px;
            background: #F8FAFC;
            border-radius: 10px;
            border: 2px solid #E2E8F0;
            margin-top: 10px;
        }

        .skills-dropdown-container .selected-skills .badge {
            font-weight: 500;
            cursor: default;
            animation: fadeInUp 0.3s ease;
            padding: 8px 14px;
            font-size: 0.9rem;
        }

        .skills-dropdown-container .selected-skills .badge i {
            transition: opacity 0.2s ease;
            cursor: pointer;
        }

        .skills-dropdown-container .selected-skills .badge i:hover {
            opacity: 1 !important;
        }

        /* Profile Preview Styles */
        .profile-preview {
            background: #F8FAFC;
            border-radius: 16px;
            padding: 30px;
            border: 2px solid #E2E8F0;
            position: relative;
        }

        .profile-preview .preview-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #E2E8F0;
        }

        .profile-preview .preview-header h4 {
            font-weight: 700;
            color: #0F172A;
            margin: 0;
        }

        .profile-preview .preview-header .preview-actions {
            display: flex;
            gap: 10px;
        }

        .profile-preview .preview-section {
            margin-bottom: 20px;
        }

        .profile-preview .preview-section .preview-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #64748B;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .profile-preview .preview-section .preview-value {
            font-size: 1rem;
            color: #0F172A;
            font-weight: 500;
        }

        .profile-preview .preview-divider {
            height: 1px;
            background: #E2E8F0;
            margin: 15px 0;
        }

        .profile-preview .badge-preview {
            background: #DBEAFE;
            color: #1E40AF;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
        }

        .profile-preview .preview-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4F46E5, #7C3AED);
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            font-weight: 700;
        }

        .profile-preview .preview-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .profile-preview .status-badge-preview {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }

        .profile-preview .status-badge-preview.completed {
            background: #D1FAE5;
            color: #065F46;
        }

        .profile-preview .status-badge-preview.pending {
            background: #FEF3C7;
            color: #92400E;
        }

        .profile-preview .skill-tag {
            background: #4F46E5;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
            display: inline-block;
            margin: 3px;
        }

        .profile-preview .education-item {
            background: white;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 8px;
            border: 1px solid #E2E8F0;
        }

        .profile-preview .education-item .edu-title {
            font-weight: 600;
            color: #0F172A;
        }

        .profile-preview .education-item .edu-detail {
            font-size: 0.85rem;
            color: #64748B;
        }

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
            .filter-section .row > div {
                margin-bottom: 10px;
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
            .profile-container {
                padding: 20px;
            }
            .profile-preview {
                padding: 20px;
            }
            .profile-preview .preview-header {
                flex-direction: column;
                gap: 10px;
            }
        }

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

/* ============================================
   SKILLS STYLES
   ============================================ */

.skills-dropdown-container {
    background: #F8FAFC;
    border-radius: 12px;
    padding: 20px;
    border: 1px solid #E2E8F0;
}

.selected-skills {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    min-height: 50px;
    padding: 12px;
    background: white;
    border-radius: 10px;
    border: 2px solid #E2E8F0;
    margin-top: 12px;
    transition: border-color 0.3s ease;
}

.selected-skills:focus-within {
    border-color: #4F46E5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.selected-skills .empty-message {
    color: #94A3B8;
    font-size: 0.9rem;
    width: 100%;
    text-align: center;
    padding: 8px 0;
}

.skill-tag {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 14px;
    background: linear-gradient(135deg, #4F46E5, #7C3AED);
    color: white;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
    animation: fadeIn 0.3s ease;
    box-shadow: 0 2px 8px rgba(79, 70, 229, 0.2);
    transition: all 0.3s ease;
}

.skill-tag:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
}

.skill-tag.custom-skill {
    background: linear-gradient(135deg, #059669, #10B981);
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.2);
}

.skill-tag.custom-skill:hover {
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.skill-tag .remove-skill {
    cursor: pointer;
    opacity: 0.7;
    font-size: 0.75rem;
    transition: all 0.3s ease;
    padding: 2px;
    border-radius: 50%;
}

.skill-tag .remove-skill:hover {
    opacity: 1;
    transform: rotate(90deg);
    background: rgba(255, 255, 255, 0.2);
}

.skill-tag small {
    font-size: 0.65rem;
    opacity: 0.8;
    font-weight: 400;
}

#skillCount {
    font-size: 0.75rem;
    background: #E2E8F0;
    color: #475569;
    padding: 2px 10px;
    border-radius: 12px;
    font-weight: 600;
}

#customSkillsArea .input-group .form-control {
    border-radius: 10px 0 0 10px;
    border: 2px solid #E2E8F0;
}

#customSkillsArea .input-group .form-control:focus {
    border-color: #4F46E5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

#customSkillsArea .input-group .btn {
    border-radius: 0 10px 10px 0;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.9) translateY(-5px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

@keyframes slideDown {
    from {
        opacity: 0;
        max-height: 0;
    }
    to {
        opacity: 1;
        max-height: 200px;
    }
}

#customSkillsArea {
    animation: slideDown 0.3s ease;
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
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#" id="dashboardLink">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="browseJobsLink">
                                <i class="fas fa-search"></i> Browse Jobs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="myApplicationsLink">
                                <i class="fas fa-file-alt"></i> My Applications
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="profileLink">
                                <i class="fas fa-user"></i> Profile
                                @if(Auth::user()->profile_completed)
                                    <span class="badge bg-success ms-2"><i class="fas fa-check"></i></span>
                                @else
                                    <span class="badge bg-warning ms-2"><i class="fas fa-exclamation-triangle"></i></span>
                                @endif
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
                <!-- Top Header -->
                <div class="top-header animate-fade-in">
                    <div class="header-left">
                        <div class="greeting">
                            <i class="fas fa-wave text-primary me-2"></i>
                            Welcome back, <strong>{{ Auth::user()->name }}</strong>
                            <span class="role-badge"><i class="fas fa-user me-1"></i> Job Seeker</span>
                        </div>
                    </div>
                    <div class="user-profile-dropdown">
                        <div class="profile-trigger">
                            <div class="avatar">{{ substr(Auth::user()->name, 0, 1) }}</div>
                            <div class="user-info-text">
                                <span class="user-name">{{ Auth::user()->name }}</span>
                                <span class="user-role"><i class="fas fa-user me-1"></i> Job Seeker</span>
                            </div>
                            <i class="fas fa-chevron-down dropdown-arrow"></i>
                        </div>
                        <div class="dropdown-menu-custom">
                            <a href="#" class="dropdown-item" onclick="event.preventDefault(); showProfile();">
                                <i class="fas fa-user"></i> My Profile
                            </a>
                            <a href="#" class="dropdown-item" onclick="event.preventDefault();">
                                <i class="fas fa-cog"></i> Settings
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>

                <div id="contentArea">
                    <!-- Dashboard Content -->
                    <div id="dashboardContent">
                        <div class="page-header animate-fade-in">
                            <div>
                                <h1>
                                    Dashboard
                                    <small>Find your dream job and track applications</small>
                                </h1>
                            </div>
                            <div class="header-actions">
                                <button class="btn btn-primary-gradient" id="browseJobsBtn">
                                    <i class="fas fa-search me-2"></i> Browse Jobs
                                </button>
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="stat-card" style="background: linear-gradient(135deg, #4F46E5, #7C3AED);">
                                    <div class="stat-icon primary">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <h2 class="stat-number" id="totalApplications">0</h2>
                                    <p class="stat-label">Total Applications</p>
                                    <span class="stat-change">
                                        <i class="fas fa-arrow-up"></i> Applied
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-card" style="background: linear-gradient(135deg, #059669, #10B981);">
                                    <div class="stat-icon success">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <h2 class="stat-number" id="shortlistedApps">0</h2>
                                    <p class="stat-label">Shortlisted</p>
                                    <span class="stat-change">
                                        <i class="fas fa-check"></i> Selected
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="stat-card" style="background: linear-gradient(135deg, #D97706, #F59E0B);">
                                    <div class="stat-icon warning">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <h2 class="stat-number" id="pendingApps">0</h2>
                                    <p class="stat-label">Pending Review</p>
                                    <span class="stat-change">
                                        <i class="fas fa-hourglass-half"></i> Awaiting response
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5">
                            <div class="section-header">
                                <h4><i class="fas fa-fire text-danger"></i> Recent Job Openings</h4>
                            </div>
                            <div id="recentJobsList"></div>
                        </div>
                    </div>

                    <!-- Browse Jobs -->
                    <div id="browseJobsContent" style="display: none;">
                        <div class="page-header animate-fade-in">
                            <div>
                                <h1>
                                    Browse Jobs
                                    <small>Find the perfect job for you</small>
                                </h1>
                            </div>
                            <div class="header-actions">
                                <button class="btn btn-secondary-gradient" id="backToDashboardBtn">
                                    <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                                </button>
                            </div>
                        </div>

                        <div class="filter-section">
                            <div class="row align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label">Search Jobs</label>
                                    <input type="text" class="form-control" id="searchJob" placeholder="Search by title, company...">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Employment Type</label>
                                    <select class="form-select" id="employmentType">
                                        <option value="">All Types</option>
                                        <option value="full-time">Full Time</option>
                                        <option value="part-time">Part Time</option>
                                        <option value="contract">Contract</option>
                                        <option value="internship">Internship</option>
                                        <option value="remote">Remote</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Location</label>
                                    <input type="text" class="form-control" id="locationFilter" placeholder="City, State...">
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-primary-gradient w-100" id="searchJobsBtn">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="jobsList"></div>
                        <div id="paginationLinks" class="pagination-custom"></div>
                    </div>

                    <!-- My Applications -->
                    <div id="myApplicationsContent" style="display: none;">
                        <div class="page-header animate-fade-in">
                            <div>
                                <h1>
                                    My Applications
                                    <small>Track your job applications</small>
                                </h1>
                            </div>
                            <div class="header-actions">
                                <button class="btn btn-secondary-gradient" id="backToDashboardBtn2">
                                    <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                                </button>
                            </div>
                        </div>
                        <div id="applicationsList"></div>
                    </div>

                    <!-- Profile Content -->
                    <div id="profileContent" style="display: none;">
                        @php
                            $profile = Auth::user()->jobseekerProfile ?? null;
                            $regions = \App\Models\Region::where('is_active', true)->orderBy('name')->get();
                            $districts = collect();
                            $wards = collect();
                            $shehias = collect();
                            
                            if ($profile && $profile->region_id) {
                                $districts = \App\Models\District::where('region_id', $profile->region_id)
                                    ->where('is_active', true)
                                    ->orderBy('name')
                                    ->get();
                            }
                            if ($profile && $profile->district_id) {
                                $wards = \App\Models\Ward::where('district_id', $profile->district_id)
                                    ->where('is_active', true)
                                    ->orderBy('name')
                                    ->get();
                            }
                            if ($profile && $profile->ward_id) {
                                $shehias = \App\Models\Shehia::where('ward_id', $profile->ward_id)
                                    ->where('is_active', true)
                                    ->orderBy('name')
                                    ->get();
                            }
                        @endphp
                        
                        <div class="page-header animate-fade-in">
                            <div>
                                <h1>
                                    My Profile
                                    <small>Add your personal Information, Education and Skills you have</small>
                                </h1>
                            </div>
                            <div class="header-actions">
                                <button class="btn btn-secondary-gradient" id="backToProfileDashboardBtn">
                                    <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                                </button>
                            </div>
                        </div>

                        <div class="profile-container">
                            <div class="profile-header">
                                <div class="icon">
                                    <i class="fas fa-user-edit"></i>
                                </div>
                                <h2>Complete Your Profile</h2>
                                <p>Please fill in all required information to start applying for jobs</p>
                                @if($profile && !$profile->is_completed)
                                    <div class="alert alert-warning mt-3">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>Please complete your profile to apply for jobs.</strong>
                                    </div>
                                @endif
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
                                        <span class="step-label">Skills</span>
                                    </div>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" id="progressFill" style="width: 33%;"></div>
                                </div>
                            </div>

                            <form id="profileForm" enctype="multipart/form-data">
                                @csrf

                                <!-- Step 1: General Information -->
                                <div class="step-content active" id="step1">
                                    <h5 class="section-title"><i class="fas fa-user"></i> General Information</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="resident_number" class="form-label">Zanzibar Resident Number <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" class="form-control bg-light" id="resident_number" name="resident_number"
                                                       value="{{ $profile->resident_number ?: Auth::user()->zan_id }}" readonly>
                                            </div>
                                            <div class="invalid-feedback d-block" id="residentNumberError"></div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name" 
                                                   value="{{ Auth::user()->name }}" required>
                                            <div class="invalid-feedback" id="nameError"></div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email"
                                                   value="{{ Auth::user()->email }}" readonly>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="phone" name="phone" 
                                                   value="{{ $profile->phone ?: Auth::user()->phone }}">
                                            <div class="invalid-feedback" id="phoneError"></div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="gender" class="form-label">Gender</label>
                                            <select class="form-select" id="gender" name="gender">
                                                <option value="">Select Gender</option>
                                                <option value="male" {{ ($profile->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                                                <option value="female" {{ ($profile->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
                                                
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="date_of_birth" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                                                   value="{{ ($profile && $profile->date_of_birth) ? \Illuminate\Support\Carbon::parse($profile->date_of_birth)->format('Y-m-d') : '' }}" required>
                                            <div class="invalid-feedback" id="date_of_birthError"></div>
                                        </div>
                                    </div>

                                    

                                    <div class="row">
                                        
                                        <div class="col-md-6 mb-3">
                                            <label for="disability" class="form-label">Disability Status</label>
                                            <select class="form-select" id="disability" name="disability">
                                                <option value="">Select Option</option>
                                                <option value="no" {{ ($profile->disability ?? '') == 'no' ? 'selected' : '' }}>No</option>
                                                <option value="yes" {{ ($profile->disability ?? '') == 'yes' ? 'selected' : '' }}>Yes</option>
                                            </select>
                                        </div>
                                         
                                        <div class="col-md-6 mb-3" id="disabilityDescriptionRow" style="display: {{ ($profile->disability ?? '') === 'yes' ? 'block' : 'none' }};">
                                            <label for="disability_description" class="form-label">Please describe your disability</label>
                                            <textarea class="form-control" id="disability_description" name="disability_description" rows="2" maxlength="500" placeholder="Briefly describe your disability...">{{ $profile->disability_description ?? '' }}</textarea>
                                            <div class="invalid-feedback" id="disabilityDescriptionError"></div>
                                        </div>
                                    
                                    </div>

                                    

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="has_passport" class="form-label">Do You Have a Passport?</label>
                                            <select class="form-select" id="has_passport" name="has_passport">
                                                <option value="">Select Option</option>
                                                <option value="no" {{ ($profile->has_passport ?? '') == 'no' ? 'selected' : '' }}>No</option>
                                                <option value="yes" {{ ($profile->has_passport ?? '') == 'yes' ? 'selected' : '' }}>Yes</option>
                                            </select>
                                            <div class="invalid-feedback" id="has_passportError"></div>
                                        </div>
                                        <div class="col-md-6 mb-3" id="placeOfIssueWrapper" style="display: {{ ($profile->has_passport ?? '') === 'yes' ? 'block' : 'none' }};">
                                            <label for="place_of_issue" class="form-label">Place of Issue</label>
                                            <select class="form-select" id="place_of_issue" name="place_of_issue">
                                                <option value="">Select Place of Issue</option>
                                                <option value="tanzania_mainland" {{ (($profile->place_of_issue ?? '') === 'tanzania_mainland' || ($profile->post_code ?? '') === 'tanzania_mainland') ? 'selected' : '' }}>Tanzania Mainland</option>
                                                <option value="zanzibar" {{ (($profile->place_of_issue ?? '') === 'zanzibar' || ($profile->post_code ?? '') === 'zanzibar') ? 'selected' : '' }}>Zanzibar</option>
                                            </select>
                                            <div class="invalid-feedback" id="place_of_issueError"></div>
                                        </div>
                                    </div>

                                    <div class="row" id="passportFieldsRow" style="display: {{ ($profile->has_passport ?? '') === 'yes' ? 'flex' : 'none' }};">
                                        <div class="col-md-6 mb-3">
                                            <label for="passport_number" class="form-label">Passport Number</label>
                                            <input type="text" class="form-control" id="passport_number" name="passport_number" value="{{ $profile->passport_number ?? '' }}" maxlength="50">
                                            <div class="invalid-feedback" id="passport_numberError"></div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="passport_expiry_date" class="form-label">Passport Expiry Date</label>
                                            <input type="date" class="form-control" id="passport_expiry_date" name="passport_expiry_date" value="{{ ($profile && $profile->passport_expiry_date) ? \Illuminate\Support\Carbon::parse($profile->passport_expiry_date)->format('Y-m-d') : '' }}">
                                            <div class="invalid-feedback" id="passport_expiry_dateError"></div>
                                        </div>
                                    </div>

                                    <!-- Location Fields -->
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="region" class="form-label">Region <span class="text-danger">*</span></label>
                                            <select class="form-select" id="region" name="region_id" required>
                                                <option value="">Select Region</option>
                                                @foreach($regions as $region)
                                                    <option value="{{ $region->id }}" 
                                                        {{ (isset($profile) && $profile->region_id == $region->id) ? 'selected' : '' }}>
                                                        {{ $region->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" id="regionError"></div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="district" class="form-label">District <span class="text-danger">*</span></label>
                                            <select class="form-select" id="district" name="district_id" required>
                                                <option value="">Select District</option>
                                                @foreach($districts as $district)
                                                    <option value="{{ $district->id }}"
                                                        {{ (isset($profile) && $profile->district_id == $district->id) ? 'selected' : '' }}>
                                                        {{ $district->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" id="districtError"></div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="ward" class="form-label">Ward <span class="text-danger">*</span></label>
                                            <select class="form-select" id="ward" name="ward_id" required>
                                                <option value="">Select Ward</option>
                                                @foreach($wards as $ward)
                                                    <option value="{{ $ward->id }}"
                                                        {{ (isset($profile) && $profile->ward_id == $ward->id) ? 'selected' : '' }}>
                                                        {{ $ward->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" id="wardError"></div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="shehia" class="form-label">Shehia <span class="text-danger">*</span></label>
                                            <select class="form-select" id="shehia" name="shehia_id" required>
                                                <option value="">Select Shehia</option>
                                                @foreach($shehias as $shehia)
                                                    <option value="{{ $shehia->id }}"
                                                        {{ (isset($profile) && $profile->shehia_id == $shehia->id) ? 'selected' : '' }}>
                                                        {{ $shehia->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" id="shehiaError"></div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="resume" class="form-label"><i class="fas fa-file-pdf"></i> Resume / CV</label>
                                            <input type="file" class="form-control" id="resume" name="resume" accept=".pdf,.doc,.docx">
                                            <div id="resumePreviewContainer" class="mt-2">
                                                @if($profile && $profile->resume)
                                                    @php
                                                        $resumeUrl = route('profile.resume.view');
                                                        $resumeExt = strtolower(pathinfo($profile->resume, PATHINFO_EXTENSION));
                                                        $isPdfResume = $resumeExt === 'pdf';
                                                    @endphp
                                                    <small class="text-muted d-block mb-2">click to view <a href="{{ $resumeUrl }}" target="_blank">{{ $profile->resume }}</a></small>
                                                    
                                                @else
                                                    <small class="text-muted d-block">No resume uploaded yet.</small>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="profile_image" class="form-label"><i class="fas fa-image"></i> Profile Photo</label>
                                            <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*">
                                            <div id="profileImagePreviewContainer" class="mt-2">
                                                @if($profile && $profile->profile_image)
                                                    @php
                                                        $profileImageUrl = route('profile.image.view');
                                                    @endphp
                                                    <small class="text-muted d-block mb-2">Current photo uploaded</small>
                                                    <a href="{{ $profileImageUrl }}" target="_blank" class="d-inline-block">
                                                        <img src="{{ $profileImageUrl }}" alt="Current profile photo" class="img-thumbnail" style="max-height: 130px; width: auto; object-fit: cover;">
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
                                    <div id="educationList"></div>
                                    <button type="button" class="btn btn-outline-gradient mt-2" onclick="showEducationForm()">
                                        <i class="fas fa-plus me-2"></i> Add Education
                                    </button>
                                    <div id="educationFormContainer" style="display: none; margin-top: 15px;">
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <input type="hidden" id="edu_id">
                                                <label for="edu_type" class="form-label">Education Type <span class="text-danger">*</span></label>
                                                <select class="form-select"   name="edu_type" id="edu_type">
                                                    <option value="">Select Education Type</option>
                                                    <option value="informal">No Formal Education</option>
                                                    <option value="primary">Primary Education</option>
                                                    <option value="secondary">Secondary Education</option>
                                                    <option value="university">University</option>
                                                </select>
                                                <div class="invalid-feedback" id="edu_typeError"></div>
                                            </div>
                                        </div>

                                        <!-- Informal Education Fields -->
                                        <div id="edu_informal_fields" class="edu-conditional-fields">
                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <label for="edu_informal_description" class="form-label">Description <span class="text-danger">*</span></label>
                                                    <textarea class="form-control" id="edu_informal_description" rows="3" placeholder="Describe your informal education/training"></textarea>
                                                    <div class="invalid-feedback" id="edu_informal_descriptionError"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Primary & Secondary Education Fields -->
                                        <div id="edu_primary_secondary_fields" class="edu-conditional-fields">
                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <label for="edu_school_name" class="form-label">School Name <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="edu_school_name" placeholder="School name">
                                                    <div class="invalid-feedback" id="edu_school_nameError"></div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="edu_start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control" id="edu_start_date">
                                                    <div class="invalid-feedback" id="edu_start_dateError"></div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="edu_end_date" class="form-label">End Date</label>
                                                    <input type="date" class="form-control" id="edu_end_date">
                                                    <div class="invalid-feedback" id="edu_end_dateError"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- University Education Fields -->
                                        <div id="edu_university_fields" class="edu-conditional-fields">
                                            <div class="row">
                                                <div class="col-md-12 mb-3">
                                                    <label for="edu_institution" class="form-label">Institution <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="edu_institution" placeholder="University/College name">
                                                    <div class="invalid-feedback" id="edu_institutionError"></div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="edu_course" class="form-label">Course <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="edu_course" placeholder="e.g., Computer Science">
                                                    <div class="invalid-feedback" id="edu_courseError"></div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="edu_level" class="form-label">Level <span class="text-danger">*</span></label>
                                                    <select class="form-select" id="edu_level">
                                                        <option value="">Select Level</option>
                                                        <option value="diploma">Diploma</option>
                                                        <option value="bachelor">Bachelor</option>
                                                        <option value="master">Master</option>
                                                        <option value="phd">PhD</option>
                                                    </select>
                                                    <div class="invalid-feedback" id="edu_levelError"></div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="edu_university_start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                                                    <input type="date" class="form-control" id="edu_university_start_date">
                                                    <div class="invalid-feedback" id="edu_university_start_dateError"></div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label for="edu_university_end_date" class="form-label">End Date</label>
                                                    <input type="date" class="form-control" id="edu_university_end_date">
                                                    <div class="invalid-feedback" id="edu_university_end_dateError"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex gap-2 mt-3">
                                            <button type="button" class="btn btn-primary-gradient" onclick="saveEducation()">
                                                <i class="fas fa-save me-2"></i> Save Education
                                            </button>
                                            <button type="button" class="btn btn-secondary-gradient" onclick="hideEducationForm()">
                                                <i class="fas fa-times me-2"></i> Cancel
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

                                <!-- Step 3: Skills (Final Step) -->
<div class="step-content" id="step3">
    <h5 class="section-title"><i class="fas fa-code"></i> Jobseeker Skills</h5>
    
    <div class="alert alert-info d-flex align-items-center">
        <i class="fas fa-info-circle me-2 fs-5"></i> Select Skills you have from the dropdown below. You can also add custom skills if not listed.
       
    </div>

    <div class="skills-dropdown-container">
        <label for="skillsDropdown" class="form-label fw-semibold">
             Select Skills 
            <span class="text-danger">*</span>
            <span class="badge bg-light ms-2" id="skillCount">0</span>
        </label>
        
        <select class="form-select" id="skillsDropdown">
            <option value="">-- Select a skill --</option>
            <option value="Airfare">Airfare</option>
            <option value="Aluminium and glass">Aluminium and glass</option>
            <option value="Beauty/Makeup">Beauty/Makeup</option>
            <option value="Boat engine servicing and maintenance">Boat engine servicing and maintenance</option>
            <option value="Cook/Chef">Cook/Chef</option>
            <option value="Driver">Driver</option>
            <option value="Electricians in sea vessels">Electricians in sea vessels</option>
            <option value="Elimu ya Muziki na Maonyesho">Elimu ya Muziki na Maonyesho</option>
            <option value="Fishing Practices">Fishing Practices</option>
            <option value="Fundi Bomba (Plumbing)">Fundi Bomba (Plumbing)</option>
            <option value="Fundi Charahani (Tailoring)">Fundi Charahani (Tailoring)</option>
            <option value="Fundi Seremala">Fundi Seremala</option>
            <option value="Gardener">Gardener</option>
            <option value="Gasmen">Gasmen</option>
            <option value="Heavy Machinery Operator">Heavy Machinery Operator</option>
            <option value="House Keeping">House Keeping</option>
            <option value="ICT">ICT</option>
            <option value="Lugha ya Alama">Lugha ya Alama</option>
            <option value="Maintenance/Repair of ships">Maintenance/Repair of ships</option>
            <option value="Marine Safety and Lifesaving Operations">Marine Safety and Lifesaving Operations</option>
            <option value="Mason">Mason</option>
            <option value="Mechanics">Mechanics</option>
            <option value="Mwalimu wa Madrasa">Mwalimu wa Madrasa</option>
            <option value="Imam wa Msikiti">Imam wa Msikiti</option>
            <option value="Mhudumu wa Nyumbani (Housemaid)">Mhudumu wa Nyumbani (Housemaid)</option>
            <option value="Mpiga Picha (Photographer)">Mpiga Picha (Photographer)</option>
            <option value="Mtaalam wa Lugha (Linguistic)">Mtaalam wa Lugha (Linguistic)</option>
            <option value="Plumbing in sea vessels">Plumbing in sea vessels</option>
            <option value="Pool trainers">Pool trainers</option>
            <option value="Recreational Boating and Water Sports">Recreational Boating and Water Sports</option>
            <option value="Refrigeration in sea vessels">Refrigeration in sea vessels</option>
            <option value="Sailors">Sailors</option>
            <option value="Seaman">Seaman</option>
            <option value="Tour Guide">Tour Guide</option>
            <option value="Umeme wa Magari">Umeme wa Magari</option>
            <option value="Umeme wa Majumbani">Umeme wa Majumbani</option>
            <option value="Undersea and aluminum welding skills">Undersea and aluminum welding skills</option>
            <option value="Upambaji (Decoration)">Upambaji (Decoration)</option>
            <option value="Vibarua wa Ujenzi">Vibarua wa Ujenzi</option>
            <option value="Welding">Welding</option>
            <option value="__other__">-- Others (Add Custom Skill) --</option>
        </select>
        
        
        <!-- Custom Skills Input -->
        <div id="customSkillsArea" style="display: none; margin-top: 12px;">
            <div class="input-group">
                <input type="text" class="form-control" id="customSkillsInput" 
                       placeholder="Enter skills separated by commas (e.g., Graphic Design, Photography)">
                <button class="btn btn-primary-gradient" type="button" id="addCustomSkillsBtn">
                    <i class="fas fa-plus me-1"></i> Add
                </button>
            </div>
            <small class="text-muted mt-1 d-block">
                <i class="fas fa-info-circle me-1"></i> Type skills separated by commas, then click "Add"
            </small>
        </div>
        
        <!-- Selected Skills Tags -->
        <div class="selected-skills" id="selectedSkills">
            @if(isset($profile) && $profile->skills)
                @php $skillsArray = array_map('trim', explode(',', $profile->skills)); @endphp
                @foreach($skillsArray as $skill)
                    @if(!empty($skill))
                        <span class="skill-tag" data-skill="{{ $skill }}">
                            {{ $skill }}
                            <i class="fas fa-times remove-skill"></i>
                        </span>
                    @endif
                @endforeach
            @endif
        </div>
        
        <!-- Hidden input for form submission -->
        <input type="hidden" id="skillsInput" name="skills" value="{{ $profile->skills ?? '' }}">
        <div class="invalid-feedback" id="skillsInputError"></div>
    </div>

    <div class="nav-buttons">
        <button type="button" class="btn btn-secondary-gradient" onclick="prevStep(2)">
            <i class="fas fa-arrow-left me-2"></i> Back
        </button>
        <div class="d-flex gap-2">
            <!-- <button type="button" class="btn btn-outline-gradient d-none" id="previewProfileBtn" onclick="showProfilePreview()">
                <i class="fas fa-eye me-2"></i> Preview Profile
            </button> -->
            <button type="button" class="btn btn-success-gradient" id="saveSkillsBtn" onclick="saveSkills()">
                <i class="fas fa-save me-2"></i> Save Skills
            </button>
            <button type="submit" class="btn btn-primary-gradient" id="submitProfileBtn">
                <i class="fas fa-check me-2"></i> Complete Profile
            </button>
        </div>
    </div>
</div>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <div
        id="jobseekerDashboardConfig"
        data-profile-completed="{{ ($profile && $profile->profile_completed) ? '1' : '0' }}"
        data-user-name="{{ Auth::user()->name }}"
        data-user-email="{{ Auth::user()->email }}"
        data-profile-image-view-url="{{ route('profile.image.view') }}"
        data-saved-profile-image-url="{{ $profile && $profile->profile_image ? route('profile.image.view') : '' }}"
        data-saved-resume-url="{{ $profile && $profile->resume ? route('profile.resume.view') : '' }}"
        data-saved-resume-name="{{ $profile->resume ?? '' }}"
        hidden
    ></div>

    <!-- Toast Container -->
    <div class="toast-container"></div>

    <!-- Profile Preview Modal -->
    <div class="modal fade" id="profilePreviewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-id-card text-primary me-2"></i> Profile Preview
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="profilePreviewContent">
                    <!-- Profile preview will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary-gradient" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Close
                    </button>
                    <button type="button" class="btn btn-primary-gradient" onclick="downloadProfile()">
                        <i class="fas fa-download me-2"></i> Download Profile
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Apply Job Modal -->
    <div class="modal fade" id="applyJobModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-paper-plane text-primary me-2"></i> Apply for Job
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="applyForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="applyJobId" name="job_id">
                        
                        <div class="mb-3">
                            <label for="cover_letter" class="form-label">
                                <i class="fas fa-file-alt text-primary me-1"></i> Upload Cover Letter (PDF) <span class="text-danger">*</span>
                            </label>
                            <input type="file" class="form-control" id="cover_letter" name="cover_letter" 
                                   accept=".pdf" required>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i> 
                                Please upload your cover letter in PDF format (Max size: 2MB)
                            </small>
                            <div class="invalid-feedback" id="cover_letterError"></div>
                        </div>
                        
                        <div id="fileInfo" style="display: none;">
                            <div class="alert alert-success d-flex align-items-center">
                                <i class="fas fa-file-pdf text-danger me-2"></i>
                                <span><strong>Selected File:</strong> <span id="fileName"></span></span>
                                <button type="button" class="btn-close ms-auto" onclick="removeFile()"></button>
                            </div>
                        </div>
                        
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Note:</strong> Your application will be submitted with your cover letter PDF.
                        </div>
                        
                        <div id="applyLoading" class="text-center mt-3" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Submitting your application...</p>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary-gradient" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-primary-gradient" id="submitApplication">
                        <i class="fas fa-paper-plane me-2"></i> Submit Application
                    </button>
                </div>
            </div>
        </div>
    </div>

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

    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
