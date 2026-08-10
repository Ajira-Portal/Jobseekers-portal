<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'company_name',
        'phone',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    // ============================================
    // RELATIONSHIPS
    // ============================================
    
    // For Employers
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'employer_id');
    }

    // For Jobseekers - Applications
    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'jobseeker_id');
    }

    // Jobseeker Profile
    public function jobseekerProfile(): HasOne
    {
        return $this->hasOne(JobseekerProfile::class);
    }

    // Legacy Personal Info Profile
    public function personalInfo(): HasOne
    {
        return $this->hasOne(PersonalInfo::class);
    }

    // Education
    public function education(): HasMany
    {
        return $this->hasMany(Education::class);
    }

    // Work Experience
    public function workExperiences(): HasMany
    {
        return $this->hasMany(WorkExperience::class);
    }

    // ============================================
    // ROLE CHECK METHODS
    // ============================================

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isEmployer(): bool
    {
        return $this->role === 'employer';
    }

    public function isJobseeker(): bool
    {
        return $this->role === 'jobseeker';
    }

    // ============================================
    // ACCESSORS
    // ============================================

    public function getRoleLabelAttribute(): string
    {
        return ucfirst($this->role);
    }

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        $initials = '';
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        return $initials;
    }

    // ============================================
    // PROFILE HELPERS
    // ============================================

    /**
     * Get or create jobseeker profile
     */
    public function getOrCreateProfile(): JobseekerProfile
    {
        $profile = $this->jobseekerProfile;
        if (!$profile) {
            $profile = new JobseekerProfile();
            $profile->user_id = $this->id;
            $profile->save();
            $this->refresh();
        }
        return $profile;
    }

    /**
     * Check if jobseeker has completed profile
     */
    public function hasCompletedProfile(): bool
    {
        if (!$this->isJobseeker()) {
            return true;
        }

        $profile = $this->jobseekerProfile;
        if (!$profile) {
            return false;
        }

        return $profile->profile_completed ?? false;
    }

    /**
     * Get profile completion percentage
     */
    public function getProfileCompletionPercentage(): int
    {
        if (!$this->isJobseeker()) {
            return 100;
        }

        $profile = $this->jobseekerProfile;
        if (!$profile) {
            return 0;
        }

        $fields = [
            'phone' => 15,
            'gender' => 5,
            'date_of_birth' => 5,
            'address' => 5,
            'bio' => 10,
            'skills' => 15,
            'experience_years' => 10,
            'resume' => 15,
            'profile_image' => 10,
            'education' => 5, // Has at least one education
            'work_experiences' => 5, // Has at least one work experience
        ];

        $completed = 0;

        // Check basic fields
        if (!empty($profile->phone)) $completed += $fields['phone'];
        if (!empty($profile->gender)) $completed += $fields['gender'];
        if (!empty($profile->date_of_birth)) $completed += $fields['date_of_birth'];
        if (!empty($profile->address)) $completed += $fields['address'];
        if (!empty($profile->bio)) $completed += $fields['bio'];
        if (!empty($profile->skills)) $completed += $fields['skills'];
        if ($profile->experience_years > 0) $completed += $fields['experience_years'];
        if (!empty($profile->resume)) $completed += $fields['resume'];
        if (!empty($profile->profile_image)) $completed += $fields['profile_image'];
        
        // Check education
        if ($this->education()->count() > 0) $completed += $fields['education'];
        
        // Check work experience
        if ($this->workExperiences()->count() > 0) $completed += $fields['work_experiences'];

        return min($completed, 100);
    }

    // Add to the $appends array if you want it available in Blade
protected $appends = ['profile_completed'];

public function getProfileCompletedAttribute()
{
    return $this->hasCompletedProfile();
}
}