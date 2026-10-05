<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class JobController extends Controller
{
    private const EMPLOYMENT_TYPES = [
        'full-time',
        'part-time',
        'contract',
        'permanent-contract',
        'temporary-contract',
        'part-time-contract',
        'daily-paid-contract',
    ];

    public function index()
    {
        return view('employer.dashboard');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'employment_type' => 'required|string|in:'.implode(',', self::EMPLOYMENT_TYPES),
            'posts' => 'nullable|string|max:100',
            'description' => 'required|string|min:50',
            'requirements' => 'required|string|min:50',
            'attachment' => 'nullable|file|mimes:pdf|max:5120',
            'application_deadline' => 'nullable|date|after:today',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $payload = [
            'employer_id' => Auth::id(),
            'title' => $request->title,
            'company_name' => $request->company_name,
            'location' => $request->location,
            'employment_type' => $this->normalizeEmploymentType($request->employment_type),
            'posts' => $request->posts,
            'description' => $request->description,
            'requirements' => $request->requirements,
            'application_deadline' => $request->application_deadline,
            'is_active' => $request->has('is_active'),
            'is_approved' => false,
            'is_rejected' => false,
            'rejection_reason' => null,
        ];

        if ($request->hasFile('attachment')) {
            $payload = array_merge($payload, $this->storeJobAttachment($request->file('attachment')));
        }

        $job = Job::create($payload);

        return response()->json([
            'success' => true,
            'message' => 'Job posted successfully! Waiting for admin approval.',
            'job' => $job,
        ], 201);
    }

    public function show($id)
    {
        $job = Job::where('employer_id', Auth::id())->findOrFail($id);

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

                'is_active' => $job->is_active,
                'is_approved' => $job->is_approved,
                'is_rejected' => (bool) $job->is_rejected,
                'rejection_reason' => $job->rejection_reason,
                'has_attachment' => ! empty($job->attachment_path),
                'attachment_name' => $job->attachment_name,
                'attachment_mime' => $job->attachment_mime,
                'attachment_url' => ! empty($job->attachment_path) ? route('employer.jobs.attachment', ['id' => $job->id]) : null,
                'created_at' => $job->created_at->format('Y-m-d H:i:s'),
                'application_deadline' => $job->application_deadline ? $job->application_deadline->format('Y-m-d') : null,
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        $job = Job::where('employer_id', Auth::id())->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'employment_type' => 'required|string|in:'.implode(',', self::EMPLOYMENT_TYPES),
            'posts' => 'nullable|string|max:100',

            'description' => 'required|string|min:50',
            'requirements' => 'required|string|min:50',
            'attachment' => 'nullable|file|mimes:pdf|max:5120',
            'remove_attachment' => 'nullable|boolean',

            'application_deadline' => 'nullable|date|after:today',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $payload = [
            'title' => $request->title,
            'company_name' => $request->company_name,
            'location' => $request->location,
            'employment_type' => $this->normalizeEmploymentType($request->employment_type),
            'posts' => $request->posts,

            'description' => $request->description,
            'requirements' => $request->requirements,

            'application_deadline' => $request->application_deadline,
            'is_active' => $request->has('is_active'),
            'is_approved' => false,
            'is_rejected' => false,
            'rejection_reason' => null,
        ];

        if ($request->boolean('remove_attachment')) {
            $this->deleteJobAttachment($job);
            $payload['attachment_path'] = null;
            $payload['attachment_name'] = null;
            $payload['attachment_mime'] = null;
            $payload['attachment_size'] = null;
        }

        if ($request->hasFile('attachment')) {
            $this->deleteJobAttachment($job);
            $payload = array_merge($payload, $this->storeJobAttachment($request->file('attachment')));
        }

        $job->update($payload);

        return response()->json([
            'success' => true,
            'message' => 'Job updated successfully! Waiting for admin approval.',
            'job' => $job,
        ]);
    }

    public function destroy($id)
    {
        $job = Job::where('employer_id', Auth::id())->findOrFail($id);
        $this->deleteJobAttachment($job);
        $job->delete();

        return response()->json([
            'success' => true,
            'message' => 'Job deleted successfully!',
        ]);
    }

    public function getJobs()
    {
        $jobs = Job::where('employer_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($job) {
                return [
                    'id' => $job->id,
                    'title' => $job->title,
                    'company_name' => $job->company_name,
                    'location' => $job->location,
                    'employment_type' => $job->employment_type,
                    'posts' => $job->posts,

                    'description' => $job->description,
                    'is_active' => $job->is_active,
                    'is_approved' => $job->is_approved,
                    'is_rejected' => (bool) $job->is_rejected,
                    'rejection_reason' => $job->rejection_reason,
                    'has_attachment' => ! empty($job->attachment_path),
                    'attachment_name' => $job->attachment_name,
                    'attachment_mime' => $job->attachment_mime,
                    'attachment_url' => ! empty($job->attachment_path) ? route('employer.jobs.attachment', ['id' => $job->id]) : null,
                    'created_at' => $job->created_at->format('Y-m-d H:i:s'),
                    'application_deadline' => $job->application_deadline ? $job->application_deadline->format('Y-m-d') : null,
                ];
            });

        return response()->json([
            'success' => true,
            'jobs' => $jobs,
            'data' => $jobs,
        ]);
    }

    public function toggleStatus($id)
    {
        $job = Job::where('employer_id', Auth::id())->findOrFail($id);
        $job->is_active = ! $job->is_active;
        $job->save();

        return response()->json([
            'success' => true,
            'message' => 'Job status updated successfully!',
            'is_active' => $job->is_active,
        ]);
    }

    public function viewAttachment($id)
    {
        $job = Job::where('employer_id', Auth::id())->findOrFail($id);

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

    public function getApplications($jobId)
    {
        $job = Job::where('employer_id', Auth::id())->findOrFail($jobId);
        $applications = $job->applications()
            ->with(['jobseeker.jobseekerProfile', 'jobseeker.personalInfo'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($app) {
                $profileSource = $app->jobseeker->jobseekerProfile ?: $app->jobseeker->personalInfo;

                $resumePath = $this->findApplicantFilePath($profileSource->resume ?? null, 'resume');
                $resumeUrl = $resumePath ? route('employer.applications.resume', ['id' => $app->id]) : null;

                $profileImagePath = $this->findApplicantFilePath($profileSource->profile_image ?? null, 'profile_image');
                $profileImageUrl = $profileImagePath ? route('employer.applications.profile-image', ['id' => $app->id]) : null;

                $coverLetterUrl = null;
                if (! empty($app->cover_letter) && file_exists(public_path('uploads/cover_letters/'.$app->cover_letter))) {
                    $coverLetterUrl = asset('uploads/cover_letters/'.$app->cover_letter);
                }

                return [
                    'id' => $app->id,
                    'jobseeker_name' => $app->jobseeker->name,
                    'jobseeker_email' => $app->jobseeker->email,
                    'jobseeker_phone' => $profileSource->phone ?? null,
                    'cover_letter' => $app->cover_letter,
                    'has_profile' => (bool) $profileSource,
                    'has_resume' => (bool) $resumeUrl,
                    'has_cover_letter' => (bool) $coverLetterUrl,
                    'resume_url' => $resumeUrl,
                    'cover_letter_url' => $coverLetterUrl,
                    'has_profile_image' => (bool) $profileImageUrl,
                    'profile_image_url' => $profileImageUrl,
                    'status' => $app->status === 'pending' ? 'submitted' : $app->status,
                    'created_at' => $app->created_at->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json([
            'success' => true,
            'applications' => $applications,
        ]);
    }

    public function exportApplicationsByStatus($jobId, $status)
    {
        $allowedStatuses = ['shortlisted', 'hired'];
        if (! in_array($status, $allowedStatuses, true)) {
            abort(422, 'Invalid export status.');
        }

        $job = Job::where('employer_id', Auth::id())->findOrFail($jobId);

        $applications = $job->applications()
            ->where('status', $status)
            ->with(['jobseeker.jobseekerProfile', 'jobseeker.personalInfo'])
            ->orderBy('created_at', 'desc')
            ->get();

        $safeJobTitle = Str::slug((string) $job->title);
        $safeJobTitle = $safeJobTitle !== '' ? $safeJobTitle : 'job';
        $fileName = $safeJobTitle.'-'.$status.'-applicants-'.now()->format('Ymd_His').'.csv';

        return response()->streamDownload(function () use ($applications, $status, $job) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Application ID',
                'Job Title',
                'Applicant Name',
                'Applicant Email',
                'Applicant Phone',
                'Status',
                'Applied Date',
            ]);

            foreach ($applications as $application) {
                $profileSource = $application->jobseeker->jobseekerProfile ?: $application->jobseeker->personalInfo;

                fputcsv($handle, [
                    $application->id,
                    $job->title,
                    $application->jobseeker->name,
                    $application->jobseeker->email,
                    $profileSource->phone ?? '',
                    $status,
                    optional($application->created_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function getApplicationProfile($applicationId)
    {
        $application = JobApplication::with([
            'job',
            'jobseeker.jobseekerProfile.region',
            'jobseeker.jobseekerProfile.district',
            'jobseeker.jobseekerProfile.ward',
            'jobseeker.jobseekerProfile.shehia',
            'jobseeker.personalInfo',
            'jobseeker.education',
            // 'jobseeker.workExperiences',
        ])->findOrFail($applicationId);

        if ($application->job->employer_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $user = $application->jobseeker;
        $profile = $user->jobseekerProfile;
        $personalInfo = $user->personalInfo;
        $profileSource = $profile ?: $personalInfo;

        $resumePath = $this->findApplicantFilePath($profileSource->resume ?? null, 'resume');
        $resumeUrl = $resumePath ? route('employer.applications.resume', ['id' => $application->id]) : null;

        $profileImagePath = $this->findApplicantFilePath($profileSource->profile_image ?? null, 'profile_image');
        $profileImageUrl = $profileImagePath ? route('employer.applications.profile-image', ['id' => $application->id]) : null;

        $dateOfBirth = $profileSource?->date_of_birth;
        $computedAge = $dateOfBirth ? $dateOfBirth->age : null;

        return response()->json([
            'success' => true,
            'application' => [
                'id' => $application->id,
                'status' => $application->status === 'pending' ? 'submitted' : $application->status,
                'cover_letter' => $application->cover_letter,
                'created_at' => optional($application->created_at)->format('Y-m-d H:i:s'),
                'job' => [
                    'id' => $application->job->id,
                    'title' => $application->job->title,
                    'company_name' => $application->job->company_name,
                ],
                'applicant' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $profileSource->phone ?? null,
                    'gender' => $profileSource->gender ?? null,
                    'gender_label' => $profileSource?->gender ? ucfirst($profileSource->gender) : null,
                    'date_of_birth' => $dateOfBirth?->format('Y-m-d'),
                    'age' => $computedAge,
                    'address' => $profileSource->address ?? null,
                    'bio' => $profileSource->bio ?? null,
                    'skills' => $this->getApplicantSkills($user, $profileSource),
                    'region' => $profile && $profile->region ? $profile->region->name : null,
                    'district' => $profile && $profile->district ? $profile->district->name : null,
                    'ward' => $profile && $profile->ward ? $profile->ward->name : null,
                    'shehia' => $profile && $profile->shehia ? $profile->shehia->name : null,
                    'profile_image_url' => $profileImageUrl,
                    'resume_url' => $resumeUrl,
                    'education' => $user->education->map(function ($education) {
                        return [
                            'type_label' => $education->type_label,
                            'level_label' => $education->level_label,
                            'institution_display' => $education->institution_display,
                            'course_display' => $education->course_display,
                            'duration' => $education->duration,
                        ];
                    })->values(),
                    // 'work_experiences' => $user->workExperiences->map(function ($experience) {
                    //     return [
                    //         'company_name' => $experience->company_name,
                    //         'position' => $experience->position,
                    //         'location' => $experience->location,
                    //         'employment_type_label' => $experience->employment_type_label,
                    //         'duration' => $experience->duration,
                    //         'description' => $experience->description,
                    //     ];
                    // })->values(),
                ],
            ],
        ]);
    }

    public function viewApplicationResume($applicationId)
    {
        return $this->serveApplicantFile($applicationId, 'resume');
    }

    public function viewApplicationProfileImage($applicationId)
    {
        return $this->serveApplicantFile($applicationId, 'profile_image');
    }

    private function serveApplicantFile($applicationId, $type)
    {
        $application = JobApplication::with([
            'job',
            'jobseeker.jobseekerProfile',
            'jobseeker.personalInfo',
        ])->findOrFail($applicationId);

        if ($application->job->employer_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $profileSource = $application->jobseeker->jobseekerProfile ?: $application->jobseeker->personalInfo;
        if (! $profileSource) {
            abort(404, 'Profile not found');
        }

        $fileName = $type === 'resume' ? ($profileSource->resume ?? null) : ($profileSource->profile_image ?? null);
        if (empty($fileName)) {
            abort(404, 'File not found');
        }

        $diskName = $type === 'resume' ? 's3_resume' : 's3_profilephoto';
        try {
            if (Storage::disk($diskName)->exists($fileName)) {
                return Storage::disk($diskName)->response($fileName, basename($fileName), [
                    'Content-Disposition' => 'inline; filename="'.basename($fileName).'"',
                ]);
            }
        } catch (\Exception $e) {
            Log::warning("S3 applicant file fetch failed ($type): ".$e->getMessage());
        }

        $path = $this->findApplicantFilePath($fileName, $type);

        if (! $path) {
            abort(404, 'File not found');
        }

        return response()->file($path);
    }

    private function findApplicantFilePath($fileName, $type)
    {
        if (empty($fileName)) {
            return null;
        }

        $safeFileName = basename($fileName);
        if ($safeFileName === '') {
            return null;
        }

        $folders = $type === 'resume'
            ? ['resumes', 'uploads/resumes', 'uploads/jobseekers/resumes']
            : ['profile_images', 'uploads/profile_images', 'uploads/jobseekers/profile_images'];

        $candidates = [];
        foreach ($folders as $folder) {
            $candidates[] = storage_path('app/public/'.$folder.'/'.$safeFileName);
            $candidates[] = storage_path('app/private/public/'.$folder.'/'.$safeFileName);
            $candidates[] = public_path($folder.'/'.$safeFileName);
            $candidates[] = public_path('storage/'.$folder.'/'.$safeFileName);
        }

        foreach ($candidates as $candidate) {
            if (file_exists($candidate) && is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function getApplicantSkills($user, $profileSource)
    {
        $profileSkills = [];

        if ($profileSource && ! empty($profileSource->skills)) {
            $profileSkills = array_filter(array_map('trim', explode(',', (string) $profileSource->skills)));
        }

        if ($profileSource && isset($profileSource->skills_array) && is_array($profileSource->skills_array)) {
            $profileSkills = array_merge($profileSkills, $profileSource->skills_array);
        }

        $tableSkills = Skill::where('user_id', $user->id)
            ->pluck('skills_name')
            ->map(function ($skill) {
                return trim((string) $skill);
            })
            ->filter()
            ->values()
            ->all();

        $merged = array_merge($profileSkills, $tableSkills);
        $unique = [];
        $seen = [];

        foreach ($merged as $skill) {
            $normalized = strtolower(trim((string) $skill));
            if ($normalized === '' || in_array($normalized, $seen, true)) {
                continue;
            }
            $seen[] = $normalized;
            $unique[] = trim((string) $skill);
        }

        return $unique;
    }

    public function updateApplicationStatus(Request $request, $applicationId)
    {
        $application = JobApplication::findOrFail($applicationId);

        if ($application->job->employer_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:submitted,pending,reviewed,shortlisted,rejected,hired',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $application->status = $request->status;
        $application->reviewed_at = now();
        $application->save();

        return response()->json([
            'success' => true,
            'message' => 'Application status updated successfully!',
        ]);
    }

    private function storeJobAttachment($file): array
    {
        $disk = config('filesystems.default') === 's3' ? 's3_jobattachments' : 'local';
        $safeOriginalName = basename((string) $file->getClientOriginalName());
        $storedName = now()->format('YmdHis').'_'.Auth::id().'_'.Str::random(8).'_'.$safeOriginalName;
        $path = $disk === 's3_jobattachments'
            ? $file->storeAs('', $storedName, 's3_jobattachments')
            : $file->storeAs('job_attachments', $storedName, 'local');

        return [
            'attachment_path' => $path,
            'attachment_name' => $safeOriginalName,
            'attachment_mime' => $file->getClientMimeType(),
            'attachment_size' => $file->getSize(),
        ];
    }

    private function deleteJobAttachment(Job $job): void
    {
        if (! empty($job->attachment_path)) {
            if (Storage::disk('s3_jobattachments')->exists($job->attachment_path)) {
                Storage::disk('s3_jobattachments')->delete($job->attachment_path);
            }
            if (Storage::disk('local')->exists($job->attachment_path)) {
                Storage::disk('local')->delete($job->attachment_path);
            }
        }
    }

    private function normalizeEmploymentType(?string $employmentType): ?string
    {
        return match ($employmentType) {
            'full-time' => 'permanent-contract',
            'part-time' => 'part-time-contract',
            'contract' => 'daily-paid-contract',
            default => $employmentType,
        };
    }
}
