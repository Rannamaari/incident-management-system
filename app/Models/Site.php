<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'region_id',
        'location_id',
        'site_number',
        'site_code',
        'display_name',
        'is_active',
        'has_fbb',
        'is_temp_site',
        'transmission_backhaul',
        'remarks',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'has_fbb' => 'boolean',
        'is_temp_site' => 'boolean',
    ];

    /**
     * Get the region this site belongs to.
     */
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Get the location this site belongs to.
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get the technologies for this site.
     */
    public function technologies()
    {
        return $this->hasMany(SiteTechnology::class);
    }

    /**
     * Get the incidents associated with this site.
     */
    public function incidents()
    {
        return $this->belongsToMany(Incident::class, 'incident_sites')
            ->withPivot('affected_technologies')
            ->withTimestamps();
    }

    /**
     * Get the maintenance logs for this site.
     */
    public function maintenanceLogs()
    {
        return $this->hasMany(SiteMaintenanceLog::class);
    }

    /**
     * Scope for active sites.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for search functionality.
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            $searchLower = strtolower($search);
            return $query->where(function ($q) use ($searchLower) {
                $q->whereRaw('LOWER(site_code) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('LOWER(display_name) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereHas('region', function($query) use ($searchLower) {
                      $query->whereRaw('LOWER(code) LIKE ?', ["%{$searchLower}%"])
                            ->orWhereRaw('LOWER(name) LIKE ?', ["%{$searchLower}%"]);
                  })
                  ->orWhereHas('location', function($query) use ($searchLower) {
                      $query->whereRaw('LOWER(location_name) LIKE ?', ["%{$searchLower}%"]);
                  });
            });
        }
        return $query;
    }
}
