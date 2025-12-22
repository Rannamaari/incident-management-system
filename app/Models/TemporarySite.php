<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TemporarySite extends Model
{
    use HasFactory;

    protected $fillable = [
        'temp_site_id',
        'atoll_code',
        'site_name',
        'coverage',
        'is_2g_online',
        'is_3g_online',
        'is_4g_online',
        'added_date',
        'transmission_or_backhaul',
        'remarks',
        'status',
        'review_date',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'added_date' => 'date',
        'review_date' => 'date',
        'is_2g_online' => 'boolean',
        'is_3g_online' => 'boolean',
        'is_4g_online' => 'boolean',
    ];

    protected static function booted()
    {
        // Log creation
        static::created(function ($temporarySite) {
            TemporarySiteAudit::create([
                'temp_site_id' => $temporarySite->temp_site_id,
                'action' => 'created',
                'new_values' => $temporarySite->toArray(),
                'user_id' => Auth::id(),
            ]);
        });

        // Log updates
        static::updated(function ($temporarySite) {
            TemporarySiteAudit::create([
                'temp_site_id' => $temporarySite->temp_site_id,
                'action' => 'updated',
                'old_values' => $temporarySite->getOriginal(),
                'new_values' => $temporarySite->getChanges(),
                'user_id' => Auth::id(),
            ]);
        });

        // Log deletion
        static::deleted(function ($temporarySite) {
            TemporarySiteAudit::create([
                'temp_site_id' => $temporarySite->temp_site_id,
                'action' => 'deleted',
                'old_values' => $temporarySite->toArray(),
                'user_id' => Auth::id(),
            ]);
        });
    }

    /**
     * Get the user who created this temporary site.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this temporary site.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get all audit records for this temporary site.
     */
    public function audits()
    {
        return $this->hasMany(TemporarySiteAudit::class, 'temp_site_id', 'temp_site_id')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Scope for search functionality.
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            $searchLower = strtolower($search);
            return $query->where(function ($q) use ($searchLower) {
                $q->whereRaw('LOWER(temp_site_id) LIKE ?', ["%{$searchLower}%"])
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
            $query->where('added_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('added_date', '<=', $endDate);
        }
        return $query;
    }

    /**
     * Log an import action for this temporary site.
     */
    public function logImport()
    {
        TemporarySiteAudit::create([
            'temp_site_id' => $this->temp_site_id,
            'action' => 'imported',
            'new_values' => $this->toArray(),
            'user_id' => Auth::id(),
        ]);
    }
}
