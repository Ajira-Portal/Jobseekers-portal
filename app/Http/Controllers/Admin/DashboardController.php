<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Graduate;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Region;
use App\Models\Shehia;
use App\Models\User;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function getStats()
    {
        try {
            $stats = [
                'total_users' => User::count(),
                'total_employers' => User::where('role', 'employer')->count(),
                'total_jobseekers' => User::where('role', 'jobseeker')->count(),
                'total_jobs' => Job::count(),
                'pending_jobs' => Job::where('is_approved', false)->where('is_rejected', false)->count(),
                'active_jobs' => Job::where('is_active', true)->where('is_approved', true)->count(),
                'total_applications' => JobApplication::count(),
                'recent_applications' => JobApplication::with(['job', 'jobseeker'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get()
                    ->map(function ($app) {
                        return [
                            'id' => $app->id,
                            'job_title' => $app->job->title,
                            'applicant_name' => $app->jobseeker->name,
                            'status' => $app->status === 'pending' ? 'submitted' : $app->status,
                            'created_at' => $app->created_at->format('Y-m-d H:i:s'),
                        ];
                    }),
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting stats:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading statistics.',
            ], 500);
        }
    }

    public function getGraduateStats()
    {
        try {
            $total = Graduate::count();
            $active = Graduate::where('status', 'active')->count();
            $employed = Graduate::where('status', 'employed')->count();
            $unemployed = Graduate::where('status', 'unemployed')->count();

            return response()->json([
                'success' => true,
                'stats' => [
                    'total' => $total,
                    'active' => $active,
                    'employed' => $employed,
                    'unemployed' => $unemployed,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting graduate stats:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading graduate statistics.',
            ], 500);
        }
    }

    public function getPendingJobs()
    {
        try {
            $jobs = Job::where('is_approved', false)
                ->where('is_rejected', false)
                ->with('employer')
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($job) {
                    return [
                        'id' => $job->id,
                        'title' => $job->title,
                        'company_name' => $job->company_name,
                        'location' => $job->location,
                        'employment_type' => $job->employment_type,
                        'description' => $job->description,
                        'is_rejected' => (bool) $job->is_rejected,
                        'rejection_reason' => $job->rejection_reason,
                        'created_at' => $job->created_at->format('Y-m-d H:i:s'),
                        'employer' => [
                            'name' => $job->employer->name,
                            'email' => $job->employer->email,
                        ],
                    ];
                });

            return response()->json([
                'success' => true,
                'jobs' => $jobs,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting pending jobs:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading pending jobs.',
            ], 500);
        }
    }

    public function getAllJobs(Request $request)
    {
        try {
            $query = Job::with('employer');

            if ($request->has('search') && $request->search) {
                $query->where(function ($q) use ($request) {
                    $q->where('title', 'LIKE', "%{$request->search}%")
                        ->orWhere('company_name', 'LIKE', "%{$request->search}%")
                        ->orWhere('location', 'LIKE', "%{$request->search}%");
                });
            }

            if ($request->has('status') && $request->status) {
                if ($request->status === 'pending') {
                    $query->where('is_approved', false)->where('is_rejected', false);
                } elseif ($request->status === 'approved') {
                    $query->where('is_approved', true);
                } elseif ($request->status === 'rejected') {
                    $query->where('is_rejected', true);
                }
            }

            $jobs = $query->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($job) {
                    return [
                        'id' => $job->id,
                        'title' => $job->title,
                        'company_name' => $job->company_name,
                        'location' => $job->location,
                        'employment_type' => $job->employment_type,

                        'description' => $job->description,
                        'is_active' => $job->is_active,
                        'is_approved' => $job->is_approved,
                        'is_rejected' => (bool) $job->is_rejected,
                        'rejection_reason' => $job->rejection_reason,
                        'created_at' => $job->created_at->format('Y-m-d H:i:s'),
                        'employer' => [
                            'name' => $job->employer->name,
                            'email' => $job->employer->email,
                        ],
                    ];
                });

            return response()->json([
                'success' => true,
                'jobs' => $jobs,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting all jobs:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading jobs.',
            ], 500);
        }
    }

    public function approveJob($id)
    {
        try {
            $job = Job::findOrFail($id);
            $job->is_approved = true;
            $job->is_rejected = false;
            $job->rejection_reason = null;
            $job->save();

            return response()->json([
                'success' => true,
                'message' => 'Job approved successfully!',
            ]);
        } catch (\Exception $e) {
            Log::error('Error approving job:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error approving job.',
            ], 500);
        }
    }

    public function rejectJob(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'rejection_reason' => 'required|string|min:10|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $job = Job::findOrFail($id);
            $job->is_approved = false;
            $job->is_rejected = true;
            $job->is_active = false;
            $job->rejection_reason = $request->rejection_reason;
            $job->save();

            return response()->json([
                'success' => true,
                'message' => 'Job rejected. Reason sent to employer.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error rejecting job:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error rejecting job.',
            ], 500);
        }
    }

    public function getJobDetails($id)
    {
        try {
            $job = Job::with(['employer', 'applications.jobseeker'])
                ->findOrFail($id);

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
                    'is_active' => $job->is_active,
                    'is_approved' => $job->is_approved,
                    'is_rejected' => (bool) $job->is_rejected,
                    'rejection_reason' => $job->rejection_reason,
                    'created_at' => $job->created_at->format('Y-m-d H:i:s'),
                    'application_deadline' => $job->application_deadline ? $job->application_deadline->format('Y-m-d') : null,
                    'employer' => [
                        'name' => $job->employer->name,
                        'email' => $job->employer->email,
                    ],
                    'applications' => $job->applications->map(function ($app) {
                        return [
                            'id' => $app->id,
                            'jobseeker_name' => $app->jobseeker->name,
                            'status' => $app->status,
                            'created_at' => $app->created_at->format('Y-m-d H:i:s'),
                        ];
                    }),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting job details:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading job details.',
            ], 500);
        }
    }

    public function deleteJob($id)
    {
        try {
            $job = Job::findOrFail($id);
            $job->delete();

            return response()->json([
                'success' => true,
                'message' => 'Job deleted successfully!',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting job:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting job.',
            ], 500);
        }
    }

    public function getAllEmployers()
    {
        try {
            $employers = User::where('role', 'employer')
                ->withCount('jobs')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'company_name' => $user->company_name,
                        'phone' => $user->phone,
                        'is_active' => $user->is_active,
                        'jobs_count' => $user->jobs_count,
                        'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                    ];
                });

            return response()->json([
                'success' => true,
                'employers' => $employers,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting employers:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading employers.',
            ], 500);
        }
    }

    public function getAllJobseekers()
    {
        try {
            $jobseekers = User::where('role', 'jobseeker')
                ->withCount('applications')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                        'is_active' => $user->is_active,
                        'applications_count' => $user->applications_count,
                        'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                    ];
                });

            return response()->json([
                'success' => true,
                'jobseekers' => $jobseekers,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting jobseekers:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading job seekers.',
            ], 500);
        }
    }

    public function getGraduates(Request $request)
    {
        try {
            $query = Graduate::query();

            if ($request->filled('search')) {
                $search = trim((string) $request->search);
                $query->where(function ($q) use ($search) {
                    $q->where('student_number', 'LIKE', "%{$search}%")
                        ->orWhere('full_name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%")
                        ->orWhere('resident_number', 'LIKE', "%{$search}%")
                        ->orWhere('phone', 'LIKE', "%{$search}%");
                });
            }

            if ($request->filled('program')) {
                $query->where('program_of_study', $request->program);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('category')) {
                $query->where('graduate_category', $request->category);
            }

            $perPage = (int) $request->input('per_page', 10);
            $perPage = $perPage > 0 ? min($perPage, 100) : 10;

            $graduates = $query
                ->orderBy('created_at', 'desc')
                ->paginate($perPage)
                ->withQueryString();

            $programs = Graduate::query()
                ->whereNotNull('program_of_study')
                ->where('program_of_study', '!=', '')
                ->distinct()
                ->orderBy('program_of_study')
                ->pluck('program_of_study')
                ->values();

            return response()->json([
                'success' => true,
                'graduates' => $graduates,
                'programs' => $programs,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting graduates:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error loading graduates.',
            ], 500);
        }
    }

    public function getGraduate($id)
    {
        try {
            $graduate = Graduate::findOrFail($id);

            return response()->json([
                'success' => true,
                'graduate' => $graduate,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Graduate not found.',
            ], 404);
        }
    }

    public function storeGraduate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_number' => 'nullable|string|max:100|unique:graduates,student_number',
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:graduates,email',
            'phone' => 'required|string|max:30',
            'gender' => 'required|in:Male,Female',
            'resident_number' => 'required|string|max:100',
            'place_of_birth' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'region_id' => 'required|exists:regions,id',
            'district_id' => 'required|exists:districts,id',
            'ward_id' => 'required|exists:wards,id',
            'shehia_id' => 'required|exists:shehias,id',
            'next_of_kin_phone' => 'required|string|max:30',
            'education_type' => 'required|in:university,vta,apprenticeship',
            'program_of_study' => 'nullable|string|max:255',
            'year_of_graduation' => 'nullable|string|max:10',
            'cgpa' => 'nullable|string|max:20',
            'skills' => 'nullable|string',
            'certifications' => 'nullable|string',
            'additional_notes' => 'nullable|string',
            'status' => 'nullable|in:active,inactive,employed,unemployed',
            'graduate_category' => 'nullable|in:college_university,vta,apprenticeship,other',
            'college_name' => 'nullable|string|max:255',
            'university_course' => 'nullable|string|max:255',
            'completed_year' => 'nullable|string|max:10',
            'gpa' => 'nullable|string|max:20',
            'nta_level' => 'nullable|string|max:50',
            'vta_course' => 'nullable|string|max:255',
            'started_year' => 'nullable|string|max:10',
            'ending_year' => 'nullable|string|max:10',
            'apprenticeship_mode' => 'nullable|string|max:20',
            'apprenticeship_specification' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $payload = $this->extractGraduatePayload($request);
            $payload['registered_from'] = 'admin';

            if (empty($payload['student_number'])) {
                $payload['student_number'] = 'GR-'.strtoupper(Str::random(8));
            }

            Graduate::create($payload);

            return response()->json([
                'success' => true,
                'message' => 'Graduate added successfully!',
            ]);
        } catch (\Exception $e) {
            Log::error('Error storing graduate:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error saving graduate.',
            ], 500);
        }
    }

    public function updateGraduate(Request $request, $id)
    {
        $graduate = Graduate::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'student_number' => 'nullable|string|max:100|unique:graduates,student_number,'.$id,
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:graduates,email,'.$id,
            'phone' => 'required|string|max:30',
            'gender' => 'required|in:Male,Female',
            'resident_number' => 'required|string|max:100',
            'place_of_birth' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'region_id' => 'required|exists:regions,id',
            'district_id' => 'required|exists:districts,id',
            'ward_id' => 'required|exists:wards,id',
            'shehia_id' => 'required|exists:shehias,id',
            'next_of_kin_phone' => 'required|string|max:30',
            'education_type' => 'required|in:university,vta,apprenticeship',
            'program_of_study' => 'nullable|string|max:255',
            'year_of_graduation' => 'nullable|string|max:10',
            'cgpa' => 'nullable|string|max:20',
            'skills' => 'nullable|string',
            'certifications' => 'nullable|string',
            'additional_notes' => 'nullable|string',
            'status' => 'nullable|in:active,inactive,employed,unemployed',
            'graduate_category' => 'nullable|in:college_university,vta,apprenticeship,other',
            'college_name' => 'nullable|string|max:255',
            'university_course' => 'nullable|string|max:255',
            'completed_year' => 'nullable|string|max:10',
            'gpa' => 'nullable|string|max:20',
            'nta_level' => 'nullable|string|max:50',
            'vta_course' => 'nullable|string|max:255',
            'started_year' => 'nullable|string|max:10',
            'ending_year' => 'nullable|string|max:10',
            'apprenticeship_mode' => 'nullable|string|max:20',
            'apprenticeship_specification' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $payload = $this->extractGraduatePayload($request, $graduate);

            if (empty($payload['student_number'])) {
                $payload['student_number'] = $graduate->student_number ?: 'GR-'.strtoupper(Str::random(8));
            }

            $graduate->update($payload);

            return response()->json([
                'success' => true,
                'message' => 'Graduate updated successfully!',
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating graduate:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating graduate.',
            ], 500);
        }
    }

    public function deleteGraduate($id)
    {
        try {
            $graduate = Graduate::findOrFail($id);
            $graduate->delete();

            return response()->json([
                'success' => true,
                'message' => 'Graduate deleted successfully!',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting graduate:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting graduate.',
            ], 500);
        }
    }

    public function importGraduates(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Please upload a valid CSV file.',
            ], 422);
        }

        $importedCount = 0;
        $errors = [];

        try {
            $handle = fopen($request->file('file')->getRealPath(), 'r');
            if ($handle === false) {
                throw new \RuntimeException('Unable to read CSV file.');
            }

            $headers = fgetcsv($handle);
            if (! $headers) {
                fclose($handle);

                return response()->json([
                    'success' => false,
                    'message' => 'CSV file is empty.',
                ], 422);
            }

            $headers = array_map(static fn ($h) => trim((string) $h), $headers);
            $line = 1;

            while (($row = fgetcsv($handle)) !== false) {
                $line++;
                $data = [];
                foreach ($headers as $index => $header) {
                    $data[$header] = isset($row[$index]) ? trim((string) $row[$index]) : null;
                }

                $rowValidator = Validator::make($data, [
                    'student_number' => 'required|string|max:100',
                    'full_name' => 'required|string|max:255',
                    'email' => 'required|email|max:255',
                    'phone' => 'nullable|string|max:30',
                    'program_of_study' => 'nullable|string|max:255',
                    'year_of_graduation' => 'nullable|string|max:10',
                    'cgpa' => 'nullable|string|max:20',
                    'skills' => 'nullable|string',
                    'certifications' => 'nullable|string',
                    'status' => 'nullable|in:active,inactive,employed,unemployed',
                ]);

                if ($rowValidator->fails()) {
                    $errors[] = 'Line '.$line.': '.implode(', ', $rowValidator->errors()->all());

                    continue;
                }

                Graduate::updateOrCreate(
                    ['student_number' => $data['student_number']],
                    [
                        'full_name' => trim((string) $data['full_name']),
                        'email' => $data['email'],
                        'phone' => $data['phone'] ?? null,
                        'program_of_study' => $data['program_of_study'] ?? null,
                        'year_of_graduation' => $data['year_of_graduation'] ?? null,
                        'cgpa' => $data['cgpa'] ?? null,
                        'skills' => $data['skills'] ?? null,
                        'certifications' => $data['certifications'] ?? null,
                        'status' => $data['status'] ?: 'active',
                        'graduate_category' => 'other',
                        'registered_from' => 'import',
                    ]
                );

                $importedCount++;
            }

            fclose($handle);

            return response()->json([
                'success' => true,
                'message' => 'Import completed.',
                'imported_count' => $importedCount,
                'errors' => $errors,
            ]);
        } catch (\Exception $e) {
            Log::error('Error importing graduates:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error importing graduates.',
            ], 500);
        }
    }

    public function exportGraduateTemplate()
    {
        return response()->json([
            'success' => true,
            'headers' => [
                'student_number',
                'full_name',
                'email',
                'phone',
                'program_of_study',
                'year_of_graduation',
                'cgpa',
                'skills',
                'certifications',
                'status',
            ],
        ]);
    }

    public function registerGraduate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female',
            'resident_number' => 'required|string|max:100',
            'place_of_birth' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'region_id' => 'required|exists:regions,id',
            'district_id' => 'required|exists:districts,id',
            'ward_id' => 'required|exists:wards,id',
            'shehia_id' => 'required|exists:shehias,id',
            'phone' => 'required|string|max:30',
            'email' => 'required|email|max:255|unique:graduates,email',
            'next_of_kin_phone' => 'required|string|max:30',
            'education_type' => 'required|in:university,vta,apprenticeship',
            'college_name' => 'nullable|string|max:255',
            'university_course' => 'nullable|string|max:255',
            'completed_year' => 'nullable|string|max:10',
            'gpa' => 'nullable|string|max:20',
            'nta_level' => 'nullable|string|max:50',
            'vta_course' => 'nullable|string|max:255',
            'started_year' => 'nullable|string|max:10',
            'ending_year' => 'nullable|string|max:10',
            'apprenticeship_mode' => 'nullable|string|max:20',
            'apprenticeship_specification' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Please fix the highlighted fields and try again.',
            ], 422);
        }

        try {
            $name = trim((string) $request->full_name);

            $location = $this->resolveLocationNames(
                $request->input('region_id'),
                $request->input('district_id'),
                $request->input('ward_id'),
                $request->input('shehia_id')
            );

            $educationType = (string) $request->education_type;
            $category = $this->mapEducationTypeToCategory($educationType);
            $program = $request->university_course ?: ($request->vta_course ?: $request->apprenticeship_specification);
            $yearOfGraduation = $request->completed_year ?: $request->ending_year;

            Graduate::create([
                'student_number' => 'GR-'.strtoupper(Str::random(8)),
                'full_name' => $name,
                'email' => $request->email,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'resident_number' => $request->resident_number,
                'place_of_birth' => $request->place_of_birth,
                'date_of_birth' => $request->date_of_birth,
                'region' => $location['region'],
                'district' => $location['district'],
                'ward' => $location['ward'],
                'shehia' => $location['shehia'],
                'next_of_kin_phone' => $request->next_of_kin_phone,
                'graduate_category' => $category,
                'education_type' => $educationType,
                'program_of_study' => $program,
                'college_name' => $request->college_name,
                'university_course' => $request->university_course,
                'completed_year' => $request->completed_year,
                'gpa' => $request->gpa,
                'nta_level' => $request->nta_level,
                'vta_course' => $request->vta_course,
                'started_year' => $request->started_year,
                'ending_year' => $request->ending_year,
                'apprenticeship_mode' => $request->apprenticeship_mode,
                'apprenticeship_specification' => $request->apprenticeship_specification,
                'year_of_graduation' => $yearOfGraduation,
                'status' => 'active',
                'registered_from' => 'welcome',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Graduate registration submitted successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error registering graduate from welcome page:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Error submitting graduate registration.',
            ], 500);
        }
    }

    private function extractGraduatePayload(Request $request, ?Graduate $graduate = null): array
    {
        $fullName = trim((string) $request->input('full_name', ''));
        if ($fullName === '' && $graduate) {
            $fullName = (string) ($graduate->full_name ?? '');
        }

        $educationType = (string) ($request->input('education_type') ?: ($graduate->education_type ?? ''));
        $program = (string) ($request->input('program_of_study') ?: ($request->input('university_course') ?: ($request->input('vta_course') ?: $request->input('apprenticeship_specification'))));
        $location = $this->resolveLocationNames(
            $request->input('region_id'),
            $request->input('district_id'),
            $request->input('ward_id'),
            $request->input('shehia_id')
        );

        if (empty($location['region']) && $graduate) {
            $location['region'] = $graduate->region;
        }
        if (empty($location['district']) && $graduate) {
            $location['district'] = $graduate->district;
        }
        if (empty($location['ward']) && $graduate) {
            $location['ward'] = $graduate->ward ?? null;
        }
        if (empty($location['shehia']) && $graduate) {
            $location['shehia'] = $graduate->shehia;
        }

        return [
            'student_number' => $request->input('student_number'),
            'full_name' => $fullName,
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'gender' => $request->input('gender'),
            'resident_number' => $request->input('resident_number'),
            'place_of_birth' => $request->input('place_of_birth'),
            'date_of_birth' => $request->input('date_of_birth'),
            'region' => $location['region'],
            'district' => $location['district'],
            'ward' => $location['ward'],
            'shehia' => $location['shehia'],
            'next_of_kin_phone' => $request->input('next_of_kin_phone'),
            'education_type' => $educationType ?: null,
            'graduate_category' => $request->input('graduate_category') ?: $this->mapEducationTypeToCategory($educationType),
            'program_of_study' => $program ?: null,
            'college_name' => $request->input('college_name'),
            'university_course' => $request->input('university_course'),
            'completed_year' => $request->input('completed_year'),
            'gpa' => $request->input('gpa'),
            'nta_level' => $request->input('nta_level'),
            'vta_course' => $request->input('vta_course'),
            'started_year' => $request->input('started_year'),
            'ending_year' => $request->input('ending_year'),
            'apprenticeship_mode' => $request->input('apprenticeship_mode'),
            'apprenticeship_specification' => $request->input('apprenticeship_specification'),
            'year_of_graduation' => $request->input('year_of_graduation') ?: $request->input('completed_year') ?: $request->input('ending_year'),
            'cgpa' => $request->input('cgpa') ?: $request->input('gpa'),
            'skills' => $request->input('skills'),
            'certifications' => $request->input('certifications'),
            'additional_notes' => $request->input('additional_notes'),
            'status' => $request->input('status', 'active'),
        ];
    }

    private function resolveLocationNames($regionId, $districtId, $wardId, $shehiaId): array
    {
        $region = $regionId ? Region::find($regionId) : null;
        $district = $districtId ? District::find($districtId) : null;
        $ward = $wardId ? Ward::find($wardId) : null;
        $shehia = $shehiaId ? Shehia::find($shehiaId) : null;

        return [
            'region' => $region?->name,
            'district' => $district?->name,
            'ward' => $ward?->name,
            'shehia' => $shehia?->name,
        ];
    }

    private function saveGraduateFile($file, string $directory): string
    {
        $filename = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();

        if (config('filesystems.default') === 's3') {
            Storage::disk('s3_graduates')->putFileAs($directory, $file, $filename);
        } else {
            $targetPath = public_path('uploads/graduates/'.$directory);
            if (! is_dir($targetPath)) {
                mkdir($targetPath, 0755, true);
            }
            $file->move($targetPath, $filename);
        }

        return $filename;
    }

    private function mapEducationTypeToCategory(?string $educationType): string
    {
        return match ($educationType) {
            'university' => 'college_university',
            'vta' => 'vta',
            'apprenticeship' => 'apprenticeship',
            default => 'college_university',
        };
    }

    // User Management Methods
    public function getUsers(Request $request)
    {
        try {
            $query = User::query();

            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                });
            }

            if ($request->has('role') && $request->role) {
                $query->where('role', $request->role);
            }

            if ($request->has('status') && $request->status) {
                $isActive = $request->status === 'active';
                $query->where('is_active', $isActive);
            }

            $users = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'users' => $users->map(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'role_label' => ucfirst($user->role),
                        'company_name' => $user->company_name,
                        'phone' => $user->phone,
                        'is_active' => $user->is_active,
                        'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                    ];
                }),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading users: '.$e->getMessage(),
            ], 500);
        }
    }

    public function getUser($id)
    {
        try {
            $user = User::findOrFail($id);

            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'company_name' => $user->company_name,
                    'phone' => $user->phone,
                    'is_active' => $user->is_active,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }
    }

    public function storeUser(Request $request)
    {
        try {
            $request->merge([
                'is_active' => $request->has('is_active'),
            ]);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'role' => 'required|in:admin,employer,jobseeker',
                'company_name' => 'nullable|required_if:role,employer|string|max:255',
                'phone' => 'nullable|required_if:role,employer|string|max:20',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $companyName = $request->role === 'employer' ? $request->company_name : null;
            $phone = $request->role === 'employer' ? $request->phone : null;

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'company_name' => $companyName,
                'phone' => $phone,
                'is_active' => $request->boolean('is_active'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating user: '.$e->getMessage(),
            ], 500);
        }
    }

    public function updateUser(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $request->merge([
                'is_active' => $request->has('is_active'),
            ]);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,'.$id,
                'password' => 'nullable|string|min:8',
                'role' => 'required|in:admin,employer,jobseeker',
                'company_name' => 'nullable|required_if:role,employer|string|max:255',
                'phone' => 'nullable|required_if:role,employer|string|max:20',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $companyName = $request->role === 'employer' ? $request->company_name : null;
            $phone = $request->role === 'employer' ? $request->phone : null;

            $user->name = $request->name;
            $user->email = $request->email;
            if ($request->password) {
                $user->password = Hash::make($request->password);
            }
            $user->role = $request->role;
            $user->company_name = $companyName;
            $user->phone = $phone;
            $user->is_active = $request->boolean('is_active');
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating user: '.$e->getMessage(),
            ], 500);
        }
    }

    public function deleteUser($id)
    {
        try {
            $user = User::findOrFail($id);

            // Prevent deleting admin users
            if ($user->role === 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete admin users.',
                ], 403);
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting user: '.$e->getMessage(),
            ], 500);
        }
    }

    public function toggleUserStatus($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->is_active = ! $user->is_active;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'User status updated successfully!',
                'is_active' => $user->is_active,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error toggling user status.',
            ], 500);
        }
    }

    // Report Methods
    public function generateReport(Request $request)
    {
        try {
            $reportType = $request->report_type;
            $year = $request->year;
            $quarter = $request->quarter;

            $title = '';
            $startDate = null;
            $endDate = null;

            switch ($reportType) {
                case 'quarterly':
                    $title = "Quarterly Report - Q{$quarter} {$year}";
                    $startDate = date("{$year}-".(($quarter - 1) * 3 + 1).'-01');
                    $endDate = date("{$year}-".($quarter * 3).'-'.cal_days_in_month(CAL_GREGORIAN, $quarter * 3, $year));
                    break;
                case 'semiannual':
                    $title = "Semiannual Report - {$year}";
                    $startDate = date("{$year}-01-01");
                    $endDate = date("{$year}-06-30");
                    break;
                case 'yearly':
                    $title = "Yearly Report - {$year}";
                    $startDate = date("{$year}-01-01");
                    $endDate = date("{$year}-12-31");
                    break;
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid report type.',
                    ], 400);
            }

            // Get data for the report period
            $totalJobs = Job::whereBetween('created_at', [$startDate, $endDate])->count();
            $activeJobs = Job::whereBetween('created_at', [$startDate, $endDate])
                ->where('is_active', true)
                ->where('is_approved', true)
                ->count();
            $pendingJobs = Job::whereBetween('created_at', [$startDate, $endDate])
                ->where('is_approved', false)
                ->where('is_rejected', false)
                ->count();
            $totalEmployers = User::where('role', 'employer')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
            $totalJobseekers = User::where('role', 'jobseeker')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
            $totalApplications = JobApplication::whereBetween('created_at', [$startDate, $endDate])->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'title' => $title,
                    'period' => [
                        'start' => $startDate,
                        'end' => $endDate,
                    ],
                    'total_jobs' => $totalJobs,
                    'active_jobs' => $activeJobs,
                    'pending_jobs' => $pendingJobs,
                    'total_employers' => $totalEmployers,
                    'total_jobseekers' => $totalJobseekers,
                    'total_applications' => $totalApplications,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating report: '.$e->getMessage(),
            ], 500);
        }
    }
}
