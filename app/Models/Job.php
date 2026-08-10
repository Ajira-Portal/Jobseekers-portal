<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Job extends Model
{
    use HasFactory;
    protected $table = 'jobs';

    protected $fillable = [
        'employer_id',
        'title',
        'company_name',
        'location',
        'employment_type',
        'posts',
        'description',
        'requirements',
        'attachment_path',
        'attachment_name',
        'attachment_mime',
        'attachment_size',
        'application_deadline',
        'is_active',
        'is_approved',
        'is_rejected',
        'rejection_reason',
        'views_count',
        'applications_count'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_approved' => 'boolean',
        'is_rejected' => 'boolean',
        'application_deadline' => 'date',
        'attachment_size' => 'integer',
        'views_count' => 'integer',
        'applications_count' => 'integer'
    ];

    // Relationships
    public function employer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employer_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    // Accessors
    public function getStatusLabelAttribute(): string
    {
        if (!$this->is_approved) {
            return 'Pending Approval';
        }
        return $this->is_active ? 'Active' : 'Inactive';
    }

    public function getStatusColorAttribute(): string
    {
        if (!$this->is_approved) {
            return 'warning';
        }
        return $this->is_active ? 'success' : 'danger';
    }

    public function getEmploymentTypeLabelAttribute(): string
    {
        $types = [
            'full-time' => 'Full Time',
            'part-time' => 'Part Time',
            'contract' => 'Contract',
            'internship' => 'Internship',
            'remote' => 'Remote'
        ];
        return $types[$this->employment_type] ?? $this->employment_type;
    }

    public function getShortDescriptionAttribute(): string
    {
        return strlen($this->description) > 150 
            ? substr($this->description, 0, 150) . '...' 
            : $this->description;
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublished($query)
    {
        return $query->where('is_approved', true)->where('is_active', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_approved', false)->where('is_rejected', false);
    }

    public function scopeByEmployer($query, $employerId)
    {
        return $query->where('employer_id', $employerId);
    }

    // Methods
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function incrementApplications(): void
    {
        $this->increment('applications_count');
    }

    
}