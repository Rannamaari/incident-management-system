<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IspEscalationContact extends Model
{
    const ESCALATION_LEVELS = ['L1', 'L2', 'L3'];

    protected $fillable = [
        'isp_link_id',
        'escalation_level',
        'contact_name',
        'contact_phone',
        'contact_email',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    // Relationships
    public function ispLink(): BelongsTo
    {
        return $this->belongsTo(IspLink::class);
    }
}
