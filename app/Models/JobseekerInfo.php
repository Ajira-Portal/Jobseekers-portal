<?php
// app/Models/JobseekerInfo.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobseekerInfo extends Model
{
    protected $table = 'jobseeker_infos';

    protected $fillable = [
        'user_id',
        'phone',
        'gender',
        'date_of_birth',
        'region_id',
        'district_id',
        'ward_id',
        'shehia_id',
        'disability',
        'resume',
        'profile_image',
        'step1_completed',
        'is_completed',
        'completed_at'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'step1_completed' => 'boolean',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}