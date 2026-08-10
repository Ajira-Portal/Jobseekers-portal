<?php
// app/Models/Region.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code'];

    public function districts()
    {
        return $this->hasMany(District::class);
    }

    public function jobseekerProfiles()
    {
        return $this->hasMany(JobseekerProfile::class);
    }
}