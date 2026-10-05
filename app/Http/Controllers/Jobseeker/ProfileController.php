<?php

namespace App\Http\Controllers\Jobseeker;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Education;
use App\Models\JobseekerProfile;
use App\Models\Region;
use App\Models\Shehia;
use App\Models\Skill;
use App\Models\Ward;
use App\Models\WorkExperience;
use App\Services\AjiraApiClient;
use App\Services\SigningService;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    //     $this->middleware('isJobseeker');
    // }

    /**
     * Show the profile form with all steps
     */
    public function index()
    {
        $user = Auth::user();

        // Get or create jobseeker profile
        $profile = JobseekerProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['user_id' => $user->id]
        );

        // Get all regions for dropdown
        $regions = Region::where('is_active', true)->orderBy('name')->get();

        // Get statistics
        $stats = [
            'regions' => Region::where('is_active', true)->count(),
            'districts' => District::where('is_active', true)->count(),
            'wards' => Ward::where('is_active', true)->count(),
            'shehias' => Shehia::where('is_active', true)->count(),
        ];

        // Get user's education, skills, work experience
        $educations = Education::where('user_id', $user->id)->orderBy('start_date', 'desc')->get();
        $skills = collect($this->getUserSkillsArray($user->id, $profile));
        $profile->skills = $skills->implode(', ');
        $workExperiences = WorkExperience::where('user_id', $user->id)->orderBy('start_date', 'desc')->get();

        // Get child location data if selected
        $districts = collect();
        if ($profile->region_id) {
            $districts = District::where('region_id', $profile->region_id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }

        $wards = collect();
        if ($profile->district_id) {
            $wards = Ward::where('district_id', $profile->district_id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }

        $shehias = collect();
        if ($profile->ward_id) {
            $shehias = Shehia::where('ward_id', $profile->ward_id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }

        return view('jobseeker.profile.index', compact(
            'profile',
            'regions',
            'districts',
            'wards',
            'shehias',
            'educations',
            'skills',
            'workExperiences',
            'stats'
        ));
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

    /**
     * Get Education List (AJAX)
     */
    public function getEducation()
    {
        $educations = Education::where('user_id', Auth::id())
            ->orderBy('start_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'education' => $educations,
        ]);
    }

    /**
     * Get single education item (AJAX)
     */
    public function getEducationItem($id)
    {
        $education = Education::where('user_id', Auth::id())->find($id);

        if (! $education) {
            return response()->json([
                'success' => false,
                'message' => 'Education record not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'education' => $education,
        ]);
    }

    /**
     * Update Step 1: Personal Information
     */
    public function updatePersonalInfo(Request $request)
    {
        try {
            Log::info('=== UPDATE PERSONAL INFO STARTED ===');
            Log::info('Request data:', $request->all());

            $user = Auth::user();
            if (! $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                ], 401);
            }

            // Validation
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'resident_number' => 'required|string|max:100',
                'phone' => 'required|string|max:20',
                'disability_description' => 'nullable|string|max:500',
                'gender' => 'nullable|in:male,female,other',
                'date_of_birth' => 'required|date|before:today',
                'has_passport' => 'nullable|in:yes,no',
                'passport_number' => 'nullable|string|max:50',
                'passport_expiry_date' => 'nullable|date|after:today',
                'place_of_issue' => 'nullable|in:tanzania_mainland,zanzibar',
                'region_id' => 'required|exists:regions,id',
                'district_id' => 'required|exists:districts,id',
                'ward_id' => 'required|exists:wards,id',
                'shehia_id' => 'required|exists:shehias,id',
                'disability' => 'nullable|in:yes,no',
                'resume' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
                'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed:', $validator->errors()->toArray());

                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            $passportErrors = [];
            if ($request->has_passport === 'yes') {
                if (! $request->filled('passport_number')) {
                    $passportErrors['passport_number'] = ['Passport number is required when passport is available.'];
                }

                if (! $request->filled('passport_expiry_date')) {
                    $passportErrors['passport_expiry_date'] = ['Passport expiry date is required when passport is available.'];
                }
            }

            if (! empty($passportErrors)) {
                return response()->json([
                    'success' => false,
                    'errors' => $passportErrors,
                ], 422);
            }

            // Get or create jobseeker profile
            try {
                $profile = JobseekerProfile::firstOrCreate(
                    ['user_id' => $user->id],
                    ['user_id' => $user->id]
                );
            } catch (\Exception $e) {
                Log::error('Failed to get/create JobseekerProfile: '.$e->getMessage());

                return response()->json([
                    'success' => false,
                    'message' => 'Database error: '.$e->getMessage(),
                ], 500);
            }

            Log::info('JobseekerProfile found/created:', ['id' => $profile->id]);

            // Update user name
            $user->name = $request->name;
            $user->save();

            // Update jobseeker info
            $updateData = [
                'resident_number' => $request->resident_number,
                'phone' => $request->phone,
                'disability_description' => $request->disability === 'yes' ? $request->disability_description : null,
                'gender' => $request->gender,
                'date_of_birth' => $request->date_of_birth,
                'has_passport' => $request->has_passport,
                'passport_number' => $request->has_passport === 'yes' ? $request->passport_number : null,
                'passport_expiry_date' => $request->has_passport === 'yes' ? $request->passport_expiry_date : null,
                'place_of_issue' => $request->place_of_issue,
                'region_id' => $request->region_id,
                'district_id' => $request->district_id,
                'ward_id' => $request->ward_id,
                'shehia_id' => $request->shehia_id,
                'disability' => $request->disability,
            ];

            // Handle resume upload
            if ($request->hasFile('resume')) {
                try {
                    $resumeDisk = config('filesystems.default') === 's3' ? 's3_resume' : 'public';
                    if ($profile->resume) {
                        try {
                            Storage::disk($resumeDisk)->delete($profile->resume);
                            Storage::disk('public')->delete('resumes/'.$profile->resume);
                            @unlink(storage_path('app/private/public/resumes/'.$profile->resume));
                        } catch (\Exception $e) {
                            Log::warning('Could not delete existing resume: '.$e->getMessage());
                        }
                    }
                    $resume = $request->file('resume');
                    $resumeName = time().'_'.$user->id.'_resume.'.$resume->getClientOriginalExtension();
                    if ($resumeDisk === 's3_resume') {
                        Storage::disk('s3_resume')->putFileAs('', $resume, $resumeName);
                    } else {
                        Storage::disk('public')->putFileAs('resumes', $resume, $resumeName);
                    }
                    $updateData['resume'] = $resumeName;
                } catch (\Exception $e) {
                    Log::error('Resume upload error: '.$e->getMessage());
                }
            }

            // Handle profile image upload
            if ($request->hasFile('profile_image')) {
                try {
                    $photoDisk = config('filesystems.default') === 's3' ? 's3_profilephoto' : 'public';
                    if ($profile->profile_image) {
                        try {
                            Storage::disk($photoDisk)->delete($profile->profile_image);
                            Storage::disk('public')->delete('profile_images/'.$profile->profile_image);
                            @unlink(storage_path('app/private/public/profile_images/'.$profile->profile_image));
                        } catch (\Exception $e) {
                            Log::warning('Could not delete existing profile image: '.$e->getMessage());
                        }
                    }
                    $image = $request->file('profile_image');
                    $imageName = time().'_'.$user->id.'_profile.'.$image->getClientOriginalExtension();
                    if ($photoDisk === 's3_profilephoto') {
                        Storage::disk('s3_profilephoto')->putFileAs('', $image, $imageName);
                    } else {
                        Storage::disk('public')->putFileAs('profile_images', $image, $imageName);
                    }
                    $updateData['profile_image'] = $imageName;
                } catch (\Exception $e) {
                    Log::error('Profile image upload error: '.$e->getMessage());
                }
            }

            // Persist only columns existing in current schema.
            $existingColumns = array_flip(Schema::getColumnListing($profile->getTable()));
            $safeData = array_filter($updateData, function ($key) use ($existingColumns) {
                return isset($existingColumns[$key]);
            }, ARRAY_FILTER_USE_KEY);

            $profile->fill($safeData);
            $profile->save();

            $this->checkProfileCompletion($user);

            Log::info('=== UPDATE PERSONAL INFO SUCCESS ===');

            return response()->json([
                'success' => true,
                'message' => 'Personal information saved successfully!',
                'data' => $profile,
            ]);

        } catch (QueryException $e) {
            Log::error('DATABASE ERROR: '.$e->getMessage());
            Log::error('SQL: '.$e->getSql());

            return response()->json([
                'success' => false,
                'message' => 'Database error: '.$e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            Log::error('UPDATE PERSONAL INFO ERROR: '.$e->getMessage());
            Log::error('Stack trace: '.$e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Server error: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store Education (Step 2)
     */
    public function storeEducation(Request $request)
    {
        Log::info('Store Education Request Data:', $request->all());

        $input = $request->all();

        // Normalize field aliases from legacy or varied JS frontends
        if (empty($input['type'])) {
            if (! empty($input['institution']) || ! empty($input['institution_name']) || ! empty($input['course']) || ! empty($input['level']) || ! empty($input['degree'])) {
                $input['type'] = 'university';
            } elseif (! empty($input['school_name'])) {
                $input['type'] = 'secondary';
            } elseif (! empty($input['description'])) {
                $input['type'] = 'informal';
            } else {
                $input['type'] = 'university';
            }
        }

        if (empty($input['institution']) && ! empty($input['institution_name'])) {
            $input['institution'] = $input['institution_name'];
        }
        if (empty($input['institution_name']) && ! empty($input['institution'])) {
            $input['institution_name'] = $input['institution'];
        }
        if (empty($input['level']) && ! empty($input['degree'])) {
            $input['level'] = $input['degree'];
        }
        if (empty($input['course']) && ! empty($input['field_of_study'])) {
            $input['course'] = $input['field_of_study'];
        }
        if (isset($input['start_date']) && $input['start_date'] === '') {
            $input['start_date'] = null;
        }
        if (isset($input['end_date']) && $input['end_date'] === '') {
            $input['end_date'] = null;
        }

        $validator = Validator::make($input, [
            'type' => 'required|in:informal,primary,secondary,university',
            'description' => 'nullable|string',
            'school_name' => 'nullable|string|max:255',
            'institution' => 'nullable|string|max:255',
            'institution_name' => 'nullable|string|max:255',
            'course' => 'nullable|string|max:255',
            'level' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $validator->sometimes('description', 'required|string', function ($input) {
            return $input->type === 'informal';
        });

        $validator->sometimes(['school_name'], 'required', function ($input) {
            return in_array($input->type, ['primary', 'secondary']);
        });

        $validator->sometimes(['course'], 'required', function ($input) {
            return $input->type === 'university';
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $education = Education::create([
            'user_id' => Auth::id(),
            'type' => $input['type'],
            'institution_name' => $input['institution_name'] ?? $input['institution'] ?? null,
            'school_name' => $input['school_name'] ?? null,
            'course' => $input['course'] ?? null,
            'level' => $input['level'] ?? null,
            'description' => $input['description'] ?? null,
            'start_date' => $input['start_date'] ?? null,
            'end_date' => $input['end_date'] ?? null,
            'is_current' => ! empty($input['is_current']),
        ]);

        $this->checkProfileCompletion(Auth::user());

        return response()->json([
            'success' => true,
            'message' => 'Education added successfully!',
            'education' => $education,
        ]);
    }

    /**
     * Update Education (Step 2)
     */
    public function updateEducation(Request $request, $id)
    {
        $education = Education::where('user_id', Auth::id())->find($id);
        if (! $education) {
            return response()->json([
                'success' => false,
                'message' => 'Education record not found.',
            ], 404);
        }

        $input = $request->all();

        if (empty($input['type'])) {
            $input['type'] = $education->type ?? 'university';
        }
        if (empty($input['institution']) && ! empty($input['institution_name'])) {
            $input['institution'] = $input['institution_name'];
        }
        if (empty($input['institution_name']) && ! empty($input['institution'])) {
            $input['institution_name'] = $input['institution'];
        }
        if (empty($input['level']) && ! empty($input['degree'])) {
            $input['level'] = $input['degree'];
        }
        if (empty($input['course']) && ! empty($input['field_of_study'])) {
            $input['course'] = $input['field_of_study'];
        }
        if (isset($input['start_date']) && $input['start_date'] === '') {
            $input['start_date'] = null;
        }
        if (isset($input['end_date']) && $input['end_date'] === '') {
            $input['end_date'] = null;
        }

        $validator = Validator::make($input, [
            'type' => 'required|in:informal,primary,secondary,university',
            'description' => 'nullable|string',
            'school_name' => 'nullable|string|max:255',
            'institution' => 'nullable|string|max:255',
            'institution_name' => 'nullable|string|max:255',
            'course' => 'nullable|string|max:255',
            'level' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_current' => 'boolean',
        ]);

        $validator->sometimes('description', 'required|string', function ($input) {
            return $input->type === 'informal';
        });

        $validator->sometimes(['school_name'], 'required', function ($input) {
            return in_array($input->type, ['primary', 'secondary']);
        });

        $validator->sometimes(['course'], 'required', function ($input) {
            return $input->type === 'university';
        });

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $education->update([
            'type' => $input['type'],
            'institution_name' => $input['institution_name'] ?? $input['institution'] ?? null,
            'school_name' => $input['school_name'] ?? null,
            'course' => $input['course'] ?? null,
            'level' => $input['level'] ?? null,
            'description' => $input['description'] ?? null,
            'start_date' => $input['start_date'] ?? null,
            'end_date' => $input['end_date'] ?? null,
            'is_current' => ! empty($input['is_current']),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Education updated successfully!',
            'education' => $education,
        ]);
    }

    /**
     * Delete Education (Step 2)
     */
    public function deleteEducation($id)
    {
        $education = Education::where('user_id', Auth::id())->find($id);
        if (! $education) {
            return response()->json([
                'success' => false,
                'message' => 'Education record not found.',
            ], 404);
        }

        $education->delete();

        // Check profile completion
        $this->checkProfileCompletion(Auth::user());

        return response()->json([
            'success' => true,
            'message' => 'Education deleted successfully!',
        ]);
    }

    /**
     * Store Skill (Step 3)
     */
    public function storeSkill(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $profile = JobseekerProfile::firstOrCreate(['user_id' => Auth::id()]);
        $skills = $this->getUserSkillsArray(Auth::id(), $profile);
        $name = trim($request->name);

        $exists = collect($skills)->contains(function ($skill) use ($name) {
            return strcasecmp($skill, $name) === 0;
        });

        if (! $exists) {
            $skills[] = $name;
            $this->saveUserSkills(Auth::id(), $skills, $profile);
        }

        // Check profile completion
        $this->checkProfileCompletion(Auth::user());

        return response()->json([
            'success' => true,
            'message' => 'Skill added successfully!',
            'skills' => implode(', ', $this->getUserSkillsArray(Auth::id(), $profile)),
        ]);
    }

    /**
     * Save full skills list from Step 3
     */
    public function saveSkills(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'skills' => 'required|string|max:3000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $rawSkills = explode(',', $request->skills);
        $normalized = [];

        foreach ($rawSkills as $skill) {
            $skill = trim($skill);
            if ($skill === '') {
                continue;
            }

            $exists = collect($normalized)->contains(function ($item) use ($skill) {
                return strcasecmp($item, $skill) === 0;
            });

            if (! $exists) {
                $normalized[] = $skill;
            }
        }

        if (count($normalized) === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Please add at least one skill.',
            ], 422);
        }

        $profile = JobseekerProfile::firstOrCreate(['user_id' => Auth::id()]);
        $this->saveUserSkills(Auth::id(), $normalized, $profile);

        $this->checkProfileCompletion(Auth::user());

        return response()->json([
            'success' => true,
            'message' => 'Skills saved successfully!',
            'skills' => implode(', ', $normalized),
            'skills_array' => $normalized,
        ]);
    }

    /**
     * Delete Skill (Step 3)
     */
    public function deleteSkill($id)
    {
        $profile = JobseekerProfile::firstOrCreate(['user_id' => Auth::id()]);
        $skills = $this->getUserSkillsArray(Auth::id(), $profile);

        if (! isset($skills[(int) $id])) {
            return response()->json([
                'success' => false,
                'message' => 'Skill not found.',
            ], 404);
        }

        unset($skills[(int) $id]);
        $this->saveUserSkills(Auth::id(), array_values(array_filter($skills)), $profile);

        // Check profile completion
        $this->checkProfileCompletion(Auth::user());

        return response()->json([
            'success' => true,
            'message' => 'Skill deleted successfully!',
        ]);
    }

    /**
     * Complete Profile (Step 4 - Final Step)
     */
    public function completeProfile(Request $request)
    {
        $user = Auth::user();
        $profile = $user->jobseekerProfile;

        if (! $profile) {
            return response()->json([
                'success' => false,
                'message' => 'Please complete your personal information first.',
            ], 422);
        }

        // Check if all required fields are filled
        $hasRequiredFields = $profile->phone &&
                    $profile->date_of_birth &&
                    $profile->region_id &&
                    $profile->district_id &&
                    $profile->ward_id &&
                    $profile->shehia_id;

        if (! $hasRequiredFields) {
            return response()->json([
                'success' => false,
                'message' => 'Please complete your personal information first.',
            ], 422);
        }

        $hasEducation = Education::where('user_id', $user->id)->exists();
        $hasSkills = count($this->getUserSkillsArray($user->id, $profile)) > 0;

        if (! $hasEducation) {
            return response()->json([
                'success' => false,
                'message' => 'Please add at least one education entry.',
            ], 422);
        }

        if (! $hasSkills) {
            return response()->json([
                'success' => false,
                'message' => 'Please add at least one skill.',
            ], 422);
        }

        // Mark profile as complete
        $profile->is_completed = true;
        $profile->profile_completed = true;
        $profile->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile completed successfully!',
        ]);
    }

    /**
     * Get profile status
     */
    public function status()
    {
        $user = Auth::user();
        $profile = $user->jobseekerProfile;

        $step1Completed = $profile
            && ! empty($profile->phone)
            && ! empty($profile->date_of_birth)
            && ! empty($profile->region_id)
            && ! empty($profile->district_id)
            && ! empty($profile->ward_id)
            && ! empty($profile->shehia_id);

        return response()->json([
            'success' => true,
            'profile_completed' => $profile ? (bool) $profile->profile_completed : false,
            'step1_completed' => $step1Completed,
            'has_education' => Education::where('user_id', $user->id)->exists(),
            'has_skills' => count($this->getUserSkillsArray($user->id, $profile)) > 0,
        ]);
    }

    /**
     * Serve current user's resume file
     */
    public function viewResume()
    {
        $profile = JobseekerProfile::where('user_id', Auth::id())->first();
        if (! $profile || ! $profile->resume) {
            abort(404);
        }

        try {
            if (Storage::disk('s3_resume')->exists($profile->resume)) {
                return Storage::disk('s3_resume')->response($profile->resume, basename($profile->resume), [
                    'Content-Disposition' => 'inline; filename="'.basename($profile->resume).'"',
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('S3 resume fetch failed: '.$e->getMessage());
        }

        $storagePath = 'resumes/'.$profile->resume;
        $privateStoragePath = storage_path('app/private/public/'.$storagePath);
        $legacyPath = public_path('uploads/resumes/'.$profile->resume);

        if (Storage::disk('public')->exists($storagePath)) {
            $absolutePath = storage_path('app/public/'.$storagePath);
        } elseif (file_exists($privateStoragePath)) {
            $absolutePath = $privateStoragePath;
        } elseif (file_exists($legacyPath)) {
            $absolutePath = $legacyPath;
        } else {
            abort(404);
        }

        return response()->file($absolutePath, [
            'Content-Disposition' => 'inline; filename="'.basename($absolutePath).'"',
        ]);
    }

    /**
     * Serve current user's profile image file
     */
    public function viewProfileImage()
    {
        $profile = JobseekerProfile::where('user_id', Auth::id())->first();
        if (! $profile || ! $profile->profile_image) {
            abort(404);
        }

        try {
            if (Storage::disk('s3_profilephoto')->exists($profile->profile_image)) {
                return Storage::disk('s3_profilephoto')->response($profile->profile_image, basename($profile->profile_image), [
                    'Content-Disposition' => 'inline; filename="'.basename($profile->profile_image).'"',
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('S3 profilephoto fetch failed: '.$e->getMessage());
        }

        $storagePath = 'profile_images/'.$profile->profile_image;
        $privateStoragePath = storage_path('app/private/public/'.$storagePath);
        $legacyPath = public_path('uploads/profile_images/'.$profile->profile_image);

        if (Storage::disk('public')->exists($storagePath)) {
            $absolutePath = storage_path('app/public/'.$storagePath);
        } elseif (file_exists($privateStoragePath)) {
            $absolutePath = $privateStoragePath;
        } elseif (file_exists($legacyPath)) {
            $absolutePath = $legacyPath;
        } else {
            abort(404);
        }

        return response()->file($absolutePath, [
            'Content-Disposition' => 'inline; filename="'.basename($absolutePath).'"',
        ]);

    }

    /**
     * Get active regions (AJAX)
     */
    public function getRegions()
    {
        $regions = Region::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'data' => $regions,
        ]);
    }

    /**
     * Get districts by region (AJAX)
     */
    public function getDistricts(Request $request)
    {
        $districts = District::where('region_id', $request->region_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'data' => $districts,
        ]);
    }

    /**
     * Get wards by district (AJAX)
     */
    public function getWards(Request $request)
    {
        $wards = Ward::where('district_id', $request->district_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'data' => $wards,
        ]);
    }

    /**
     * Get shehias by ward (AJAX)
     */
    public function getShehias(Request $request)
    {
        $shehias = Shehia::where('ward_id', $request->ward_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'data' => $shehias,
        ]);
    }

    /**
     * Validate location hierarchy
     */
    private function validateLocationHierarchy($regionId, $districtId, $wardId, $shehiaId): bool
    {
        $region = Region::find($regionId);
        if (! $region) {
            return false;
        }

        $district = District::find($districtId);
        if (! $district || $district->region_id != $regionId) {
            return false;
        }

        $ward = Ward::find($wardId);
        if (! $ward || $ward->district_id != $districtId) {
            return false;
        }

        $shehia = Shehia::find($shehiaId);
        if (! $shehia || $shehia->ward_id != $wardId) {
            return false;
        }

        return true;
    }

    /**
     * Check if profile is complete
     */
    private function checkProfileCompletion($user): void
    {
        $profile = $user->jobseekerProfile;
        if (! $profile) {
            return;
        }

        $hasEducation = Education::where('user_id', $user->id)->exists();
        $hasSkills = count($this->getUserSkillsArray($user->id, $profile)) > 0;
        $hasStep1 = ! empty($profile->phone)
            && ! empty($profile->region_id)
            && ! empty($profile->district_id)
            && ! empty($profile->ward_id)
            && ! empty($profile->shehia_id);

        if ($hasStep1 && $hasEducation && $hasSkills) {
            $profile->is_completed = true;
            $profile->profile_completed = true;
            $profile->save();
        }
    }

    /**
     * Returns normalized skills from either profile column or dedicated skills table.
     */
    private function getUserSkillsArray(int $userId, ?JobseekerProfile $profile = null): array
    {
        if ($profile && Schema::hasColumn($profile->getTable(), 'skills')) {
            $skills = $profile->skills ? array_map('trim', explode(',', $profile->skills)) : [];
        } else {
            $skills = Skill::where('user_id', $userId)
                ->orderBy('id')
                ->pluck('skills_name')
                ->toArray();
            $skills = array_map('trim', $skills);
        }

        $normalized = [];
        foreach ($skills as $skill) {
            if ($skill === '') {
                continue;
            }

            $exists = collect($normalized)->contains(function ($item) use ($skill) {
                return strcasecmp($item, $skill) === 0;
            });

            if (! $exists) {
                $normalized[] = $skill;
            }
        }

        return $normalized;
    }

    /**
     * Persists normalized skills in whichever schema is available.
     */
    private function saveUserSkills(int $userId, array $skills, ?JobseekerProfile $profile = null): void
    {
        $skills = array_values(array_filter(array_map('trim', $skills), function ($value) {
            return $value !== '';
        }));

        if ($profile && Schema::hasColumn($profile->getTable(), 'skills')) {
            $profile->skills = implode(', ', $skills);
            $profile->save();

            return;
        }

        DB::transaction(function () use ($userId, $skills) {
            Skill::where('user_id', $userId)->delete();
            foreach ($skills as $skill) {
                Skill::create([
                    'user_id' => $userId,
                    'skills_name' => $skill,
                    'is_custom' => true,
                ]);
            }
        });
    }
}
