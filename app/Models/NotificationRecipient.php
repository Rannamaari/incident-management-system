<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationRecipient extends Model
{
    protected $fillable = [
        'notification_level_id',
        'email',
        'name',
        'department',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the notification level this recipient belongs to
     */
    public function notificationLevel()
    {
        return $this->belongsTo(NotificationLevel::class);
    }

    /**
     * Scope to get only active recipients
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
