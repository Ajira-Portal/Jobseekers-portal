<?php

// app/Models/JobseekerProfile.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobseekerProfile extends Model
{
    use HasFactory;

    protected $table = 'jobseeker_profiles';

    protected $fillable = [
        'user_id',
        'resident_number',
        'phone',
        'gender',
        'date_of_birth',
        'disability',
        'disability_description',
        'has_passport',
        'passport_number',
        'passport_expiry_date',
        'place_of_issue',
        'region_id',
        'district_id',
        'ward_id',
        'shehia_id',
        'resume',
        'profile_image',

        'skills',
        'is_completed',
        'profile_completed',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'passport_expiry_date' => 'date',
        'is_completed' => 'boolean',
        'profile_completed' => 'boolean',
        'experience_years' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function ward()
    {
        return $this->belongsTo(Ward::class);
    }

    public function shehia()
    {
        return $this->belongsTo(Shehia::class);
    }

    public function educations()
    {
        return $this->hasMany(Education::class);
    }

    public function workExperiences()
    {
        return $this->hasMany(WorkExperience::class);
    }

    public function getSkillsArrayAttribute()
    {
        return $this->skills ? array_map('trim', explode(',', $this->skills)) : [];
    }

    public function getGenderLabelAttribute()
    {
        if (empty($this->gender)) {
            return null;
        }

        return ucfirst($this->gender);
    }

    public function getAgeAttribute()
    {
        if (empty($this->date_of_birth)) {
            return null;
        }

        return $this->date_of_birth->age;
    }

    public function getResumeUrlAttribute()
    {
        return $this->resume ? url('/storage/resumes/'.$this->resume) : null;
    }

    public function getProfileImageUrlAttribute()
    {
        return $this->profile_image ? url('/storage/profile_images/'.$this->profile_image) : null;
    }

    public function getFullNameAttribute()
    {
        return $this->user ? $this->user->name : '';
    }

    public function getEmailAttribute()
    {
        return $this->user ? $this->user->email : '';
    }
}
