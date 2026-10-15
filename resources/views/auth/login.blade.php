<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>Login - JobPortal</title>
    <link rel="icon" type="image/png" href="{{ asset('images/emblem.png') }}">
    <link rel="preload" href="{{ asset('webfonts/fa-solid-900.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('webfonts/fa-regular-400.woff2') }}" as="font" type="font/woff2" crossorigin>
    
     <link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('css/all.min.css')}}" rel="stylesheet">
    
    <link href="{{ asset('css/fonts.css') }}" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

       


html {
    font-size: 14px;
}


        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 50%, #0F172A 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
            
        }

        /* Animated Background */
        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(ellipse at 30% 50%, rgba(79, 70, 229, 0.08) 0%, transparent 60%),
                        radial-gradient(ellipse at 70% 50%, rgba(124, 58, 237, 0.08) 0%, transparent 60%);
            animation: rotateBackground 30s linear infinite;
            z-index: 0;
        }

        @keyframes rotateBackground {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Floating Particles */
        .particles {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            overflow: hidden;
            z-index: 0;
        }

        .particle {
            position: absolute;
            border-radius: 50%;
            background: rgba(79, 70, 229, 0.1);
            animation: floatParticle 20s infinite ease-in-out;
        }

        .particle:nth-child(1) { width: 80px; height: 80px; top: 10%; left: 5%; animation-delay: 0s; }
        .particle:nth-child(2) { width: 120px; height: 120px; top: 70%; right: 10%; animation-delay: -5s; }
        .particle:nth-child(3) { width: 60px; height: 60px; bottom: 20%; left: 20%; animation-delay: -10s; }
        .particle:nth-child(4) { width: 100px; height: 100px; top: 40%; right: 5%; animation-delay: -15s; }

        @keyframes floatParticle {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(30px, -30px) scale(1.1); }
            50% { transform: translate(-20px, 20px) scale(0.9); }
            75% { transform: translate(40px, 30px) scale(1.05); }
        }

        .auth-container {
            width: 100%;
            max-width: 460px;
            position: relative;
            z-index: 1;
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 45px 40px 40px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
            animation: slideUp 0.6s ease forwards;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .auth-card .brand {
            text-align: center;
            margin-bottom: 35px;
        }

        .auth-card .brand .logo-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #3a703e, #7C3AED);
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            box-shadow: 0 10px 30px rgba(79, 70, 229, 0.3);
            margin-bottom: 15px;
            transition: transform 0.3s ease;
        }

        .auth-card .brand .logo-icon:hover {
            transform: scale(1.05) rotate(-5deg);
        }

        .auth-card .brand h4 {
            font-weight: 800;
            color: #0F172A;
            font-size: 1.3rem;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .auth-card .brand h4 span {
            background: linear-gradient(135deg, #4F46E5, #7C3AED);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .auth-card .brand p {
            color: #64748B;
            font-size: 0.95rem;
            margin-top: 5px;
        }

        .auth-card .brand .logo-container {
    margin-bottom: 15px;
}

.auth-card .brand .logo-image {
    width: 120px;
    height: auto;
    display: inline-block;
    transition: transform 0.3s ease;
}

.auth-card .brand .logo-image:hover {
    transform: scale(1.05) rotate(-3deg);
}

        .auth-card .form-group {
            margin-bottom: 20px;
        }

        .auth-card .form-group label {
            font-weight: 600;
            color: #0F172A;
            font-size: 0.9rem;
            margin-bottom: 6px;
            display: block;
        }

        .auth-card .form-group .input-group {
            border-radius: 12px;
            overflow: hidden;
            border: 2px solid #E2E8F0;
            transition: all 0.3s ease;
            background: white;
        }

        .auth-card .form-group .input-group:focus-within {
            border-color: #4F46E5;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        .auth-card .form-group .input-group.is-invalid {
            border-color: #EF4444;
        }

        .auth-card .form-group .input-group.is-invalid:focus-within {
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
        }

        .auth-card .form-group .input-group-text {
            background: transparent;
            border: none;
            color: #94A3B8;
            padding: 0 0 0 16px;
            font-size: 1.1rem;
        }

        .auth-card .form-group .form-control {
            border: none;
            padding: 12px 16px;
            font-size: 0.95rem;
            background: transparent;
            box-shadow: none;
        }

        .auth-card .form-group .form-control:focus {
            box-shadow: none;
        }

        .auth-card .form-group .input-group .password-toggle {
            background: transparent;
            border: none;
            color: #94A3B8;
            padding: 0 16px 0 0;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .auth-card .form-group .input-group .password-toggle:hover {
            color: #4F46E5;
        }

        .auth-card .form-group .invalid-feedback {
            font-size: 0.8rem;
            margin-top: 4px;
            display: block;
        }

        .auth-card .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .auth-card .form-options .checkbox-custom {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .auth-card .form-options .checkbox-custom input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #4F46E5;
            cursor: pointer;
        }

        .auth-card .form-options .checkbox-custom label {
            color: #64748B;
            font-size: 0.9rem;
            cursor: pointer;
            margin: 0;
        }

        .auth-card .form-options a {
            color: #4F46E5;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .auth-card .form-options a:hover {
            color: #7C3AED;
            text-decoration: underline;
        }

        .auth-card .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #4F46E5, #7C3AED);
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.3);
            position: relative;
            overflow: hidden;
        }

        .auth-card .btn-login:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(79, 70, 229, 0.4);
        }

        .auth-card .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .auth-card .btn-login .btn-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .auth-card .divider {
            display: flex;
            align-items: center;
            margin: 25px 0;
        }

        .auth-card .divider::before,
        .auth-card .divider::after {
            content: '';
            flex: 1;
            border-bottom: 2px solid #E2E8F0;
        }

        .auth-card .divider span {
            padding: 0 15px;
            color: #94A3B8;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .auth-card .register-link {
            text-align: center;
            color: #64748B;
            font-size: 0.95rem;
        }

        .auth-card .register-link a {
            color: #4F46E5;
            text-decoration: none;
            font-weight: 700;
            transition: color 0.3s ease;
        }

        .auth-card .register-link a:hover {
            color: #7C3AED;
            text-decoration: underline;
        }

        .spinner-custom {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s ease infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Toast Container */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }

        .toast {
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            border: none;
            min-width: 320px;
            backdrop-filter: blur(10px);
        }

        .toast .toast-body {
            padding: 16px 20px;
            font-weight: 500;
        }

        .toast.bg-success {
            background: linear-gradient(135deg, #059669, #10B981) !important;
        }

        .toast.bg-danger {
            background: linear-gradient(135deg, #DC2626, #EF4444) !important;
        }

        .toast.bg-info {
            background: linear-gradient(135deg, #2563EB, #3B82F6) !important;
        }

        .toast.bg-warning {
            background: linear-gradient(135deg, #D97706, #F59E0B) !important;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .auth-card {
                padding: 30px 20px;
            }

            .auth-card .brand .logo-icon {
                width: 300px;
                height: 300px;
                font-size: 1.5rem;
            }

            .auth-card .brand h2 {
                font-size: 1.5rem;
            }

            .auth-card .form-options {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start;
            }
        }

        .top-language-switch {
            position: fixed;
            top: 15px;
            right: 20px;
            z-index: 1200;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .top-language-switch .lang-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 28px;
            padding: 0 10px;
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
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }
    </style>
</head>
<body>
    <div class="top-language-switch" aria-label="Language switch">
        <a href="{{ route('language.switch', ['locale' => 'sw']) }}" class="lang-pill {{ app()->getLocale() === 'sw' ? 'active' : '' }}">SW</a>
        <a href="{{ route('language.switch', ['locale' => 'en']) }}" class="lang-pill {{ app()->getLocale() === 'en' ? 'active' : '' }}">EN</a>
    </div>

    <!-- Animated Background Particles -->
    <div class="particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <div class="auth-container">
        <div class="auth-card">
            <!-- Brand -->
           <!-- Brand with Logo -->
<div class="brand">
    <a href="{{ url('/') }}" class="logo-container d-inline-block" aria-label="Go to welcome page">
       <img src="{{ asset('images/emblem.png') }}" alt="Logo" class="logo-image" style="height: 110px;">
    </a>
    <h4 class="mt-2 fw-bold text-dark">Jobseekers Portal<span></span></h4>
    <p class="text-muted">Sign in to your account</p>
</div>

            <!-- Login Form -->
            <form id="loginForm" action="{{ route('login.post') }}">
                @csrf
                <div class="form-group">
                        <label for="zan_id">ZAN ID</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-id-card"></i>
                        </span>
                            <input type="text" class="form-control" id="zan_id" name="zan_id"
                               inputmode="numeric" autocomplete="off"
                                   placeholder="Enter your ZAN ID" maxlength="12" required autofocus>
                    </div>
                        <div class="invalid-feedback" id="zan_idError"></div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Enter your password" required>
                        <button class="password-toggle" type="button" id="togglePassword" tabindex="-1">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="invalid-feedback" id="passwordError"></div>
                </div>

                <!-- <div class="form-options">
                    <label class="checkbox-custom">
                        <input type="checkbox" name="remember" id="remember">
                        <label for="remember">Remember Me</label>
                    </label>
                    <a href="{{ route('password.request') }}">Forgot Password?</a>
                </div> -->

                <button type="submit" class="btn-login" id="loginBtn">
                    <span class="btn-content">
                        <i class="fas fa-sign-in-alt"></i>
                        Sign In
                    </span>
                </button>

                <div class="divider">
                    <span>or</span>
                </div>

                <div class="register-link">
                    Dont have an account? <a href="{{ route('register') }}">Create Account</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container"></div>

    <script src="{{asset('js/jquery-3.6.0.min.js')}}"></script>
    <script src="{{asset('js/bootstrap.bundle.min.js')}}"></script>
</body>
</html>
