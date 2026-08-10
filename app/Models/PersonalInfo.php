<?php
// app/Models/PersonalInfo.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'resident_number',
        'phone',
        'gender',
        'date_of_birth',
        'disability',
        'disability_description',
        'region_id',
        'district_id',
        'ward_id',
        'shehia_id',
        'resume',
        'profile_image',
        'is_completed',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_completed' => 'boolean',
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

    public function getFullNameAttribute()
    {
        return $this->user ? $this->user->name : '';
    }

    public function getEmailAttribute()
    {
        return $this->user ? $this->user->email : '';
    }
}