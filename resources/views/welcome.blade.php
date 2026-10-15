<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>Jobseeker's Portal</title>
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
            --dark: #0F172A;
            --gray: #64748B;
            --light: #F8FAFC;
        }

        * {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #F1F5F9;
            overflow-x: hidden;
            padding-top: 140px;
        }

        /* Navbar Styles */
        .navbar-custom {
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(20px);
            padding: 10px 0;
            box-shadow: 0 4px 30px rgba(0,0,0,0.3);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .navbar-custom.scrolled {
            background: rgba(15, 23, 42, 0.98);
        }

        .navbar-custom .navbar-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        /* Logo - Left */
        .navbar-custom .brand {
            display: flex;
            align-items: center;
            text-decoration: none;
            flex-shrink: 0;
        }

        .navbar-custom .brand img {
            height: 120px;
            width: auto;
            border-radius: 10px;
            object-fit: contain;
            background: rgba(12, 10, 22, 0.05);
            padding: 5px;
            transition: transform 0.3s ease;
        }

        .navbar-custom .brand img:hover {
            transform: scale(1.02);
        }

        /* Ministry Name - Center */
        .navbar-custom .ministry-center {
            text-align: center;
            flex: 1;
            padding: 0 20px;
        }

        .navbar-custom .ministry-center .government-name {
            color: rgba(255,255,255,0.8);
            font-size: 1.08rem;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin: 0 0 4px;
            line-height: 1.2;
        }

        .navbar-custom .ministry-center .ministry-name {
            color: white;
            font-size: 0.95rem;
            font-weight: 650;
            letter-spacing: 0.5px;
            margin: 0;
            line-height: 1.3;
        }

        /* Social Icons - Right */
        .navbar-custom .social-icons {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }
        
        .top-language-switch {
            position: fixed;
            top: 10px;
            right: 14px;
            z-index: 1200;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .top-language-switch .lang-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 44px;
            height: 32px;
            padding: 0 12px;
            border-radius: 999px;
            text-decoration: none;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.4px;
            border: 1px solid rgba(255,255,255,0.25);
            color: #fff;
            background: rgba(15, 23, 42, 0.55);
            transition: all 0.2s ease;
        }
        
        .top-language-switch .lang-pill:hover {
            color: #fff;
            background: rgba(79, 70, 229, 0.75);
            border-color: rgba(129, 140, 248, 0.8);
        }
        
        .top-language-switch .lang-pill.active {
            background: linear-gradient(135deg, #4F46E5, #7C3AED);
            border-color: transparent;
            box-shadow: 0 6px 18px rgba(79, 70, 229, 0.3);
        }


        .navbar-custom .social-icons a {
            color: rgba(255,255,255,0.6);
            text-decoration: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            font-size: 1rem;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .navbar-custom .social-icons a:hover {
            color: white;
            background: rgba(79, 70, 229, 0.3);
            border-color: rgba(79, 70, 229, 0.3);
            transform: translateY(-2px);
        }

        .navbar-custom .social-icons .login-btn {
            background: linear-gradient(135deg, #4F46E5, #7C3AED);
            color: white;
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
            border: none;
            width: auto;
            height: auto;
            font-size: 0.85rem;
            gap: 8px;
        }

        .navbar-custom .social-icons .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.4);
            color: white;
            background: linear-gradient(135deg, #6366F1, #8B5CF6);
            border: none;
        }

        .navbar-custom .social-icons .login-btn i {
            margin-right: 6px;
        }

        /* Mobile Menu Toggle */
        .navbar-toggler {
            border: none;
            padding: 8px;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            flex-shrink: 0;
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }

        .navbar-toggler .bar {
            display: block;
            width: 25px;
            height: 2px;
            margin: 5px auto;
            background: white;
            transition: all 0.3s ease;
        }

        /* Navigation Menu Below Ministry Name */
        .nav-menu {
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(10px);
            padding: 0;
            position: relative;
            z-index: 10;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            display: block;
        }

        .nav-menu .nav-links {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0;
            padding: 0;
            margin: 0;
            list-style: none;
        }

        .nav-menu .nav-links li {
            display: inline-block;
        }

        .nav-menu .nav-links a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            padding: 12px 24px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            position: relative;
            display: inline-block;
        }

        .nav-menu .nav-links a:hover {
            color: white;
            background: rgba(255,255,255,0.05);
        }

        .nav-menu .nav-links a.active {
            color: white;
        }

        .nav-menu .nav-links a.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 30px;
            height: 3px;
            background: linear-gradient(90deg, #4F46E5, #7C3AED);
            border-radius: 2px;
        }

        .nav-menu .nav-links a i {
            margin-right: 8px;
        }

        /* Hero Section - Information */
        .hero-section {
            background: linear-gradient(135deg, #0f2a26 0%, #1E293B 100%);
            padding: 70px 0 20px;
            text-align: center;
            border-bottom: 3px solid rgba(79, 70, 229, 0.3);
            position: relative;
            overflow: hidden;
            min-height: 77vh;
            display: flex;
            align-items: center;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(ellipse at 30% 50%, rgba(79, 70, 229, 0.05) 0%, transparent 60%),
                        radial-gradient(ellipse at 70% 50%, rgba(124, 58, 237, 0.05) 0%, transparent 60%);
            animation: rotateBackground 30s linear infinite;
        }

        @keyframes rotateBackground {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .hero-section .hero-content {
            margin-top: -40px;
            position: relative;
            z-index: 1;
        }

        .hero-section .welcome-text {
            color: rgba(255,255,255,0.5);
            font-size: 0.9rem;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .hero-section h1 {
            font-weight: 900;
            color: white;
            font-size: 2.5rem;
            margin: 0;
            letter-spacing: 1px;
            line-height: 1.1;
        }

        .hero-section h1 span {
            background: linear-gradient(135deg, #cccfe7, #c5cdc7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-section .hero-divider {
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #4F46E5, #7C3AED);
            margin: 20px auto;
            border-radius: 2px;
        }

        .hero-section .hero-description {
            color: rgba(255,255,255,0.6);
            font-size: 1.1rem;
            max-width: 700px;
            margin: 0 auto 40px;
            line-height: 1.8;
        }

        /* Info Cards */
        .info-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            margin-top: 20px;
        }

        .info-card {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 30px 24px;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.08);
            transition: all 0.3s ease;
        }

        .info-card:hover {
            transform: translateY(-8px);
            background: rgba(255,255,255,0.08);
            border-color: rgba(79, 70, 229, 0.3);
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        .info-card .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            font-size: 1.5rem;
        }

        .info-card .card-icon.primary {
            background: rgba(79, 70, 229, 0.2);
            color: #818CF8;
        }

        .info-card .card-icon.success {
            background: rgba(16, 185, 129, 0.2);
            color: #34D399;
        }

        .info-card .card-icon.warning {
            background: rgba(245, 158, 11, 0.2);
            color: #FBBF24;
        }

        .info-card .card-icon.info {
            background: rgba(59, 130, 246, 0.2);
            color: #60A5FA;
        }

        .info-card h4 {
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 10px;
        }

        .info-card p {
            color: rgba(255,255,255,0.6);
            font-size: 0.9rem;
            line-height: 1.6;
            margin: 0;
        }

        /* Jobs Section */
        .jobs-section {
            padding: 60px 0;
        }

        .jobs-section .section-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .jobs-section .section-header h2 {
            font-weight: 800;
            color: #0F172A;
            font-size: 2.5rem;
        }

        .jobs-section .section-header h2 span {
            background: linear-gradient(135deg, #4F46E5, #7C3AED);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .jobs-section .section-header p {
            color: #64748B;
            font-size: 1.1rem;
        }

        /* Job Cards */
        .job-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
            transition: all 0.3s ease;
            border: 1px solid rgba(0,0,0,0.06);
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }

        .job-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.08);
            border-color: rgba(79, 70, 229, 0.1);
        }

        .job-card .job-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .job-card .job-header .job-title {
            font-weight: 700;
            color: #0F172A;
            font-size: 1.1rem;
            margin: 0;
        }

        .job-card .job-header .job-company {
            color: #64748B;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .job-card .job-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin: 12px 0;
        }

        .job-card .job-meta span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #64748B;
            font-size: 0.85rem;
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

        .job-card .job-footer .job-tags {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .job-card .job-footer .job-tags .tag {
            background: #F1F5F9;
            color: #64748B;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .job-card .job-footer .job-tags .tag.active {
            background: #D1FAE5;
            color: #065F46;
        }

        .job-card .btn-apply {
            background: linear-gradient(135deg, #4F46E5, #7C3AED);
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 600;
            color: white;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }

        .job-card .btn-apply:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(79, 70, 229, 0.3);
            color: white;
        }

        .job-card .btn-apply.outline {
            background: transparent;
            border: 2px solid #4F46E5;
            color: #4F46E5;
        }

        .job-card .btn-apply.outline:hover {
            background: #4F46E5;
            color: white;
        }

        /* Toast Container */
        .toast-container {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 9999;
        }

        .toast {
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            border: none;
            min-width: 300px;
            backdrop-filter: blur(10px);
        }

        .toast .toast-body {
            padding: 14px 18px;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .toast.bg-success {
            background: linear-gradient(135deg, #059669, #10B981) !important;
        }

        .toast.bg-danger {
            background: linear-gradient(135deg, #DC2626, #EF4444) !important;
        }

        /* Graduate Modal */
        .graduate-modal .modal-content {
            border: 0;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 18px 50px rgba(15, 23, 42, 0.22);
        }

        .graduate-modal .modal-header {
            background: linear-gradient(135deg, #0F172A 0%, #1E3A8A 100%);
            color: #fff;
            border-bottom: 0;
            padding: 1rem 1.25rem;
        }

        .graduate-modal .modal-title {
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: 0.2px;
        }

        .graduate-modal .btn-close {
            filter: invert(1);
            opacity: 0.9;
        }

        .graduate-modal .modal-body {
            background: linear-gradient(180deg, #F8FAFC 0%, #EEF2FF 100%);
            padding: 1.25rem;
        }

        .graduate-modal .section-heading {
            font-size: 0.78rem;
            letter-spacing: 0.9px;
            text-transform: uppercase;
            color: #1D4ED8;
            font-weight: 800;
            margin-bottom: 0.85rem;
            border-left: 4px solid #2563EB;
            padding-left: 0.6rem;
        }

        .graduate-modal .form-section-card {
            background: #fff;
            border: 1px solid #E2E8F0;
            border-radius: 14px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .graduate-modal .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #1E293B;
            margin-bottom: 0.4rem;
        }

        .graduate-modal .form-control {
            border-radius: 10px;
            border: 1px solid #CBD5E1;
            padding: 0.62rem 0.75rem;
            font-size: 0.9rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .graduate-modal .form-select {
            border-radius: 10px;
            border: 1px solid #CBD5E1;
            padding: 0.62rem 0.75rem;
            font-size: 0.9rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .graduate-modal .form-control:focus {
            border-color: #3B82F6;
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.18);
        }

        .graduate-modal .form-select:focus {
            border-color: #3B82F6;
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.18);
        }

        .graduate-modal .form-check-input {
            border-color: #94A3B8;
        }

        .graduate-modal .form-check-input:checked {
            background-color: #2563EB;
            border-color: #2563EB;
        }

        .graduate-modal .modal-footer {
            border-top: 1px solid #E2E8F0;
            background: #fff;
            padding: 0.9rem 1.25rem;
        }

        .graduate-modal .btn-secondary {
            border-radius: 10px;
            padding: 0.48rem 1rem;
        }

        .graduate-modal .btn-primary {
            background: linear-gradient(135deg, #2563EB, #4F46E5);
            border: none;
            border-radius: 10px;
            padding: 0.52rem 1.05rem;
            font-weight: 600;
        }

        .graduate-modal .btn-primary:hover {
            background: linear-gradient(135deg, #1D4ED8, #4338CA);
            transform: translateY(-1px);
        }

        .graduate-modal .education-fields {
            display: none;
        }

        /* Loading Spinner */
        .loading-spinner {
            display: none;
            text-align: center;
            padding: 40px;
        }

        .loading-spinner .spinner-border {
            width: 50px;
            height: 50px;
            border-width: 3px;
            color: #4F46E5;
        }

        /* Responsive */
        @media (max-width: 992px) {
            body {
                padding-top: 120px;
            }
            
            .top-language-switch {
                top: 8px;
                right: 10px;
            }
            
            .top-language-switch .lang-pill {
                min-width: 40px;
                height: 30px;
                padding: 0 10px;
                font-size: 0.72rem;
            }


            .navbar-custom .brand img {
                height: 80px;
            }

            .navbar-custom .ministry-center .government-name {
                font-size: 0.88rem;
                margin-bottom: 3px;
            }

            .navbar-custom .ministry-center .ministry-name {
                font-size: 0.72rem;
            }

            .navbar-custom .social-icons a {
                width: 30px;
                height: 30px;
                font-size: 0.8rem;
            }

            .navbar-custom .social-icons .login-btn {
                padding: 6px 14px;
                font-size: 0.75rem;
            }

            .nav-menu .nav-links a {
                padding: 10px 16px;
                font-size: 0.8rem;
            }

            .hero-section {
                padding: 10px 0 10px;
            }

            .hero-section .hero-content {
                margin-top: 10px;
            }

            .hero-section h1 {
                font-size: 3rem;
            }

            .info-cards {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            body {
                padding-top: 98px;
            }

            .navbar-custom .brand img {
                height: 60px;
            }

            .navbar-custom .ministry-center {
                padding: 0 10px;
            }

            .navbar-custom .ministry-center .government-name {
                font-size: 0.72rem;
                margin-bottom: 2px;
                letter-spacing: 0.7px;
            }

            .navbar-custom .ministry-center .ministry-name {
                font-size: 0.56rem;
            }

            .navbar-custom .social-icons {
                gap: 6px;
            }

            .navbar-custom .social-icons a {
                width: 26px;
                height: 26px;
                font-size: 0.7rem;
            }

            .navbar-custom .social-icons .login-btn {
                padding: 4px 12px;
                font-size: 0.65rem;
            }

            .nav-menu .nav-links {
                flex-wrap: wrap;
                padding: 5px 0;
            }

            .nav-menu .nav-links a {
                padding: 8px 12px;
                font-size: 0.7rem;
            }

            .hero-section {
                padding: 45px 0 40px;
            }

            .hero-section .hero-content {
                margin-top: -12px;
            }

            .hero-section h1 {
                font-size: 2.2rem;
            }

            .hero-section .hero-description {
                font-size: 0.95rem;
            }

            .info-cards {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .jobs-section .section-header h2 {
                font-size: 1.8rem;
            }
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
            animation: fadeInUp 0.6s ease forwards;
        }

        .animate-fade-in-delay {
            animation: fadeInUp 0.6s ease 0.2s forwards;
            opacity: 0;
        }

        .animate-fade-in-delay-2 {
            animation: fadeInUp 0.6s ease 0.4s forwards;
            opacity: 0;
        }

        .info-card {
            animation: fadeInUp 0.6s ease forwards;
            opacity: 0;
        }

        .info-card:nth-child(1) { animation-delay: 0.1s; }
        .info-card:nth-child(2) { animation-delay: 0.2s; }
        .info-card:nth-child(3) { animation-delay: 0.3s; }
        .info-card:nth-child(4) { animation-delay: 0.4s; }

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
<body data-is-authenticated="{{ auth()->check() ? '1' : '0' }}" data-login-url="{{ route('login') }}" data-jobseeker-dashboard-url="{{ url('/') }}">
    <div class="top-language-switch" aria-label="Language switch">
        <a href="{{ route('language.switch', ['locale' => 'sw']) }}" class="lang-pill {{ app()->getLocale() === 'sw' ? 'active' : '' }}">SW</a>
        <a href="{{ route('language.switch', ['locale' => 'en']) }}" class="lang-pill {{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
    </div>

    <!-- Navbar -->
    <nav class="navbar-custom" id="navbar">
        <div class="container">
            <div class="navbar-content">
                <!-- Logo - Left -->
                <a href="/" class="brand">
                    <img src="{{ asset('images/emblem.png') }}" alt="Logo">
                </a>

                <!-- Ministry Name - Center -->
                <div class="ministry-center">
                    <p class="government-name">The Revolutionary Government of Zanzibar</p>
                    <h3 class="ministry-name">Ministry of Youth, Employment and Empowerment</h3>
                </div>
    
                <!-- Social Icons - Right -->
                <div class="social-icons">
                     @auth
                       
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="login-btn">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    @endauth
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                   
                </div>

                <!-- Mobile Toggle -->
                <button class="navbar-toggler d-lg-none" type="button" onclick="toggleMobileMenu()">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </button>
            </div>
        </div>
    </nav>

    <!-- Navigation Menu Below Ministry Name -->
    <div class="nav-menu" id="navMenu">
        <div class="container">
            <ul class="nav-links">
                <li><a href="/" class="active"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="#jobs"><i class="fas fa-briefcase"></i> Jobs</a></li>
                <li><a href="#" id="graduatesNavLink"><i class="fas fa-user-graduate"></i> Graduates</a></li>
                <li><a href="#"><i class="fas fa-file-alt"></i> Documentation</a></li>                
                <li><a href="#"><i class="fas fa-question-circle"></i> Help</a></li>
                <li><a href="#"><i class="fas fa-envelope"></i> Contact</a></li>
            </ul>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div class="nav-links d-lg-none" id="mobileMenu" style="display: none; background: rgba(15,23,42,0.95); padding: 15px 20px; position: fixed; top: 98px; left: 0; right: 0; z-index: 999; flex-direction: column; gap: 5px;">
        <a href="/" class="active" style="color: rgba(255,255,255,0.7); text-decoration: none; padding: 10px 16px; border-radius: 8px; font-weight: 500; font-size: 0.9rem;"><i class="fas fa-home"></i> Home</a>
        <a href="#jobs" style="color: rgba(255,255,255,0.7); text-decoration: none; padding: 10px 16px; border-radius: 8px; font-weight: 500; font-size: 0.9rem;"><i class="fas fa-briefcase"></i> Jobs</a>
        <a href="#" id="graduatesNavLinkMobile" style="color: rgba(255,255,255,0.7); text-decoration: none; padding: 10px 16px; border-radius: 8px; font-weight: 500; font-size: 0.9rem;"><i class="fas fa-user-graduate"></i> Graduates</a>
        <a href="#" style="color: rgba(255,255,255,0.7); text-decoration: none; padding: 10px 16px; border-radius: 8px; font-weight: 500; font-size: 0.9rem;"><i class="fas fa-file-alt"></i> Documentation</a>
        <a href="#" style="color: rgba(255,255,255,0.7); text-decoration: none; padding: 10px 16px; border-radius: 8px; font-weight: 500; font-size: 0.9rem;"><i class="fas fa-question-circle"></i> Help</a>
        <a href="#" style="color: rgba(255,255,255,0.7); text-decoration: none; padding: 10px 16px; border-radius: 8px; font-weight: 500; font-size: 0.9rem;"><i class="fas fa-envelope"></i> Contact</a>
    </div>

    <!-- Hero Section with Information -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <p class="welcome-text animate-fade-in">WELCOME TO</p>
                <h1 class="animate-fade-in-delay">
                    <span>Jobseekers Portal</span>
                </h1>
                <div class="hero-divider animate-fade-in-delay-2"></div>
                

                <!-- Info Cards -->
                <div class="info-cards">
                    <div class="info-card">
                        <div class="card-icon primary">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <h4>Opportunities</h4>
                        <p>Opportunities obtained from Employers</p>
                    </div>

                    <div class="info-card">
                        <div class="card-icon success">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h4>Applicant/Expert</h4>
                        <p>An individual with qualifications or expertise who is searching for an employment opportunity</p>
                    </div>

                    <div class="info-card">
                        <div class="card-icon warning">
                            <i class="fas fa-building"></i>
                        </div>
                        <h4>Employer</h4>
                        <p>Any natural or legal entity that employs people. Kazi Portal allows employers to register jobs and internship</p>
                    </div>

                    <div class="info-card">
                        <div class="card-icon info">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <h4>ZBPRA</h4>
                        <p>A natural or legal person, company, institution or any other entity whose objective is to provide employment</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Jobs Section -->
    <section class="jobs-section" id="jobs">
        <div class="container">
            <!-- <div class="section-header">
                <h2>Available <span>Job Posts</span></h2>
                <p>Discover the best opportunities from top employers</p>
            </div> -->

            <!-- Loading Spinner -->
            <div class="loading-spinner" id="loadingSpinner">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Loading jobs...</p>
            </div>

            <!-- Jobs List -->
            <div id="jobsList"></div>

            <!-- Pagination -->
            <div id="paginationLinks" class="d-flex justify-content-center mt-4"></div>
        </div>
    </section>

    <!-- Toast Container -->
    <div class="toast-container"></div>

    <!-- Graduate Registration Modal -->
    <div class="modal fade graduate-modal" id="graduateRegisterModal" tabindex="-1" aria-labelledby="graduateRegisterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="graduateRegisterModalLabel">
                        <i class="fas fa-user-graduate text-primary me-2"></i> Graduate Information Form
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="graduateRegisterForm" data-verify-url="{{ route('register.verify-id') }}">
                        <div class="form-section-card">
                            <h6 class="section-heading">Section A - Personal Identification</h6>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="gradResidentNumber" class="form-label">Zanzibar Resident Number</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="gradResidentNumber" name="resident_number" inputmode="numeric" autocomplete="off" maxlength="12" placeholder="Your Zanzibar Resident Number" required>
                                        <button type="button" class="btn btn-outline-primary" id="verifyGradResidentNumberBtn">
                                            <span class="btn-content"><i class="fas fa-shield-halved"></i> Verify</span>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback" id="gradResidentNumberError"></div>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="gradFullName" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="gradFullName" name="full_name" placeholder="Your Full Name" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label d-block">Gender</label>
                                    <div class="d-flex gap-4 pt-1">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gender" id="gradGenderMale" value="Male" required>
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
                                    <input type="tel" class="form-control" id="gradNextOfKin" name="next_of_kin_phone" placeholder="Your Next of Kin Phone Number" required>
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

                            <div id="universityFields" class="education-fields mt-3">
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

                            <div id="vtaFields" class="education-fields mt-3">
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

                            <div id="apprenticeshipFields" class="education-fields mt-3">
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submitGraduateRegisterBtn">
                        <i class="fas fa-paper-plane me-1"></i> Submit Form
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    @vite(['resources/js/app.js'])
</body>
</html>
