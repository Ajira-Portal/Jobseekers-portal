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
use App\Models\WorkExperience;
use App\Services\AjiraApiClient;
use App\Services\SigningService;
use Carbon\Carbon;
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

        try {
            if (Storage::disk('s3_jobattachments')->exists($job->attachment_path)) {
                return Storage::disk('s3_jobattachments')->download(
                    $job->attachment_path,
                    $job->attachment_name ?: basename($job->attachment_path)
                );
            }
        } catch (\Exception $e) {
            Log::warning('S3 job attachment fetch failed: '.$e->getMessage());
        }

        if (! Storage::disk('local')->exists($job->attachment_path)) {
            abort(404, 'Attachment not found.');
        }

        return response()->download(
            Storage::disk('local')->path($job->attachment_path),
            $job->attachment_name ?: basename($job->attachment_path)
        );
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
                if (config('filesystems.default') === 's3') {
                    Storage::disk('s3_coverletter')->putFileAs('', $file, $filename);
                } else {
                    $file->move(public_path('uploads/cover_letters'), $filename);
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

    public function getMyApplications()
    {
        $applications = JobApplication::where('jobseeker_id', Auth::id())
            ->with(['job', 'job.employer'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($app) {
                return [
                    'id' => $app->id,
                    'job_title' => $app->job->title,
                    'company_name' => $app->job->company_name,
                    'location' => $app->job->location,
                    'status' => $app->status === 'pending' ? 'submitted' : $app->status,
                    'cover_letter' => $app->cover_letter,
                    'created_at' => $app->created_at->format('Y-m-d H:i:s'),
                    'employer_name' => $app->job->employer->name,
                ];
            });

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
        $job->decrement('applications_count');

        return response()->json([
            'success' => true,
            'message' => 'Application withdrawn successfully!',
        ]);
    }

    /**
     * Fetch resident details by ZANID using SigningService and AjiraApiClient
     */
    public function fetchZanId(Request $request, SigningService $signingService, AjiraApiClient $ajiraApiClient)
    {
        $validator = Validator::make($request->all(), [
            'resident_number' => 'required|string|regex:/^[0-9]{9,}$/',
        ], [
            'resident_number.regex' => 'The ZANID Number must contain only numbers and be at least 9 digits long.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $zanId = $request->input('resident_number');
        $apiKey = config('services.ajira.token');
        $innerPayload = [
            'apiKey' => $apiKey,
            'zanid' => $zanId,
        ];
        $innerPayloadJson = json_encode($innerPayload);

        try {
            $signature = $signingService->sign($innerPayloadJson);
            $finalRequest = [
                'payload' => $innerPayload,
                'signature' => $signature,
            ];
            Log::info('ZANID Payload: '.json_encode($finalRequest));
        } catch (\Exception $e) {
            Log::error('ZANID Payload Signing Error: '.$e->getMessage(), ['zanid' => $zanId]);

            return response()->json([
                'success' => false,
                'message' => 'Internal configuration error: Unable to securely sign the payload.',
            ], 500);
        }

        try {
            $apiData = $ajiraApiClient->getZanID($finalRequest);
            $payloadObj = $apiData['payload'] ?? null;

            if ($payloadObj && isset($payloadObj['code'])) {
                $code = $payloadObj['code'];
                $msg = $payloadObj['msg'] ?? 'Unknown Error';

                if ($code === 9000 && ! empty($payloadObj['data'])) {
                    $data = $payloadObj['data'];

                    $firstName = $data['PRSN_FIRST_NAME'] ?? '';
                    $middleName = $data['PRSN_MIDLE_NAME'] ?? '';
                    $lastName = $data['PRSN_LAST_NAME'] ?? '';
                    $fullName = trim("{$firstName} {$middleName} {$lastName}");

                    $gender = (strtoupper($data['PRSN_SEX'] ?? '') === 'FEMALE') ? 'female' : 'male';
                    $dob = null;
                    if (! empty($data['PRSN_BIRTH_DATE'])) {
                        try {
                            $dob = Carbon::createFromFormat('j-n-Y', $data['PRSN_BIRTH_DATE'])->format('Y-m-d');
                        } catch (\Exception $e) {
                            $dob = null;
                        }
                    }

                    return response()->json([
                        'success' => true,
                        'message' => 'ZANID details retrieved successfully.',
                        'data' => [
                            'resident_number' => $zanId,
                            'name' => $fullName,
                            'gender' => $gender,
                            'date_of_birth' => $dob,
                            'region_of_birth' => $data['PRSN_BIRTH_PLACE'] ?? null,
                            'district_of_birth' => $data['PRSN_RES_DISTRICT'] ?? null,
                            'shehia' => $data['PRSN_PLACE_SHEHIA'] ?? null,
                            'nationality' => $data['PRSN_NATIONALITY_IND'] ?? 'Tanzanian',
                            'raw' => $data,
                        ],
                    ]);
                } elseif ($code === 9003) {
                    return response()->json([
                        'success' => false,
                        'message' => 'ZANID Not Found. Please check the number and try again.',
                    ], 404);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'ZANID verification service returned an error: '.$msg,
                    ], 400);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Invalid response format from ZANID service.',
            ], 500);

        } catch (\Exception $e) {
            Log::error('ZANID API Exception: '.$e->getMessage(), ['zanid' => $zanId]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to connect to ZANID service. Please try again later.',
            ], 500);
        }
    }

    public function showCompleteProfile()
    {
        return view('jobseeker.complete-profile');
    }

    public function completeProfile(Request $request)
    {
        try {
            $user = Auth::user();

            // Get or create profile
            $profile = $user->personalInfo;
            if (! $profile) {
                $profile = new PersonalInfo;
                $profile->user_id = $user->id;
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'gender' => 'nullable|in:male,female,other',
                'date_of_birth' => 'nullable|date|before:today',
                'disability' => 'required|string|max:255',
                'regions' => 'required|exists:regions,id',
                'districts' => 'required|exists:districts,id',
                'wards' => 'required|exists:wards,id',
                'shehias' => 'required|exists:shehias,id',

                'resume' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
                'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Update user name
            $user->name = $request->name;
            $user->save();

            // Update profile
            $profile->phone = $request->phone;
            $profile->gender = $request->gender;
            $profile->date_of_birth = $request->date_of_birth;

            $profile->profile_completed = true;

            // Handle resume upload
            if ($request->hasFile('resume')) {
                if ($profile->resume && file_exists(public_path('uploads/resumes/'.$profile->resume))) {
                    @unlink(public_path('uploads/resumes/'.$profile->resume));
                }
                $file = $request->file('resume');
                $filename = time().'_'.$user->id.'_resume.'.$file->getClientOriginalExtension();
                if (config('filesystems.default') === 's3') {
                    Storage::disk('s3_resume')->putFileAs('', $file, $filename);
                } else {
                    $file->move(public_path('uploads/resumes'), $filename);
                }
                $profile->resume = $filename;
            }

            // Handle profile image
            if ($request->hasFile('profile_image')) {
                if ($profile->profile_image && file_exists(public_path('uploads/profile_images/'.$profile->profile_image))) {
                    @unlink(public_path('uploads/profile_images/'.$profile->profile_image));
                }
                $file = $request->file('profile_image');
                $filename = time().'_'.$user->id.'_profile.'.$file->getClientOriginalExtension();
                if (config('filesystems.default') === 's3') {
                    Storage::disk('s3_profilephoto')->putFileAs('', $file, $filename);
                } else {
                    $file->move(public_path('uploads/profile_images'), $filename);
                }
                $profile->profile_image = $filename;
            }

            $profile->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile completed successfully! You can now apply for jobs.',
                'profile_completed' => true,
            ]);

        } catch (\Exception $e) {
            Log::error('Error completing profile:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error completing profile: '.$e->getMessage(),
            ], 500);
        }
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

    // ============================================
    // WORK EXPERIENCE CRUD
    // ============================================

    public function getWorkExperiences()
    {
        try {
            $experiences = WorkExperience::where('user_id', Auth::id())
                ->orderBy('start_date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'experiences' => $experiences,
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading work experiences:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading work experiences.',
            ], 500);
        }
    }

    public function getWorkExperience($id)
    {
        try {
            $experience = WorkExperience::where('user_id', Auth::id())->findOrFail($id);

            return response()->json([
                'success' => true,
                'experience' => $experience,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Work experience not found.',
            ], 404);
        }
    }

    public function addWorkExperience(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'company_name' => 'required|string|max:255',
                'position' => 'required|string|max:255',
                'location' => 'nullable|string|max:255',
                'employment_type' => 'nullable|string|max:50',
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

            $experience = WorkExperience::create([
                'user_id' => Auth::id(),
                'company_name' => $request->company_name,
                'position' => $request->position,
                'location' => $request->location,
                'employment_type' => $request->employment_type,
                'start_date' => $request->start_date,
                'end_date' => $request->is_current ? null : $request->end_date,
                'is_current' => $request->is_current ?? false,
                'description' => $request->description,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Work experience added successfully!',
                'experience' => $experience,
            ]);

        } catch (\Exception $e) {
            Log::error('Error adding work experience:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error adding work experience: '.$e->getMessage(),
            ], 500);
        }
    }

    public function updateWorkExperience(Request $request, $id)
    {
        try {
            $experience = WorkExperience::where('user_id', Auth::id())->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'company_name' => 'required|string|max:255',
                'position' => 'required|string|max:255',
                'location' => 'nullable|string|max:255',
                'employment_type' => 'nullable|string|max:50',
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

            $experience->update([
                'company_name' => $request->company_name,
                'position' => $request->position,
                'location' => $request->location,
                'employment_type' => $request->employment_type,
                'start_date' => $request->start_date,
                'end_date' => $request->is_current ? null : $request->end_date,
                'is_current' => $request->is_current ?? false,
                'description' => $request->description,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Work experience updated successfully!',
                'experience' => $experience,
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating work experience:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating work experience: '.$e->getMessage(),
            ], 500);
        }
    }

    public function deleteWorkExperience($id)
    {
        try {
            $experience = WorkExperience::where('user_id', Auth::id())->findOrFail($id);
            $experience->delete();

            return response()->json([
                'success' => true,
                'message' => 'Work experience deleted successfully!',
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting work experience:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting work experience: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get full profile for employer view
     */
    public function getFullProfile($userId)
    {
        try {
            $user = User::with(['jobseekerProfile', 'education', 'workExperiences'])
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
                    'work_experiences' => $user->workExperiences,
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
