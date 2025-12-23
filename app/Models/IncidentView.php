<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncidentView extends Model
{
    protected $fillable = [
        'incident_id',
        'user_id',
        'last_viewed_at',
    ];

    protected $casts = [
        'last_viewed_at' => 'datetime',
    ];

    /**
     * Get the incident that was viewed.
     */
    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }

    /**
     * Get the user who viewed the incident.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
