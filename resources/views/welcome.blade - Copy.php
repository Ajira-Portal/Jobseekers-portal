<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>labour Market Information System</title>
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
            padding: 6px 0;
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

        .navbar-custom .ministry-center .ministry-name {
            color: white;
            font-size: 1rem;
            font-weight: 700;
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
            position: fixed;
            top: 140px;
            left: 0;
            right: 0;
            z-index: 999;
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
            padding: 110px 0 20px;
            text-align: center;
            border-bottom: 3px solid rgba(79, 70, 229, 0.3);
            position: relative;
            overflow: hidden;
            min-height: 100vh;
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
            background: linear-gradient(135deg, #818CF8, #C084FC);
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
            .navbar-custom .brand img {
                height: 80px;
            }

            .navbar-custom .ministry-center .ministry-name {
                font-size: 0.7rem;
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

            .nav-menu {
                top: 120px;
            }

            .hero-section {
                padding: 180px 0 60px;
            }

            .hero-section h1 {
                font-size: 3rem;
            }

            .info-cards {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .navbar-custom .brand img {
                height: 60px;
            }

            .navbar-custom .ministry-center {
                padding: 0 10px;
            }

            .navbar-custom .ministry-center .ministry-name {
                font-size: 0.55rem;
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

            .nav-menu {
                top: 100px;
            }

            .hero-section {
                padding: 160px 0 40px;
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
<body>
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
                    <h3 class="ministry-name">Ministry of Labour, Youth, Employment and Persons with Disabilities</h3>
                </div>

                <!-- Social Icons - Right -->
                <div class="social-icons">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    @auth
                       
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="login-btn">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                    @endauth
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
                <li><a href="#"><i class="fas fa-file-alt"></i> Documentation</a></li>
                <li><a href="#"><i class="fas fa-question-circle"></i> Help</a></li>
                <li><a href="#"><i class="fas fa-envelope"></i> Contact</a></li>
            </ul>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div class="nav-links d-lg-none" id="mobileMenu" style="display: none; background: rgba(15,23,42,0.95); padding: 15px 20px; position: fixed; top: 100px; left: 0; right: 0; z-index: 999; flex-direction: column; gap: 5px;">
        <a href="/" class="active" style="color: rgba(255,255,255,0.7); text-decoration: none; padding: 10px 16px; border-radius: 8px; font-weight: 500; font-size: 0.9rem;"><i class="fas fa-home"></i> Home</a>
        <a href="#jobs" style="color: rgba(255,255,255,0.7); text-decoration: none; padding: 10px 16px; border-radius: 8px; font-weight: 500; font-size: 0.9rem;"><i class="fas fa-briefcase"></i> Jobs</a>
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
                    <span>Labour Market </span>Information system
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

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {
            // Load jobs
            loadJobs();

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

                $.ajax({
                    url: '/jobs',
                    type: 'GET',
                    data: {
                        search: keyword,
                        location: location,
                        employment_type: type,
                        page: page,
                        per_page: 9
                    },
                    success: function(response) {
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
                                    
                                    const isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
                                    let applyButton = '';
                                    
                                    if (isLoggedIn) {
                                        applyButton = `
                                            <button class="btn-apply" onclick="redirectToApply(${job.id})">
                                                <i class="fas fa-paper-plane me-1"></i> Apply Now
                                            </button>
                                        `;
                                    } else {
                                        applyButton = `
                                            <a href="{{ route('login') }}" class="btn-apply outline">
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
                    },
                    error: function() {
                        $('#loadingSpinner').hide();
                        $('#jobsList').html('<div class="alert alert-danger">Error loading jobs. Please try again.</div>');
                    }
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
                window.location.href = '/jobseeker/dashboard';
            };
        });

        // Mobile menu toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            if (menu.style.display === 'none') {
                menu.style.display = 'flex';
                menu.style.flexDirection = 'column';
            } else {
                menu.style.display = 'none';
            }
        }
    </script>
</body>
</html>