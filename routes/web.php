<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Employer\JobController as EmployerJobController;
use App\Http\Controllers\Jobseeker\DashboardController as JobseekerDashboardController;
use App\Http\Controllers\Jobseeker\ProfileController;
use Illuminate\Http\Request;

// ============================================
// WELCOME PAGE & PUBLIC ROUTES
// ============================================

Route::get('/', function () {
    if (auth()->check()) {
        $role = auth()->user()->role;

        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'employer' => redirect()->route('employer.dashboard'),
            default => redirect()->route('jobseeker.dashboard'),
        };
    }

    return view('welcome');
})->middleware('prevent.back.history')->name('home');

Route::get('/stats', [AdminDashboardController::class, 'getStats']);
Route::get('/jobs', [JobseekerDashboardController::class, 'getPublicJobs']);
Route::get('/jobs/{id}', [JobseekerDashboardController::class, 'getPublicJobDetails']);
Route::post('/graduates/register', [AdminDashboardController::class, 'registerGraduate'])->name('graduates.register');
Route::get('/locations/regions', [ProfileController::class, 'getRegions'])->name('locations.regions');
Route::get('/locations/districts', [ProfileController::class, 'getDistricts'])->name('locations.districts');
Route::get('/locations/wards', [ProfileController::class, 'getWards'])->name('locations.wards');
Route::get('/locations/shehias', [ProfileController::class, 'getShehias'])->name('locations.shehias');

Route::get('/language/switch/{locale}', function (Request $request, string $locale) {
    if (!in_array($locale, ['sw', 'en'], true)) {
        $locale = 'sw';
    }

    $request->session()->put('locale', $locale);

    return back();
})->name('language.switch');

Route::get('/storage/{path}', function (string $path) {
    abort_unless(Storage::disk('public')->exists($path), 404);

    return Storage::disk('public')->response($path);
})->where('path', '.*')->name('storage.fallback');

// ============================================
// AUTH ROUTES
// ============================================

Route::middleware(['guest', 'prevent.back.history'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::get('/forgot-password', [AuthController::class, 'showForgotForm'])->name('password.request');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');

    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ============================================
// EMPLOYER ROUTES
// ============================================

Route::middleware(['auth', 'prevent.back.history', 'isEmployer'])->prefix('employer')->group(function () {
    Route::get('/dashboard', [EmployerJobController::class, 'index'])->name('employer.dashboard');
    Route::post('/jobs', [EmployerJobController::class, 'store'])->name('employer.jobs.store');
    Route::get('/jobs', [EmployerJobController::class, 'getJobs'])->name('employer.jobs.index');
    Route::get('/jobs/{id}', [EmployerJobController::class, 'show'])->name('employer.jobs.show');
    Route::get('/jobs/{id}/attachment', [EmployerJobController::class, 'viewAttachment'])->name('employer.jobs.attachment');
    Route::put('/jobs/{id}', [EmployerJobController::class, 'update'])->name('employer.jobs.update');
    Route::delete('/jobs/{id}', [EmployerJobController::class, 'destroy'])->name('employer.jobs.destroy');
    Route::patch('/jobs/{id}/toggle-status', [EmployerJobController::class, 'toggleStatus'])->name('employer.jobs.toggle-status');
    Route::get('/jobs/{id}/applications', [EmployerJobController::class, 'getApplications'])->name('employer.jobs.applications');
    Route::get('/jobs/{id}/applications/export/{status}', [EmployerJobController::class, 'exportApplicationsByStatus'])->name('employer.jobs.applications.export');
    Route::get('/applications/{id}/profile', [EmployerJobController::class, 'getApplicationProfile'])->name('employer.applications.profile');
    Route::get('/applications/{id}/resume', [EmployerJobController::class, 'viewApplicationResume'])->name('employer.applications.resume');
    Route::get('/applications/{id}/profile-image', [EmployerJobController::class, 'viewApplicationProfileImage'])->name('employer.applications.profile-image');
    Route::patch('/applications/{id}/status', [EmployerJobController::class, 'updateApplicationStatus'])->name('employer.applications.update-status');
    Route::post('/applications/{id}/status', [EmployerJobController::class, 'updateApplicationStatus']);
});


// JOBSEEKER ROUTES 


Route::middleware(['auth', 'prevent.back.history', 'isJobseeker'])->prefix('jobseeker')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [JobseekerDashboardController::class, 'index'])->name('jobseeker.dashboard');
    
    // Jobs 
    Route::get('/jobs', [JobseekerDashboardController::class, 'getJobs'])->name('jobseeker.jobs.index');
    Route::get('/jobs/{id}', [JobseekerDashboardController::class, 'getJobDetails'])->name('jobseeker.jobs.show');
    Route::get('/jobs/{id}/attachment', [JobseekerDashboardController::class, 'viewJobAttachment'])->name('jobseeker.jobs.attachment');
    
    // Applications
    Route::get('/applications', [JobseekerDashboardController::class, 'getMyApplications'])->name('jobseeker.applications.index');
    Route::delete('/applications/{id}', [JobseekerDashboardController::class, 'withdrawApplication'])->name('jobseeker.applications.withdraw');
    
    // Apply for job
    Route::post('/jobs/{id}/apply', [JobseekerDashboardController::class, 'apply'])->name('jobseeker.jobs.apply');
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'updatePersonalInfo'])->name('profile.update');
    Route::post('/profile/complete', [ProfileController::class, 'completeProfile'])->name('profile.complete');
    Route::get('/profile/status', [ProfileController::class, 'status'])->name('profile.status');
    Route::get('/profile/resume', [ProfileController::class, 'viewResume'])->name('profile.resume.view');
    Route::get('/profile/profile-image', [ProfileController::class, 'viewProfileImage'])->name('profile.image.view');
    Route::post('/profile/fetch-zanid', [ProfileController::class, 'fetchZanId'])->name('profile.fetch-zanid');
    
    // AJAX Routes 
    Route::get('/get-districts', [ProfileController::class, 'getDistricts'])->name('get-districts');
    Route::get('/get-wards', [ProfileController::class, 'getWards'])->name('get-wards');
    Route::get('/get-shehias', [ProfileController::class, 'getShehias'])->name('get-shehias');
    
    // Education Routes 
    Route::get('/education', [ProfileController::class, 'getEducation'])->name('education');
    Route::get('/education/{id}', [ProfileController::class, 'getEducationItem'])->name('education.item');
    Route::post('/education', [ProfileController::class, 'storeEducation'])->name('education.store');
    Route::put('/education/{id}', [ProfileController::class, 'updateEducation'])->name('education.update');
    Route::delete('/education/{id}', [ProfileController::class, 'deleteEducation'])->name('education.delete');
    
    // Skills Routes
    Route::post('/skills', [ProfileController::class, 'storeSkill'])->name('skills.store');
    Route::post('/skills/save', [ProfileController::class, 'saveSkills'])->name('skills.save');
    Route::delete('/skills/{id}', [ProfileController::class, 'deleteSkill'])->name('skills.delete');
    
    //Work Experience Routes 
    Route::get('/work-experiences', [ProfileController::class, 'getWorkExperiences'])->name('work-experiences');
    Route::post('/work-experience', [ProfileController::class, 'storeWorkExperience'])->name('work-experience.store');
    Route::put('/work-experience/{id}', [ProfileController::class, 'updateWorkExperience'])->name('work-experience.update');
    Route::delete('/work-experience/{id}', [ProfileController::class, 'deleteWorkExperience'])->name('work-experience.delete');



    });


