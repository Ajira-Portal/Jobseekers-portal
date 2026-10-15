<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>Admin Dashboard - JobPortal</title>
    <link rel="icon" type="image/png" href="{{ asset('images/emblem.png') }}">
    <link rel="preload" href="{{ asset('webfonts/fa-solid-900.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('webfonts/fa-regular-400.woff2') }}" as="font" type="font/woff2" crossorigin>
     <link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('css/all.min.css')}}" rel="stylesheet">
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

        .sidebar .nav-item.dropdown-nav {
            margin-bottom: 5px;
        }

        .sidebar .nav-link.dropdown-toggle-nav {
            justify-content: space-between;
        }

        .sidebar .nav-link.dropdown-toggle-nav .nav-main {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar .nav-link.dropdown-toggle-nav .dropdown-caret {
            font-size: 0.8rem;
            transition: transform 0.25s ease;
        }

        .sidebar .nav-link.dropdown-toggle-nav[aria-expanded="true"] .dropdown-caret {
            transform: rotate(180deg);
        }

        .sidebar .nav-submenu {
            list-style: none;
            padding-left: 18px;
            margin: 6px 0 0;
        }

        .sidebar .nav-submenu .nav-link {
            padding: 10px 16px 10px 18px;
            font-size: 0.88rem;
            margin-bottom: 4px;
            background: rgba(255,255,255,0.03);
        }

        .sidebar .nav-submenu .nav-link.active,
        .sidebar .nav-submenu .nav-link:hover {
            background: rgba(255,255,255,0.1);
            transform: translateX(4px);
        }

        .sidebar .nav-link .badge-pending {
            background: #F59E0B;
            color: #0F172A;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            margin-left: auto;
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
            background: linear-gradient(135deg, #4F46E5, #7C3AED);
            color: white;
            padding: 3px 14px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            margin-left: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* User Profile Dropdown */
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
            flex-wrap: wrap;
        }

        /* Buttons */
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
            padding: 10px 24px;
            border-radius: 12px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
        }

        .btn-success-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
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

        .btn-outline-danger {
            background: transparent;
            border: 2px solid #EF4444;
            color: #EF4444;
            padding: 6px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }

        .btn-outline-danger:hover {
            background: #EF4444;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(239, 68, 68, 0.3);
        }

        /* Stat Cards */
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

        .stat-card .stat-icon.danger {
            background: linear-gradient(135deg, #EF4444, #F87171);
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
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-header h4 i {
            color: #4F46E5;
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

        /* Status Badges */
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

        .status-badge.employed {
            background: #DBEAFE;
            color: #1E40AF;
        }

        .status-badge.unemployed {
            background: #FEF3C7;
            color: #92400E;
        }

        .status-badge.warning {
            background: #FEF3C7;
            color: #92400E;
        }

        /* Role Badges */
        .role-badge-admin {
            background: #4F46E5;
            color: white;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .role-badge-employer {
            background: #10B981;
            color: white;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .role-badge-jobseeker {
            background: #3B82F6;
            color: white;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        /* Job Actions */
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

        .btn-action.approve {
            background: #D1FAE5;
            color: #059669;
        }

        .btn-action.approve:hover {
            background: #A7F3D0;
        }

        .btn-action.reject {
            background: #FEE2E2;
            color: #DC2626;
        }

        .btn-action.reject:hover {
            background: #FECACA;
        }

        .btn-action.delete {
            background: #FEE2E2;
            color: #DC2626;
        }

        .btn-action.delete:hover {
            background: #FECACA;
        }

        .btn-action.edit {
            background: #FEF3C7;
            color: #D97706;
        }

        .btn-action.edit:hover {
            background: #FDE68A;
        }

        .btn-action.toggle {
            background: #DBEAFE;
            color: #2563EB;
        }

        .btn-action.toggle:hover {
            background: #BFDBFE;
        }

        /* Table Styles */
        .table-responsive-custom {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            border: 1px solid rgba(0,0,0,0.06);
        }

        .table-responsive-custom .table {
            margin-bottom: 0;
        }

        .table-responsive-custom .table thead th {
            background: #F8FAFC;
            border-bottom: 2px solid #E2E8F0;
            color: #475569;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 16px;
        }

        .table-responsive-custom .table tbody td {
            padding: 12px 16px;
            vertical-align: middle;
            border-bottom: 1px solid #F1F5F9;
        }

        .table-responsive-custom .table tbody tr:hover {
            background: #F8FAFC;
        }

        /* Filter Section */
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

        /* Report Card */
        .report-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 20px;
            border: 1px solid rgba(0,0,0,0.06);
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            transition: all 0.3s ease;
        }
        .report-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.08);
        }
        .report-card .report-title {
            font-weight: 700;
            color: #0F172A;
            font-size: 1.1rem;
            margin-bottom: 15px;
        }
        .report-card .report-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }
        .report-card .report-stats .stat-item {
            text-align: center;
            padding: 15px;
            background: #F8FAFC;
            border-radius: 10px;
        }
        .report-card .report-stats .stat-item .number {
            font-size: 1.8rem;
            font-weight: 800;
            color: #0F172A;
        }
        .report-card .report-stats .stat-item .label {
            font-size: 0.8rem;
            color: #64748B;
        }
        .report-card .report-stats .stat-item.primary .number { color: #4F46E5; }
        .report-card .report-stats .stat-item.success .number { color: #10B981; }
        .report-card .report-stats .stat-item.warning .number { color: #F59E0B; }
        .report-card .report-stats .stat-item.info .number { color: #3B82F6; }
        .report-card .report-stats .stat-item.danger .number { color: #EF4444; }
        
        .report-actions {
            margin-top: 15px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
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
        .modal {
            z-index: 2000;
        }

        .modal-backdrop {
            z-index: 1990;
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

        .modal-footer {
            border-top: 1px solid rgba(0,0,0,0.06);
            padding: 15px 25px;
        }

        .modal .invalid-feedback {
            font-size: 0.8rem;
            color: #EF4444;
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

        /* Pagination */
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
            .filter-section .row > div {
                margin-bottom: 10px;
            }
            .report-card .report-stats {
                grid-template-columns: repeat(2, 1fr);
            }
            .table-responsive-custom {
                overflow-x: auto;
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
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#" id="dashboardLink">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="pendingJobsLink">
                                <i class="fas fa-clock"></i> Pending Jobs
                                <span class="badge-pending" id="pendingBadge">0</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="allJobsLink">
                                <i class="fas fa-briefcase"></i> All Jobs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="employersLink">
                                <i class="fas fa-building"></i> Employers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="jobseekersLink">
                                <i class="fas fa-users"></i> Job Seekers
                            </a>
                        </li>
                        <li class="nav-item dropdown-nav">
                            <a class="nav-link dropdown-toggle-nav" href="#" id="graduatesLink" aria-expanded="false" aria-controls="graduatesMenu">
                                <span class="nav-main">
                                    <i class="fas fa-user-graduate"></i>
                                    <span>Graduates</span>
                                </span>
                                <span class="d-flex align-items-center gap-2">
                                    <!-- <span class="badge-pending" id="graduateBadge">0</span> -->
                                    <i class="fas fa-chevron-down dropdown-caret"></i>
                                </span>
                            </a>
                            <div id="graduatesMenu" style="display: none;">
                                <ul class="nav flex-column nav-submenu">
                                    <li class="nav-item">
                                        <a class="nav-link" href="#" data-graduate-category="">
                                            <i class="fas fa-users"></i> All Graduates
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#" data-graduate-category="college_university">
                                            <i class="fas fa-layer-group"></i> College/University
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#" data-graduate-category="vta">
                                            <i class="fas fa-user-check"></i> VTA Graduates 
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#" data-graduate-category="apprenticeship">
                                            <i class="fas fa-briefcase"></i> Apprenticeship
                                        </a>
                                    </li>

                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="usersLink">
                                <i class="fas fa-user-cog"></i> Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="reportsLink">
                                <i class="fas fa-chart-bar"></i> Reports
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
                            <i class="fas fa-crown text-warning me-2"></i>
                            Welcome back, <strong>{{ Auth::user()->name }}</strong>
                            <span class="role-badge"><i class="fas fa-shield-alt me-1"></i> Admin</span>
                        </div>
                    </div>
                    <div class="user-profile-dropdown">
                        <div class="profile-trigger">
                            <div class="avatar">{{ substr(Auth::user()->name, 0, 1) }}</div>
                            <div class="user-info-text">
                                <span class="user-name">{{ Auth::user()->name }}</span>
                                <span class="user-role"><i class="fas fa-shield-alt me-1"></i> Administrator</span>
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
                                    Dashboard Overview
                                    <small>Manage users, jobs, and platform statistics</small>
                                </h1>
                            </div>
                            <div class="header-actions">
                                <span class="text-muted" style="font-size: 0.9rem;">
                                    <i class="fas fa-calendar-alt me-1"></i> {{ now()->format('l, F j, Y') }}
                                </span>
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-3">
                                <div class="stat-card" style="background: linear-gradient(135deg, #4F46E5, #7C3AED);">
                                    <div class="stat-icon primary">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <h2 class="stat-number" id="totalUsers">0</h2>
                                    <p class="stat-label">Total Users</p>
                                    <span class="stat-change">
                                        <i class="fas fa-users"></i> Registered
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card" style="background: linear-gradient(135deg, #059669, #10B981);">
                                    <div class="stat-icon success">
                                        <i class="fas fa-briefcase"></i>
                                    </div>
                                    <h2 class="stat-number" id="totalJobs">0</h2>
                                    <p class="stat-label">Total Jobs</p>
                                    <span class="stat-change">
                                        <i class="fas fa-check"></i> Active
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
                            <div class="col-md-3">
                                <div class="stat-card" style="background: linear-gradient(135deg, #2563EB, #3B82F6);">
                                    <div class="stat-icon info">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <h2 class="stat-number" id="totalApplications">0</h2>
                                    <p class="stat-label">Applications</p>
                                    <span class="stat-change">
                                        <i class="fas fa-arrow-up"></i> Total
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5">
                            <div class="section-header">
                                <h4><i class="fas fa-history"></i> Recent Applications</h4>
                            </div>
                            <div id="recentApplicationsList"></div>
                        </div>
                    </div>

                    <!-- Pending Jobs -->
                    <div id="pendingJobsContent" style="display: none;">
                        <div class="page-header animate-fade-in">
                            <div>
                                <h1>
                                    Pending Jobs
                                    <small>Review and approve job listings</small>
                                </h1>
                            </div>
                            <div class="header-actions">
                                <button class="btn btn-secondary-gradient" id="backToDashboardBtn">
                                    <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                                </button>
                            </div>
                        </div>
                        <div id="pendingJobsList"></div>
                    </div>

                    <!-- All Jobs -->
                    <div id="allJobsContent" style="display: none;">
                        <div class="page-header animate-fade-in">
                            <div>
                                <h1>
                                    All Jobs
                                    <small>Manage all job listings</small>
                                </h1>
                            </div>
                            <div class="header-actions">
                                <button class="btn btn-secondary-gradient" id="backToDashboardBtn2">
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
                                    <label class="form-label">Filter by Status</label>
                                    <select class="form-select" id="filterStatus">
                                        <option value="">All</option>
                                        <option value="pending">Pending</option>
                                        <option value="approved">Approved</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-primary-gradient w-100" id="filterJobsBtn">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div id="allJobsList"></div>
                    </div>

                    <!-- Employers List -->
                    <div id="employersContent" style="display: none;">
                        <div class="page-header animate-fade-in">
                            <div>
                                <h1>
                                    Employers
                                    <small>Manage employer accounts</small>
                                </h1>
                            </div>
                            <div class="header-actions">
                                <button class="btn btn-secondary-gradient" id="backToDashboardBtn3">
                                    <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                                </button>
                            </div>
                        </div>
                        <div id="employersList"></div>
                    </div>

                    <!-- Job Seekers List -->
                    <div id="jobseekersContent" style="display: none;">
                        <div class="page-header animate-fade-in">
                            <div>
                                <h1>
                                    Job Seekers
                                    <small>Manage job seeker accounts</small>
                                </h1>
                            </div>
                            <div class="header-actions">
                                <button class="btn btn-secondary-gradient" id="backToDashboardBtn4">
                                    <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                                </button>
                            </div>
                        </div>
                        <div id="jobseekersList"></div>
                    </div>

                    <!-- Graduates Management -->
                    <div id="graduatesContent" style="display: none;">
                        <div class="page-header animate-fade-in">
                            <div>
                                <h1>
                                    Graduates Management
                                    <small>Manage graduate students and alumni records</small>
                                </h1>
                            </div>
                            <div class="header-actions">
                                <button class="btn btn-success-gradient" id="importGraduatesBtn">
                                    <i class="fas fa-file-upload me-2"></i> Bulk Import
                                </button>
                                <button class="btn btn-primary-gradient" id="addGraduateBtn">
                                    <i class="fas fa-user-plus me-2"></i> Add Graduate
                                </button>
                                <button class="btn btn-secondary-gradient" id="backToDashboardBtn7">
                                    <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                                </button>
                            </div>
                        </div>

                        <!-- Graduate Statistics -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-3">
                                <div class="stat-card" style="background: linear-gradient(135deg, #4F46E5, #7C3AED);">
                                    <div class="stat-icon primary">
                                        <i class="fas fa-user-graduate"></i>
                                    </div>
                                    <h2 class="stat-number" id="gradTotal">0</h2>
                                    <p class="stat-label">Total Graduates</p>
                                    <span class="stat-change">
                                        <i class="fas fa-users"></i> Registered
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card" style="background: linear-gradient(135deg, #059669, #10B981);">
                                    <div class="stat-icon success">
                                        <i class="fas fa-user-check"></i>
                                    </div>
                                    <h2 class="stat-number" id="gradActive">0</h2>
                                    <p class="stat-label">Active</p>
                                    <span class="stat-change">
                                        <i class="fas fa-check"></i> Available
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card" style="background: linear-gradient(135deg, #D97706, #F59E0B);">
                                    <div class="stat-icon warning">
                                        <i class="fas fa-briefcase"></i>
                                    </div>
                                    <h2 class="stat-number" id="gradEmployed">0</h2>
                                    <p class="stat-label">Employed</p>
                                    <span class="stat-change">
                                        <i class="fas fa-check-circle"></i> Placed
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-card" style="background: linear-gradient(135deg, #EF4444, #F87171);">
                                    <div class="stat-icon danger">
                                        <i class="fas fa-user-slash"></i>
                                    </div>
                                    <h2 class="stat-number" id="gradUnemployed">0</h2>
                                    <p class="stat-label">Unemployed</p>
                                    <span class="stat-change">
                                        <i class="fas fa-search"></i> Available
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Filter Section -->
                        <div class="filter-section">
                            <div class="row align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label">Search Graduates</label>
                                    <input type="text" class="form-control" id="searchGraduate" placeholder="Search by name, email, ID...">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Program</label>
                                    <select class="form-select" id="filterProgram">
                                        <option value="">All Programs</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" id="filterGraduateStatus">
                                        <option value="">All Status</option>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="employed">Employed</option>
                                        <option value="unemployed">Unemployed</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-primary-gradient w-100" id="filterGraduatesBtn">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="graduatesList"></div>
                        <div id="graduatesPagination" class="pagination-custom"></div>
                    </div>

                    <!-- Users Management -->
                    <div id="usersContent" style="display: none;">
                        <div class="page-header animate-fade-in">
                            <div>
                                <h1>
                                    Users Management
                                    <small>Manage all users and create admin accounts</small>
                                </h1>
                            </div>
                            <div class="header-actions">
                                <button class="btn btn-primary-gradient" id="createUserBtn">
                                    <i class="fas fa-user-plus me-2"></i> Create New User
                                </button>
                                <button class="btn btn-secondary-gradient" id="backToDashboardBtn5">
                                    <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                                </button>
                            </div>
                        </div>
                        
                        <div class="filter-section">
                            <div class="row align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label">Search Users</label>
                                    <input type="text" class="form-control" id="searchUser" placeholder="Search by name, email...">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Filter by Role</label>
                                    <select class="form-select" id="filterRole">
                                        <option value="">All Roles</option>
                                        <option value="admin">Admin</option>
                                        <option value="employer">Employer</option>
                                        <option value="jobseeker">Job Seeker</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Filter by Status</label>
                                    <select class="form-select" id="filterUserStatus">
                                        <option value="">All</option>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-primary-gradient w-100" id="filterUsersBtn">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div id="usersList"></div>
                    </div>

                    <!-- Reports -->
                    <div id="reportsContent" style="display: none;">
                        <div class="page-header animate-fade-in">
                            <div>
                                <h1>
                                    Reports
                                    <small>View platform statistics and reports</small>
                                </h1>
                            </div>
                            <div class="header-actions">
                                <button class="btn btn-secondary-gradient" id="backToDashboardBtn6">
                                    <i class="fas fa-arrow-left me-2"></i> Back to Dashboard
                                </button>
                            </div>
                        </div>

                        <div class="filter-section">
                            <div class="row align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label">Report Type</label>
                                    <select class="form-select" id="reportType">
                                        <option value="quarterly">Quarterly Report</option>
                                        <option value="semiannual">Semiannual Report</option>
                                        <option value="yearly">Yearly Report</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Year</label>
                                    <select class="form-select" id="reportYear">
                                        @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-md-3" id="quarterField">
                                    <label class="form-label">Quarter</label>
                                    <select class="form-select" id="reportQuarter">
                                        <option value="1">Q1 (Jan - Mar)</option>
                                        <option value="2">Q2 (Apr - Jun)</option>
                                        <option value="3">Q3 (Jul - Sep)</option>
                                        <option value="4">Q4 (Oct - Dec)</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-primary-gradient w-100" id="generateReportBtn">
                                        <i class="fas fa-chart-bar me-2"></i> Generate
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="reportResults">
                            <div class="text-center py-5">
                                <i class="fas fa-chart-bar" style="font-size: 4rem; color: #CBD5E1;"></i>
                                <h4 class="mt-3">Select Report Criteria</h4>
                                <p class="text-muted">Choose a report type and click Generate to view statistics</p>
                            </div>
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

    <div class="modal fade" id="rejectJobModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-times-circle text-danger me-2"></i> Reject Job Post
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="rejectJobForm">
                        <input type="hidden" id="rejectJobId">
                        <label for="rejectReason" class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejectReason" rows="4" maxlength="1000" placeholder="Explain why this post was rejected so the employer can fix it."></textarea>
                        <div class="invalid-feedback" id="rejectReasonError"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary-gradient" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmRejectJobBtn">
                        <i class="fas fa-times me-2"></i> Reject Job
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Graduate Modal -->
    <div class="modal fade" id="graduateModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-graduate text-primary me-2"></i> 
                        <span id="graduateModalTitle">Add Graduate</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="graduateForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="graduateId" name="graduate_id">
                        <input type="hidden" id="gradFormMethod" name="_method" value="POST">

                        <div class="form-section-card">
                            <h6 class="section-heading">Section A - Personal Identification</h6>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="gradResidentNumber" class="form-label">Zanzibari Resident Number</label>
                                    <input type="text" class="form-control" id="gradResidentNumber" name="resident_number" placeholder="Your Zanzibari Resident Number" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="gradFullName" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="gradFullName" name="full_name" placeholder="Your Full Name" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label d-block">Gender</label>
                                    <div class="d-flex gap-4 pt-1">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gender" id="gradGenderMale" value="Male" checked required>
                                            <label class="form-check-label" for="gradGenderMale">Male</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gender" id="gradGenderFemale" value="Female" required>
                                            <label class="form-check-label" for="gradGenderFemale">Female</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="gradPlaceBirth" class="form-label">Place of Birth</label>
                                    <input type="text" class="form-control" id="gradPlaceBirth" name="place_of_birth" placeholder="Your Place of Birth" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="gradDob" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control" id="gradDob" name="date_of_birth" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="gradRegion" class="form-label">Region of Residence</label>
                                    <select class="form-select" id="gradRegion" name="region_id" required>
                                        <option value="">Select Region</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="gradDistrict" class="form-label">District</label>
                                    <select class="form-select" id="gradDistrict" name="district_id" required disabled>
                                        <option value="">Select District</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="gradWard" class="form-label">Ward</label>
                                    <select class="form-select" id="gradWard" name="ward_id" required disabled>
                                        <option value="">Select Ward</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="gradShehia" class="form-label">Shehia</label>
                                    <select class="form-select" id="gradShehia" name="shehia_id" required disabled>
                                        <option value="">Select Shehia</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="gradPhone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="gradPhone" name="phone" placeholder="Your Phone Number" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="gradEmail" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="gradEmail" name="email" placeholder="Your Email Address" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="gradNextOfKin" class="form-label">Phone Number of Next of Kin</label>
                                    <input type="tel" class="form-control" id="gradNextOfKin" name="next_of_kin_phone" placeholder="Your Next of Kin's Phone Number" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-section-card mb-0">
                            <h6 class="section-heading">Section B - Level of Education</h6>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="gradEducationPath" class="form-label">Select Education Type</label>
                                    <select class="form-select" id="gradEducationPath" name="education_type" required>
                                        <option value="">Choose option</option>
                                        <option value="university">University Graduate</option>
                                        <option value="vta">VTA Graduate</option>
                                        <option value="apprenticeship">Apprenticeship Graduate</option>
                                    </select>
                                </div>
                            </div>

                            <div id="gradUniversityFields" class="education-fields mt-3">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="gradCollegeName" class="form-label">College/University Name</label>
                                        <input type="text" class="form-control" id="gradCollegeName" name="college_name" placeholder="e.g., SUZA, University of Dar es Salaam">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="gradUniversityCourse" class="form-label">Course</label>
                                        <input type="text" class="form-control" id="gradUniversityCourse" name="university_course" placeholder="e.g., Computer Science">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="gradCompletedYear" class="form-label">Completed Year</label>
                                        <input type="text" class="form-control" id="gradCompletedYear" name="completed_year" placeholder="e.g., 2024">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="gradGpa" class="form-label">GPA</label>
                                        <input type="text" class="form-control" id="gradGpa" name="gpa" placeholder="e.g., 3.6">
                                    </div>
                                </div>
                            </div>

                            <div id="gradVtaFields" class="education-fields mt-3">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="gradNtaLevel" class="form-label">NTA Level</label>
                                        <input type="text" class="form-control" id="gradNtaLevel" name="nta_level" placeholder="e.g., NTA Level 6">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="gradVtaCourse" class="form-label">Course</label>
                                        <input type="text" class="form-control" id="gradVtaCourse" name="vta_course" placeholder="e.g., Electrical Installation">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="gradStartedYear" class="form-label">Started Year</label>
                                        <input type="text" class="form-control" id="gradStartedYear" name="started_year" placeholder="e.g., 2022">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="gradEndingYear" class="form-label">Ending Year</label>
                                        <input type="text" class="form-control" id="gradEndingYear" name="ending_year" placeholder="e.g., 2024">
                                    </div>
                                </div>
                            </div>

                            <div id="gradApprenticeshipFields" class="education-fields mt-3">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="gradApprenticeshipMode" class="form-label">Apprenticeship Type</label>
                                        <select class="form-select" id="gradApprenticeshipMode" name="apprenticeship_mode">
                                            <option value="">Choose apprenticeship type</option>
                                            <option value="formal">Formal Apprenticeship</option>
                                            <option value="informal">Informal Apprenticeship</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="gradApprenticeshipSpec" class="form-label">Specify Type of Apprenticeship</label>
                                        <input type="text" class="form-control" id="gradApprenticeshipSpec" name="apprenticeship_specification" placeholder="e.g., Welding, Carpentry, Tailoring">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary-gradient" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-primary-gradient" id="saveGraduateBtn">
                        <i class="fas fa-save me-2"></i> Save Graduate
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Graduate Modal -->
    <div class="modal fade" id="viewGraduateModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-graduate text-primary me-2"></i> Graduate Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="viewGraduateContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary-gradient" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Import Modal -->
    <div class="modal fade" id="importGraduatesModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-file-upload text-success me-2"></i> Bulk Import Graduates
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Instructions:</strong>
                        <ul class="mt-2 mb-0">
                            <li>Upload an Excel file (.xlsx, .xls, .csv)</li>
                            <li>Required columns: student_number, full_name, email</li>
                            <li>Optional columns: phone, program_of_study, year_of_graduation, cgpa, skills, certifications, status</li>
                            <li>Download the template for the correct format</li>
                        </ul>
                    </div>
                    
                    <div class="mb-3">
                        <a href="#" class="btn btn-outline-gradient btn-sm" id="downloadTemplateBtn">
                            <i class="fas fa-download me-2"></i> Download Template
                        </a>
                    </div>

                    <form id="importForm" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="importFile" class="form-label">Select Excel File <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="importFile" name="file" accept=".xlsx,.xls,.csv" required>
                            <div class="invalid-feedback" id="importFileError"></div>
                        </div>
                        
                        <div id="importProgress" style="display: none;">
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" id="importProgressBar" style="width: 0%"></div>
                            </div>
                            <p class="text-muted mt-2" id="importStatus">Uploading...</p>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary-gradient" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-success-gradient" id="importFileBtn">
                        <i class="fas fa-upload me-2"></i> Import
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit User Modal -->
    <div class="modal fade" id="userModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-plus text-primary me-2"></i> <span id="userModalTitle">Create New User</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="userForm">
                        @csrf
                        <input type="hidden" id="userId" name="user_id">
                        <input type="hidden" id="formMethod" name="_method" value="POST">
                        
                        <div class="mb-3">
                            <label for="userName" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="userName" name="name" placeholder="Enter full name" required>
                            <div class="invalid-feedback" id="userNameError"></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="userEmail" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="userEmail" name="email" placeholder="Enter email address" required>
                            <div class="invalid-feedback" id="userEmailError"></div>
                        </div>
                        
                        <div class="mb-3" id="passwordField">
                            <label for="userPassword" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="userPassword" name="password" placeholder="Enter password (min 8 characters)">
                            <div class="invalid-feedback" id="userPasswordError"></div>
                            <small class="text-muted">Minimum 8 characters. Leave blank to keep current password when editing.</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="userRole" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="userRole" name="role" required>
                                <option value="">Select Role</option>
                                <option value="admin">Admin</option>
                                <option value="employer">Employer</option>
                                <option value="jobseeker">Job Seeker</option>
                            </select>
                            <div class="invalid-feedback" id="userRoleError"></div>
                        </div>
                        
                        <div class="mb-3" id="userCompanyField" style="display: none;">
                            <label for="userCompany" class="form-label">Company Name <small class="text-muted">(For Employers)</small></label>
                            <input type="text" class="form-control" id="userCompany" name="company_name" placeholder="Enter company name">
                            <div class="invalid-feedback" id="userCompanyError"></div>
                        </div>
                        
                        <div class="mb-3" id="userPhoneField" style="display: none;">
                            <label for="userPhone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="userPhone" name="phone" placeholder="Enter phone number">
                            <div class="invalid-feedback" id="userPhoneError"></div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="userActive" name="is_active" checked>
                            <label class="form-check-label" for="userActive">Active</label>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary-gradient" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-primary-gradient" id="saveUserBtn">
                        <i class="fas fa-save me-2"></i> Save User
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
