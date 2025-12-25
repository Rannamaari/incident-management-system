<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'region_id',
        'site_name',
        'site_number',
        'site_code',
        'display_name',
        'is_active',
        'has_fbb',
        'is_temp_site',
        'is_link_site',
        'site_type',
        'transmission_backhaul',
        'remarks',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'has_fbb' => 'boolean',
        'is_temp_site' => 'boolean',
        'is_link_site' => 'boolean',
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
     * Get the sites connected to this hub site.
     * Only applicable when site_type = 'Hub Site'
     */
    public function connectedSites()
    {
        return $this->belongsToMany(
            Site::class,
            'hub_site_connections',
            'hub_site_id',
            'connected_site_id'
        )->withTimestamps();
    }

    /**
     * Get the hub sites this site is connected to.
     */
    public function hubSites()
    {
        return $this->belongsToMany(
            Site::class,
            'hub_site_connections',
            'connected_site_id',
            'hub_site_id'
        )->withTimestamps();
    }

    /**
     * Scope for active sites.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for hub sites only.
     */
    public function scopeHubSites($query)
    {
        return $query->where('site_type', 'Hub Site');
    }

    /**
     * Scope for end sites only.
     */
    public function scopeEndSites($query)
    {
        return $query->where('site_type', 'End Site');
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

    /**
     * Generate the next site code for a given region.
     * Pattern: {region_code}-{3-digit-number}
     * Example: AA-001, AA-002
     */
    public static function generateSiteCode($regionId)
    {
        $region = Region::findOrFail($regionId);

        // Find the highest site number for this region
        $lastSite = static::where('region_id', $regionId)
            ->orderByDesc('site_number')
            ->first();

        // Increment the number (or start at 1)
        $nextNumber = $lastSite ? (intval($lastSite->site_number) + 1) : 1;

        // Generate the site code with 3-digit number
        $siteCode = sprintf(
            '%s-%03d',
            $region->code,
            $nextNumber
        );

        return [
            'site_code' => $siteCode,
            'site_number' => $nextNumber,
        ];
    }
}