// ADMIN ROUTES


Route::middleware(['auth', 'prevent.back.history', 'isAdmin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/stats', [AdminDashboardController::class, 'getStats'])->name('admin.stats');
    Route::get('/pending-jobs', [AdminDashboardController::class, 'getPendingJobs'])->name('admin.pending-jobs');
    Route::get('/all-jobs', [AdminDashboardController::class, 'getAllJobs'])->name('admin.all-jobs');
    Route::get('/jobs/{id}', [AdminDashboardController::class, 'getJobDetails'])->name('admin.jobs.show');
    Route::patch('/jobs/{id}/approve', [AdminDashboardController::class, 'approveJob'])->name('admin.jobs.approve');
    Route::post('/jobs/{id}/approve', [AdminDashboardController::class, 'approveJob']);
    Route::patch('/jobs/{id}/reject', [AdminDashboardController::class, 'rejectJob'])->name('admin.jobs.reject');
    Route::post('/jobs/{id}/reject', [AdminDashboardController::class, 'rejectJob']);
    Route::delete('/jobs/{id}', [AdminDashboardController::class, 'deleteJob'])->name('admin.jobs.delete');
    Route::get('/employers', [AdminDashboardController::class, 'getAllEmployers'])->name('admin.employers');
    Route::get('/jobseekers', [AdminDashboardController::class, 'getAllJobseekers'])->name('admin.jobseekers');
    Route::get('/graduates/stats', [AdminDashboardController::class, 'getGraduateStats'])->name('admin.graduates.stats');
    Route::get('/graduates', [AdminDashboardController::class, 'getGraduates'])->name('admin.graduates.index');
    Route::get('/graduates/export/template', [AdminDashboardController::class, 'exportGraduateTemplate'])->name('admin.graduates.export-template');
    Route::get('/graduates/{id}', [AdminDashboardController::class, 'getGraduate'])->name('admin.graduates.show');
    Route::post('/graduates', [AdminDashboardController::class, 'storeGraduate'])->name('admin.graduates.store');
    Route::put('/graduates/{id}', [AdminDashboardController::class, 'updateGraduate'])->name('admin.graduates.update');
    Route::delete('/graduates/{id}', [AdminDashboardController::class, 'deleteGraduate'])->name('admin.graduates.delete');
    Route::post('/graduates/import', [AdminDashboardController::class, 'importGraduates'])->name('admin.graduates.import');
    
    // User Management
    Route::get('/users', [AdminDashboardController::class, 'getUsers'])->name('admin.users');
    Route::get('/users/{id}', [AdminDashboardController::class, 'getUser'])->name('admin.user.show');
    Route::post('/users', [AdminDashboardController::class, 'storeUser'])->name('admin.user.store');
    Route::put('/users/{id}', [AdminDashboardController::class, 'updateUser'])->name('admin.user.update');
    Route::delete('/users/{id}', [AdminDashboardController::class, 'deleteUser'])->name('admin.user.delete');
    Route::patch('/users/{id}/toggle-status', [AdminDashboardController::class, 'toggleUserStatus'])->name('admin.user.toggle-status');
    
    // Reports
    Route::post('/reports/generate', [AdminDashboardController::class, 'generateReport'])->name('admin.reports.generate');
});