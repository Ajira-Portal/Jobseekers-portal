<?php
// app/Models/Ward.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    use HasFactory;

    protected $fillable = ['district_id', 'name', 'code'];

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function shehias()
    {
        return $this->hasMany(Shehia::class);
    }

    public function jobseekerProfiles()
    {
        return $this->hasMany(JobseekerProfile::class);
    }
}