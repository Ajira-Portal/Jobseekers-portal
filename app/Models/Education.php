<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Education extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'institution_name',
        'school_name',
        'course',
        'level',
        'description',
        'start_date',
        'end_date',
        'is_current',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the education.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the education type label.
     */
    public function getTypeLabelAttribute(): string
    {
        $labels = [
            'informal' => 'Informal Education',
            'primary' => 'Primary Education',
            'secondary' => 'Secondary Education',
            'university' => 'University Education',
        ];

        return $labels[$this->type] ?? ucfirst($this->type);
    }

    /**
     * Get the education level label.
     */
    public function getLevelLabelAttribute(): string
    {
        $labels = [
            'diploma' => 'Diploma',
            'bachelor' => 'Bachelor\'s Degree',
            'master' => 'Master\'s Degree',
            'phd' => 'PhD',
        ];

        return $labels[$this->level] ?? ucfirst($this->level);
    }

    /**
     * Get the formatted duration.
     */
    public function getDurationAttribute(): string
    {
        if ($this->is_current) {
            return $this->start_date?->format('M Y') . ' - Present';
        }

        if ($this->start_date && $this->end_date) {
            return $this->start_date->format('M Y') . ' - ' . $this->end_date->format('M Y');
        }

        return 'N/A';
    }

    /**
     * Get the institution display name.
     */
    public function getInstitutionDisplayAttribute(): string
    {
        if ($this->type === 'university') {
            return $this->institution_name ?? 'N/A';
        }

        if (in_array($this->type, ['primary', 'secondary'])) {
            return $this->school_name ?? 'N/A';
        }

        return 'Informal Education';
    }

    /**
     * Get the course display name.
     */
    public function getCourseDisplayAttribute(): string
    {
        if ($this->type === 'university') {
            return $this->course ?? 'N/A';
        }

        if ($this->type === 'informal') {
            return $this->description ?? 'N/A';
        }

        return $this->type ?? 'N/A';
    }

    /**
     * Check if education is from university.
     */
    public function isUniversity(): bool
    {
        return $this->type === 'university';
    }

    /**
     * Check if education is from primary or secondary.
     */
    public function isSchool(): bool
    {
        return in_array($this->type, ['primary', 'secondary']);
    }

    /**
     * Check if education is informal.
     */
    public function isInformal(): bool
    {
        return $this->type === 'informal';
    }

    /**
     * Scope a query to only include education of a given type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include current education.
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    /**
     * Scope a query to only include completed education.
     */
    public function scopeCompleted($query)
    {
        return $query->where('is_current', false);
    }

    /**
     * Scope a query to only include university education.
     */
    public function scopeUniversity($query)
    {
        return $query->where('type', 'university');
    }

    /**
     * Scope a query to only include school education.
     */
    public function scopeSchool($query)
    {
        return $query->whereIn('type', ['primary', 'secondary']);
    }
}