<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Graduate extends Model
{
    use HasFactory;

    protected $table = 'graduates';

    protected $fillable = [
        'student_number',
        'full_name',
        'email',
        'phone',
        'gender',
        'resident_number',
        'place_of_birth',
        'date_of_birth',
        'region',
        'district',
        'ward',
        'shehia',
        'next_of_kin_phone',
        'graduate_category',
        'education_type',
        'program_of_study',
        'college_name',
        'university_course',
        'completed_year',
        'gpa',
        'nta_level',
        'vta_course',
        'started_year',
        'ending_year',
        'apprenticeship_mode',
        'apprenticeship_specification',
        'year_of_graduation',
        'cgpa',
        'skills',
        'certifications',
        'additional_notes',
        'status',
        'resume',
        'profile_photo',
        'registered_from',
    ];
}
