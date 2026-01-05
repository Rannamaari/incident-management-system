<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLevel extends Model
{
    protected $fillable = [
        'name',
        'description',
        'severities',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'severities' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the recipients for this notification level
     */
    public function recipients()
    {
        return $this->hasMany(NotificationRecipient::class);
    }

    /**
     * Get only active recipients for this level
     */
    public function activeRecipients()
    {
        return $this->hasMany(NotificationRecipient::class)->where('is_active', true);
    }

    /**
     * Check if this level should receive notifications for a given severity
     */
    public function shouldReceiveForSeverity(string $severity): bool
    {
        return $this->is_active && in_array($severity, $this->severities ?? []);
    }

    /**
     * Scope to get only active notification levels
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
