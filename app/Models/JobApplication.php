<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'jobseeker_id',
        'cover_letter',        
        'status',
        'employer_notes',
        'reviewed_at'
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'additional_documents' => 'array'
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function jobseeker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'jobseeker_id');
    }

    // Get jobseeker profile
    public function getJobseekerProfileAttribute()
    {
        return $this->jobseeker->jobseekerProfile;
    }

    // Get full jobseeker details including profile
    public function getJobseekerDetailsAttribute()
    {
        $user = $this->jobseeker;
        $profile = $user->jobseekerProfile;
        
        return [
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
                'education' => $user->education,
                'work_experiences' => $user->workExperiences,
            ] : null
        ];
    }
}