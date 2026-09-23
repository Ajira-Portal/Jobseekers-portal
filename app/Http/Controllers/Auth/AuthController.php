<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\JobseekerProfile;
use App\Models\User;
use App\Services\ZanIdService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public const ZAN_ID_RULE = 'required|string|regex:/^[0-9]{1,12}$/';

    public function __construct(private ZanIdService $zanIdService)
    {
    }

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

    /**
     * Verify a ZAN ID against the Zan ID system and
     * return the citizen data used to auto-fill the registration form.
     */
    public function verifyZanId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'zan_id' => self::ZAN_ID_RULE,
        ], [
            'zan_id.regex' => 'The ZAN ID must contain only digits (up to 12 digits).'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $zanId = $request->zan_id;

        if (User::where('zan_id', $zanId)->exists()) {
            return response()->json([
                'success' => false,
                'errors' => [
                    'zan_id' => ['This ZAN ID is already registered. Please sign in instead.']
                ]
            ], 422);
        }

        $result = $this->zanIdService->verify($zanId);

        if (!$result['success']) {
            $status = $result['status'] === 'not_found' ? 404 : 503;

            return response()->json([
                'success' => false,
                'message' => $result['message'],
                'errors' => [
                    'zan_id' => [$result['message']]
                ]
            ], $status);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'citizen' => [
                'zan_id' => $result['data']['zan_id'],
                'name' => $result['data']['name'],
                'date_of_birth' => $result['data']['date_of_birth'],
                'gender' => $result['data']['gender'],
                'phone' => $result['data']['phone'],
            ]
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'zan_id' => self::ZAN_ID_RULE . '|unique:users,zan_id',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:jobseeker',

            'terms' => 'accepted'
        ], [
            'zan_id.unique' => 'This ZAN ID is already registered. Please sign in instead.',
            'zan_id.regex' => 'The ZAN ID must contain only digits (up to 12 digits).',
            'terms.accepted' => 'You must agree to the terms and conditions.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Re-verify the Zan ID server-side so the registered identity
        // always comes from the Zan ID system, not from form input.
        $result = $this->zanIdService->verify($request->zan_id);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
                'errors' => [
                    'zan_id' => [$result['message']]
                ]
            ], $result['status'] === 'not_found' ? 404 : 503);
        }

        $citizen = $result['data'];

        $user = User::create([
            'name' => $citizen['name'],
            'email' => $request->email,
            'zan_id' => $citizen['zan_id'] ?: $request->zan_id,
            'password' => Hash::make($request->password),
            'role' => 'jobseeker', // Default role for new users

            'phone' => $citizen['phone'],
        ]);

        // Pre-fill the jobseeker profile with the verified identity data.
        // This step must never fail the registration itself.
        try {
            $profile = new JobseekerProfile();
            $profile->user_id = $user->id;
            $profile->resident_number = $user->zan_id;
            $profile->gender = $citizen['gender'] ? strtolower($citizen['gender']) : null;
            $profile->date_of_birth = $citizen['date_of_birth'];
            // These columns are NOT NULL in the schema - default to empty strings.
            $profile->region = '';
            $profile->district = '';
            $profile->ward = '';
            $profile->shehia = '';
            $profile->save();
        } catch (\Throwable $e) {
            Log::warning('Could not pre-fill jobseeker profile after registration: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Registration successful! Please sign in to continue.',
            'redirect' => route('login')
        ]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'zan_id' => 'required|string',
            'password' => 'required|string',
        ], [
            'zan_id.required' => 'Please enter your ZAN ID or Email.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $input = trim($request->input('zan_id'));
        $password = $request->input('password');
        $remember = $request->has('remember');

        // Search user by zan_id, email, or resident_number in profile
        $user = User::where('zan_id', $input)
            ->orWhere('email', $input)
            ->orWhereHas('jobseekerProfile', function ($query) use ($input) {
                $query->where('resident_number', $input);
            })
            ->orWhereHas('personalInfo', function ($query) use ($input) {
                $query->where('resident_number', $input);
            })
            ->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid ZAN ID / Email or password.'
            ], 401);
        }

        Auth::login($user, $remember);

        // Prevent session fixation by regenerating the session ID after login.
        $request->session()->regenerate();

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