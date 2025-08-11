<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncidentLog extends Model
{
    protected $fillable = [
        'incident_id',
        'occurred_at',
        'note',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
    ];

    /**
     * Get the incident that owns the log.
     */
    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }
}
