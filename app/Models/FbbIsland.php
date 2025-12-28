<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FbbIsland extends Model
{
    use HasFactory;

    protected $fillable = [
        'region_id',
        'island_name',
        'technology',
        'is_active',
        'remarks',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Append accessors to array/JSON serialization.
     */
    protected $appends = [
        'full_name',
    ];

    /**
     * Get the region that owns the FBB island.
     */
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Get the incidents associated with this FBB island.
     */
    public function incidents()
    {
        return $this->belongsToMany(Incident::class, 'incident_fbb_island')
            ->withTimestamps();
    }

    /**
     * Scope to get only active FBB islands.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to search FBB islands.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('island_name', 'LIKE', "%{$search}%")
              ->orWhere('technology', 'LIKE', "%{$search}%")
              ->orWhereHas('region', function ($rq) use ($search) {
                  $rq->where('name', 'LIKE', "%{$search}%")
                     ->orWhere('code', 'LIKE', "%{$search}%");
              });
        });
    }

    /**
     * Get full display name (Region - Island Name).
     */
    public function getFullNameAttribute()
    {
        return $this->region->code . ' - ' . $this->island_name;
    }
}
