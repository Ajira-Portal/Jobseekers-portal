<?php
// app/Models/Shehia.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shehia extends Model
{
    use HasFactory;

    protected $fillable = ['ward_id', 'name', 'code'];

    public function ward()
    {
        return $this->belongsTo(Ward::class);
    }

    public function jobseekerProfiles()
    {
        return $this->hasMany(JobseekerProfile::class);
    }
}