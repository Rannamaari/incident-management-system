<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'atoll_code',
        'site_name',
        'coverage',
        'operational_date',
        'transmission_or_backhaul',
        'remarks',
        'status',
        'review_date',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'operational_date' => 'date',
        'review_date' => 'date',
    ];

    /**
     * Get the user who created this site.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this site.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope for search functionality.
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            $searchLower = strtolower($search);
            return $query->where(function ($q) use ($searchLower) {
                $q->whereRaw('LOWER(site_id) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('LOWER(site_name) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('LOWER(atoll_code) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('LOWER(transmission_or_backhaul) LIKE ?', ["%{$searchLower}%"]);
            });
        }
        return $query;
    }

    /**
     * Scope for filtering by atoll.
     */
    public function scopeFilterAtoll($query, $atoll)
    {
        if ($atoll) {
            return $query->where('atoll_code', $atoll);
        }
        return $query;
    }

    /**
     * Scope for filtering by coverage.
     */
    public function scopeFilterCoverage($query, $coverage)
    {
        if ($coverage) {
            return $query->where('coverage', $coverage);
        }
        return $query;
    }

    /**
     * Scope for filtering by status.
     */
    public function scopeFilterStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Scope for filtering by date range.
     */
    public function scopeFilterDateRange($query, $startDate, $endDate)
    {
        if ($startDate) {
            $query->where('operational_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('operational_date', '<=', $endDate);
        }
        return $query;
    }
}
