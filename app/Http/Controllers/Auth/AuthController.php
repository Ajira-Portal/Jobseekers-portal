<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    private function redirectAuthenticatedUser()
    {
        if (Auth::check()) {
            return redirect($this->getRedirectUrl(Auth::user()->role));
        }

        return null;
    }

    public function showLoginForm()
    {
        if ($redirect = $this->redirectAuthenticatedUser()) {
            return $redirect;
        }

        return view('auth.login');
    }

    public function showRegisterForm()
    {
        if ($redirect = $this->redirectAuthenticatedUser()) {
            return $redirect;
        }

        return view('auth.register');
    }

    public function showForgotForm()
    {
        if ($redirect = $this->redirectAuthenticatedUser()) {
            return $redirect;
        }

        return view('auth.forgot-password');
    }

    public function showResetForm($token)
    {
        if ($redirect = $this->redirectAuthenticatedUser()) {
            return $redirect;
        }

        return view('auth.reset-password', ['token' => $token]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:jobseeker',
            'company_name' => 'nullable|string|max:255',
            'terms' => 'accepted'
        ], [
            'terms.accepted' => 'You must agree to the terms and conditions.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'jobseeker', // Default role for new users
            'company_name' => $request->company_name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Registration successful! Please sign in to continue.',
            'redirect' => route('login')
        ]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string',
        ], [
            'email.required' => 'Please enter your email or ZANID.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $loginInput = trim($request->input('email'));
        $password = $request->input('password');
        $remember = $request->has('remember');

        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            $credentials = ['email' => $loginInput, 'password' => $password];
        } else {
            // Find user by ZANID (resident_number) in JobseekerProfile or PersonalInfo
            $user = User::whereHas('jobseekerProfile', function ($query) use ($loginInput) {
                $query->where('resident_number', $loginInput);
            })->orWhereHas('personalInfo', function ($query) use ($loginInput) {
                $query->where('resident_number', $loginInput);
            })->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid email/ZANID or password.'
                ], 401);
            }

            $credentials = ['email' => $user->email, 'password' => $password];
        }

        if (!Auth::attempt($credentials, $remember)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email/ZANID or password.'
            ], 401);
        }

        // Prevent session fixation by regenerating the session ID after login.
        $request->session()->regenerate();

        $user = Auth::user();

        // Check if user is active
        if (!$user->is_active) {
            Auth::logout();
            return response()->json([
                'success' => false,
                'message' => 'Your account has been deactivated. Please contact support.'
            ], 403);
        }

        // Update last login
        $user->last_login_at = now();
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Login successful! Welcome back, ' . $user->name . '!',
            'redirect' => $this->getRedirectUrl($user->role),
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ]
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('success', 'You have been logged out successfully.');
    }

    public function sendResetLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'success' => true,
                'message' => 'Password reset link sent to your email!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unable to send reset link. Please try again.'
        ], 400);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));
                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully! You can now login.',
                'redirect' => '/login'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid token or email. Please try again.'
        ], 400);
    }

    private function getRedirectUrl($role)
    {
        return match($role) {
            'admin' => '/admin/dashboard',
            'employer' => '/employer/dashboard',
            default => '/jobseeker/dashboard',
        };
    }
}