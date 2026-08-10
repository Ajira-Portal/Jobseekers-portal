<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkExperience extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'company_name',
        'position',
        'location',
        'employment_type',
        'description',
        'start_date',
        'end_date',
        
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
     * Get the user that owns the work experience.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the employment type label.
     */
    public function getEmploymentTypeLabelAttribute(): string
    {
        $labels = [
            'full-time' => 'Full Time',
            'part-time' => 'Part Time',
            'contract' => 'Contract',
            'internship' => 'Internship',
            'remote' => 'Remote',
        ];

        return $labels[$this->employment_type] ?? ucfirst($this->employment_type);
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
     * Get the total months worked.
     */
    public function getTotalMonthsAttribute(): int
    {
        $end = $this->is_current ? now() : $this->end_date;
        
        if ($this->start_date && $end) {
            return $this->start_date->diffInMonths($end);
        }

        return 0;
    }

    /**
     * Get the duration in years and months.
     */
    public function getDurationHumanAttribute(): string
    {
        $months = $this->total_months;
        
        $years = floor($months / 12);
        $remainingMonths = $months % 12;
        
        $parts = [];
        if ($years > 0) {
            $parts[] = $years . ' ' . ($years > 1 ? 'years' : 'year');
        }
        if ($remainingMonths > 0) {
            $parts[] = $remainingMonths . ' ' . ($remainingMonths > 1 ? 'months' : 'month');
        }
        
        return empty($parts) ? 'Less than a month' : implode(' ', $parts);
    }

    /**
     * Check if work experience is still ongoing.
     */
    public function isOngoing(): bool
    {
        return $this->is_current;
    }

    /**
     * Check if work experience is completed.
     */
    public function isCompleted(): bool
    {
        return !$this->is_current && $this->end_date && $this->end_date->isPast();
    }

    /**
     * Scope a query to only include current work experience.
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    /**
     * Scope a query to only include past work experience.
     */
    public function scopePast($query)
    {
        return $query->where('is_current', false);
    }

    /**
     * Scope a query to only include a specific employment type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('employment_type', $type);
    }

    /**
     * Scope a query to only include work experience from a specific company.
     */
    public function scopeAtCompany($query, string $companyName)
    {
        return $query->where('company_name', 'LIKE', "%{$companyName}%");
    }

    /**
     * Scope a query to only include work experience with a specific position.
     */
    public function scopeWithPosition($query, string $position)
    {
        return $query->where('position', 'LIKE', "%{$position}%");
    }

    /**
     * Scope a query to order by start date descending.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('start_date', 'desc');
    }
}