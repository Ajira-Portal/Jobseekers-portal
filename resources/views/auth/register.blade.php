<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
     @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>Register - JobPortal</title>
    <link rel="icon" type="image/png" href="{{ asset('images/emblem.png') }}">
    <link rel="preload" href="{{ asset('webfonts/fa-solid-900.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ asset('webfonts/fa-regular-400.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('css/all.min.css')}}" rel="stylesheet">
    <link href="{{ asset('css/fonts.css') }}" rel="stylesheet">
    <link  href="{{asset('css/style.css')}}"  rel="stylesheet">
</head>
<body>
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
    <div class="logo-container">
        <img src="{{ asset('images/emblem.png') }}" alt="Logo" class="logo-image" >
    </div>
    <h4>Jobseekers Portal<span></span></h4>
                <p>Create your account to get started</p>
            </div>

            <!-- Register Form -->
            <form id="registerForm" action="{{ route('register.post') }}" data-login-url="{{ route('login') }}" data-verify-url="{{ route('register.verify-id') }}">
                @csrf
                <input type="hidden" name="role" value="jobseeker">

                <!-- Step 1: ZAN ID Verification -->
                <div class="form-group">
                    <label for="zan_id">ZAN ID</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-id-card"></i>
                        </span>
                           <input type="text" class="form-control" id="zan_id" name="zan_id"
                               inputmode="numeric" autocomplete="off"
                               placeholder="Enter your ZAN ID" maxlength="12" required autofocus>
                        <button class="btn-verify" type="button" id="verifyIdBtn">
                            <span class="btn-content">
                                <i class="fas fa-shield-halved"></i>
                                Verify
                            </span>
                        </button>
                    </div>
                    <div class="invalid-feedback" id="zan_idError"></div>
                    <small class="text-muted" style="font-size: 0.7rem;">Your identity will be verified against the Zanzibar ID system.</small>
                </div>

                <!-- Verified citizen summary -->
                <div class="verified-banner hidden" id="verifiedBanner">
                    <i class="fas fa-circle-check"></i>
                    <div>
                        <strong id="verifiedName"></strong>
                        <span id="verifiedMeta"></span>
                    </div>
                </div>

                <!-- Step 2: unlocked after verification -->
                <fieldset id="registrationFields" disabled>

                <div class="form-group">
                    <label for="name">Full Name</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" class="form-control" id="name" name="name"
                               placeholder="Auto-filled after ID verification" readonly required>
                    </div>
                    <div class="invalid-feedback" id="nameError"></div>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="Enter your email" required>
                    </div>
                    <div class="invalid-feedback" id="emailError"></div>
                </div>

                <div class="form-group company-field hidden">
                    <label for="company_name">Company Name</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-building"></i>
                        </span>
                        <input type="text" class="form-control" id="company_name" name="company_name" 
                               placeholder="Enter your company name">
                    </div>
                    <div class="invalid-feedback" id="company_nameError"></div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Minimum 8 characters" required>
                        <button class="password-toggle" type="button" id="togglePassword" tabindex="-1">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="invalid-feedback" id="passwordError"></div>
                    <small class="text-muted" style="font-size: 0.7rem;">Password must be at least 8 characters long.</small>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" class="form-control" id="password_confirmation" 
                               name="password_confirmation" placeholder="Confirm your password" required>
                    </div>
                </div>

                <div class="form-group">
                    <div class="terms-check">
                        <input type="checkbox" id="terms" name="terms" required>
                        <label for="terms">
                            Agree ?
                        </label>
                    </div>
                    <div class="invalid-feedback" id="termsError"></div>
                </div>

                <button type="submit" class="btn-register" id="registerBtn">
                    <span class="btn-content">
                        <i class="fas fa-user-plus"></i>
                        Create Account
                    </span>
                </button>

                </fieldset>

                <div class="divider">
                    <span>or</span>
                </div>

                <div class="login-link">
                    Already have an account? <a href="{{ route('login') }}">Sign In</a>
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
