<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class IncidentSite extends Pivot
{
    /**
     * The table associated with the pivot model.
     */
    protected $table = 'incident_sites';

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = true;

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'affected_technologies' => 'array',
    ];
}
