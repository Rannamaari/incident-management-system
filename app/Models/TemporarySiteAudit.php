<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemporarySiteAudit extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'temp_site_id',
        'action',
        'old_values',
        'new_values',
        'user_id',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($audit) {
            $audit->created_at = now();
        });
    }

    /**
     * Get the user who performed this action.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the temporary site this audit belongs to.
     */
    public function temporarySite()
    {
        return $this->belongsTo(TemporarySite::class, 'temp_site_id', 'temp_site_id');
    }

    /**
     * Get a formatted action name.
     */
    public function getFormattedActionAttribute()
    {
        return ucfirst($this->action);
    }
}
