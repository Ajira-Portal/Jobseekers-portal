<?php

namespace App\Http\Controllers\Jobseeker;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Education;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Region;
use App\Models\Shehia;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    //  public static function middleware()
    // {
    //     return [
    //         new Middleware('auth'),
    //         new Middleware('isAdmin'),
    //     ];
    // }

    public function getPublicJobs(Request $request)
    {
        try {
            $query = Job::where('is_active', true)
                ->where('is_approved', true)
                ->select([
                    'id',
                    'employer_id',
                    'title',
                    'company_name',
                    'location',
                    'employment_type',
                    'posts',
                    'description',
                    'requirements',
                    'application_deadline',
                    'is_active',
                    'is_approved',
                    'views_count',
                    'applications_count',
                    'created_at',
                    'updated_at',
                ]);

            // Search filter
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                        ->orWhere('company_name', 'LIKE', "%{$search}%")
                        ->orWhere('location', 'LIKE', "%{$search}%");
                });
            }

            // Location filter
            if ($request->has('location') && $request->location) {
                $query->where('location', 'LIKE', "%{$request->location}%");
            }

            // Employment type filter
            if ($request->has('employment_type') && $request->employment_type) {
                $query->where('employment_type', $request->employment_type);
            }

            $perPage = $request->has('per_page') ? (int) $request->per_page : 9;
            $jobs = $query->with('employer')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'jobs' => $jobs,
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading public jobs:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading jobs: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get single job details (no login required)
     */
    public function getPublicJobDetails($id)
    {
        try {
            $job = Job::where('is_active', true)
                ->where('is_approved', true)
                ->with('employer')
                ->findOrFail($id);

            // Check if user is logged in to show if they've applied
            $hasApplied = false;
            if (Auth::check()) {
                $hasApplied = JobApplication::where('job_id', $id)
                    ->where('jobseeker_id', Auth::id())
                    ->exists();
            }

            return response()->json([
                'success' => true,
                'job' => [
                    'id' => $job->id,
                    'title' => $job->title,
                    'company_name' => $job->company_name,
                    'location' => $job->location,
                    'employment_type' => $job->employment_type,
                    'posts' => $job->posts,
                    'description' => $job->description,
                    'requirements' => $job->requirements,
                    'application_deadline' => $job->application_deadline ? $job->application_deadline->format('Y-m-d') : null,
                    'created_at' => $job->created_at->format('Y-m-d H:i:s'),
                    'employer' => [
                        'name' => $job->employer->name,
                        'email' => $job->employer->email,
                        'company_name' => $job->employer->company_name,
                    ],
                ],
                'hasApplied' => $hasApplied,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Job not found',
            ], 404);
        }
    }

    public function index()
    {
        $user = Auth::user();
        \App\Models\JobseekerProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['resident_number' => $user->zan_id, 'phone' => $user->phone]
        );

        $regions = Region::where('is_active', true)->orderBy('name')->get();
        $districts = District::where('is_active', true)->orderBy('name')->get();
        $wards = Ward::where('is_active', true)->orderBy('name')->get();
        $shehias = Shehia::where('is_active', true)->orderBy('name')->get();

        return view('jobseeker.dashboard', compact('regions', 'districts', 'wards', 'shehias'));
    }

    public function getJobs(Request $request)
    {
        $query = Job::where('is_active', true)
            ->where('is_approved', true);

        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'LIKE', "%{$request->search}%")
                    ->orWhere('company_name', 'LIKE', "%{$request->search}%")
                    ->orWhere('location', 'LIKE', "%{$request->search}%")
                    ->orWhere('description', 'LIKE', "%{$request->search}%");
            });
        }

        if ($request->has('employment_type') && $request->employment_type) {
            $query->where('employment_type', $request->employment_type);
        }

        if ($request->has('location') && $request->location) {
            $query->where('location', 'LIKE', "%{$request->location}%");
        }

        $perPage = $request->has('per_page') ? $request->per_page : 10;
        $jobs = $query->with('employer')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $jobItems = collect($jobs->items())->map(function ($job) {
            return [
                'id' => $job->id,
                'title' => $job->title,
                'company_name' => $job->company_name,
                'location' => $job->location,
                'employment_type' => $job->employment_type,
                'posts' => $job->posts,
                'description' => $job->description,
                'requirements' => $job->requirements,
                'application_deadline' => $job->application_deadline ? $job->application_deadline->format('Y-m-d') : null,
                'created_at' => $job->created_at->format('Y-m-d H:i:s'),
                'employer' => [
                    'id' => $job->employer->id,
                    'name' => $job->employer->name,
                    'company_name' => $job->employer->company_name,
                ],
                'has_attachment' => ! empty($job->attachment_path),
                'attachment_name' => $job->attachment_name,
                'attachment_mime' => $job->attachment_mime,
                'attachment_url' => ! empty($job->attachment_path) ? route('jobseeker.jobs.attachment', ['id' => $job->id]) : null,
            ];
        });

        return response()->json([
            'success' => true,
            'jobs' => [
                'data' => $jobItems,
                'total' => $jobs->total(),
                'per_page' => $jobs->perPage(),
                'current_page' => $jobs->currentPage(),
                'last_page' => $jobs->lastPage(),
                'links' => $jobs->links()->toHtml(),
            ],
        ]);
    }

    public function getJobDetails($id)
    {
        $job = Job::where('is_active', true)
            ->where('is_approved', true)
            ->with('employer')
            ->findOrFail($id);

        $job->incrementViews();

        $hasApplied = JobApplication::where('job_id', $id)
            ->where('jobseeker_id', Auth::id())
            ->exists();

        return response()->json([
            'success' => true,
            'job' => [
                'id' => $job->id,
                'title' => $job->title,
                'company_name' => $job->company_name,
                'location' => $job->location,
                'employment_type' => $job->employment_type,

                'description' => $job->description,
                'requirements' => $job->requirements,
                'has_attachment' => ! empty($job->attachment_path),
                'attachment_name' => $job->attachment_name,
                'attachment_mime' => $job->attachment_mime,
                'attachment_url' => ! empty($job->attachment_path) ? route('jobseeker.jobs.attachment', ['id' => $job->id]) : null,

                'application_deadline' => $job->application_deadline ? $job->application_deadline->format('Y-m-d') : null,
                'created_at' => $job->created_at->format('Y-m-d H:i:s'),
                'employer' => [
                    'name' => $job->employer->name,
                    'email' => $job->employer->email,
                    'company_name' => $job->employer->company_name,
                ],
            ],
            'hasApplied' => $hasApplied,
        ]);
    }

    public function viewJobAttachment($id)
    {
        $job = Job::where('is_active', true)
            ->where('is_approved', true)
            ->findOrFail($id);

        if (empty($job->attachment_path)) {
            abort(404, 'Attachment not found.');
        }

        $name = $job->attachment_name ?: basename($job->attachment_path);
        $headers = [
            'Content-Type' => $job->attachment_mime ?: 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.addslashes($name).'"',
        ];

        try {
            if (Storage::disk('s3_jobattachments')->exists($job->attachment_path)) {
                return Storage::disk('s3_jobattachments')->response($job->attachment_path, $name, $headers);
            }
        } catch (\Exception $e) {
            Log::warning('S3 job attachment fetch failed: '.$e->getMessage());
        }

        if (! Storage::disk('local')->exists($job->attachment_path)) {
            abort(404, 'Attachment not found.');
        }

        return response()->file(Storage::disk('local')->path($job->attachment_path), $headers);
    }

    public function apply(Request $request, $jobId)
    {
        try {
            $job = Job::where('is_active', true)
                ->where('is_approved', true)
                ->findOrFail($jobId);

            $existing = JobApplication::where('job_id', $jobId)
                ->where('jobseeker_id', Auth::id())
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already applied for this job.',
                ], 400);
            }

            $validator = Validator::make($request->all(), [
                'cover_letter' => 'required|file|mimes:pdf|max:2048', // 2MB max
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $coverLetterPath = null;
            if ($request->hasFile('cover_letter')) {
                $file = $request->file('cover_letter');
                $filename = time().'_'.Auth::id().'_cover_letter.pdf';

                if (! $this->storeCoverLetter($file, $filename)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Could not save your cover letter. Please try again.',
                    ], 500);
                }
                $coverLetterPath = $filename;
            }

            $application = JobApplication::create([
                'job_id' => $jobId,
                'jobseeker_id' => Auth::id(),
                'cover_letter' => $coverLetterPath,
                'status' => 'submitted',
            ]);

            $job->incrementApplications();

            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully!',
                'application' => $application,
            ]);

        } catch (\Exception $e) {
            Log::error('Error applying for job:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error submitting application: '.$e->getMessage(),
            ], 500);
        }
    }

    // S3 disks use throw=false, so a failed upload returns false; verify it and fall back to local storage.
    private function storeCoverLetter($file, string $filename): bool
    {
        if (config('filesystems.default') === 's3') {
            try {
                if (Storage::disk('s3_coverletter')->putFileAs('', $file, $filename) !== false) {
                    return true;
                }
            } catch (\Exception $e) {
                Log::error('S3 cover letter upload error: '.$e->getMessage());
            }
            Log::warning('S3 cover letter upload failed, falling back to local storage');
        }

        try {
            $file->move(public_path('uploads/cover_letters'), $filename);

            return true;
        } catch (\Exception $e) {
            Log::error('Local cover letter save failed: '.$e->getMessage());

            return false;
        }
    }

    public function getMyApplications()
    {
        $applications = JobApplication::where('jobseeker_id', Auth::id())
            ->with(['job', 'job.employer'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->filter(fn ($app) => $app->job !== null)
            ->map(function ($app) {
                return [
                    'id' => $app->id,
                    'job_title' => $app->job->title,
                    'company_name' => $app->job->company_name,
                    'location' => $app->job->location,
                    'status' => $app->status === 'pending' ? 'submitted' : $app->status,
                    'cover_letter' => $app->cover_letter,
                    'created_at' => $app->created_at->format('Y-m-d H:i:s'),
                    'employer_name' => $app->job->employer->name ?? '',
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'applications' => $applications,
        ]);
    }

    public function withdrawApplication($applicationId)
    {
        $application = JobApplication::where('jobseeker_id', Auth::id())
            ->findOrFail($applicationId);

        if (! in_array($application->status, ['submitted'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot withdraw application after it has been reviewed.',
            ], 400);
        }

        $job = $application->job;
        $application->delete();
        $job?->decrement('applications_count');

        return response()->json([
            'success' => true,
            'message' => 'Application withdrawn successfully!',
        ]);
    }

    // ============================================
    // EDUCATION CRUD
    // ============================================

    public function getEducation()
    {
        try {
            $education = Education::where('user_id', Auth::id())
                ->orderBy('start_date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'education' => $education,
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading education:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading education.',
            ], 500);
        }
    }

    public function getEducationItem($id)
    {
        try {
            $education = Education::where('user_id', Auth::id())->findOrFail($id);

            return response()->json([
                'success' => true,
                'education' => $education,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Education not found.',
            ], 404);
        }
    }

    public function addEducation(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'institution' => 'required|string|max:255',
                'degree' => 'required|string|max:255',
                'field_of_study' => 'nullable|string|max:255',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after:start_date',
                'is_current' => 'boolean',
                'description' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $education = Education::create([
                'user_id' => Auth::id(),
                'institution' => $request->institution,
                'degree' => $request->degree,
                'field_of_study' => $request->field_of_study,
                'start_date' => $request->start_date,
                'end_date' => $request->is_current ? null : $request->end_date,
                'is_current' => $request->is_current ?? false,
                'description' => $request->description,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Education added successfully!',
                'education' => $education,
            ]);

        } catch (\Exception $e) {
            Log::error('Error adding education:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error adding education: '.$e->getMessage(),
            ], 500);
        }
    }

    public function updateEducation(Request $request, $id)
    {
        try {
            $education = Education::where('user_id', Auth::id())->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'institution' => 'required|string|max:255',
                'degree' => 'required|string|max:255',
                'field_of_study' => 'nullable|string|max:255',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after:start_date',
                'is_current' => 'boolean',
                'description' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $education->update([
                'institution' => $request->institution,
                'degree' => $request->degree,
                'field_of_study' => $request->field_of_study,
                'start_date' => $request->start_date,
                'end_date' => $request->is_current ? null : $request->end_date,
                'is_current' => $request->is_current ?? false,
                'description' => $request->description,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Education updated successfully!',
                'education' => $education,
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating education:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating education: '.$e->getMessage(),
            ], 500);
        }
    }

    public function deleteEducation($id)
    {
        try {
            $education = Education::where('user_id', Auth::id())->findOrFail($id);
            $education->delete();

            return response()->json([
                'success' => true,
                'message' => 'Education deleted successfully!',
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting education:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting education: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get full profile for employer view
     */
    public function getFullProfile($userId)
    {
        try {
            $user = User::with(['jobseekerProfile', 'education'])
                ->findOrFail($userId);

            $profile = $user->jobseekerProfile;

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'profile' => $profile ? [
                        'phone' => $profile->phone,
                        'gender' => $profile->gender,
                        'gender_label' => $profile->gender_label,
                        'date_of_birth' => $profile->date_of_birth,
                        'age' => $profile->age,
                        'address' => $profile->address,
                        'bio' => $profile->bio,
                        'skills' => $profile->skills,
                        'skills_array' => $profile->skills_array,
                        'experience_years' => $profile->experience_years,
                        'experience_label' => $profile->experience_label,
                        'profile_image_url' => $profile->profile_image_url,
                        'resume_url' => $profile->resume_url,
                        'linkedin_url' => $profile->linkedin_url,
                        'github_url' => $profile->github_url,
                        'website_url' => $profile->website_url,
                    ] : null,
                    'education' => $user->education,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting full profile:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading profile.',
            ], 500);
        }
    }
}
