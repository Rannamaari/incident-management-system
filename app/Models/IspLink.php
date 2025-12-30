<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IspLink extends Model
{
    const STATUSES = ['Up', 'Down', 'Degraded'];
    const LINK_TYPES = ['Backhaul', 'Peering'];

    protected $fillable = [
        'isp_name',
        'circuit_id',
        'link_type',
        'total_capacity_gbps',
        'current_capacity_gbps',
        'status',
        'location_a',
        'location_b',
        'prtg_sensor_id',
        'prtg_api_endpoint',
        'last_prtg_sync',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'total_capacity_gbps' => 'decimal:2',
        'current_capacity_gbps' => 'decimal:2',
        'last_prtg_sync' => 'datetime',
    ];

    // Relationships
    public function escalationContacts(): HasMany
    {
        return $this->hasMany(IspEscalationContact::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }

    public function activeIncidents(): HasMany
    {
        return $this->hasMany(Incident::class)->whereIn('status', ['Open', 'In Progress', 'Monitoring']);
    }

    // Check if link has active incidents (from both old and new systems)
    public function hasActiveIncidents(): bool
    {
        // Check old single ISP link system
        $hasOldIncidents = $this->activeIncidents()->exists();

        // Check new many-to-many system
        $hasNewIncidents = \DB::table('incident_isp_link')
            ->join('incidents', 'incident_isp_link.incident_id', '=', 'incidents.id')
            ->where('incident_isp_link.isp_link_id', $this->id)
            ->whereIn('incidents.status', ['Open', 'In Progress', 'Monitoring'])
            ->exists();

        return $hasOldIncidents || $hasNewIncidents;
    }

    // Get total capacity lost from all active incidents
    public function getActiveIncidentsCapacityLost(): float
    {
        $capacityLost = 0;

        // From old system
        $capacityLost += $this->activeIncidents()->sum('isp_capacity_lost_gbps') ?? 0;

        // From new system
        $capacityLost += \DB::table('incident_isp_link')
            ->join('incidents', 'incident_isp_link.incident_id', '=', 'incidents.id')
            ->where('incident_isp_link.isp_link_id', $this->id)
            ->whereIn('incidents.status', ['Open', 'In Progress', 'Monitoring'])
            ->sum('incident_isp_link.capacity_lost_gbps') ?? 0;

        return $capacityLost;
    }

    // Scopes
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('isp_name', 'LIKE', "%{$search}%")
                    ->orWhere('circuit_id', 'LIKE', "%{$search}%")
                    ->orWhere('location_a', 'LIKE', "%{$search}%")
                    ->orWhere('location_b', 'LIKE', "%{$search}%");
            });
        }
        return $query;
    }

    public function scopeStatus($query, $status)
    {
        return $status ? $query->where('status', $status) : $query;
    }

    public function scopeLinkType($query, $type)
    {
        return $type ? $query->where('link_type', $type) : $query;
    }

    // Accessors
    protected function lostCapacityGbps(): Attribute
    {
        return Attribute::make(
            get: fn() => max(0, $this->total_capacity_gbps - $this->current_capacity_gbps)
        );
    }

    protected function availabilityPercentage(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->total_capacity_gbps == 0) {
                    return 0;
                }
                return round(($this->current_capacity_gbps / $this->total_capacity_gbps) * 100, 2);
            }
        );
    }

    protected function statusColorClass(): Attribute
    {
        return Attribute::make(
            get: function () {
                return match ($this->status) {
                    'Up' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
                    'Down' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                    'Degraded' => 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-300',
                    default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
                };
            }
        );
    }
}
