<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PendingNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'incident_id',
        'job_id',
        'notification_type',
        'scheduled_for',
        'status',
        'cancelled_at',
        'cancelled_by'
    ];

    protected $casts = [
        'scheduled_for' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Get the incident this notification belongs to
     */
    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }

    /**
     * Get the user who cancelled this notification
     */
    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    /**
     * Cancel this pending notification
     */
    public function cancel(): void
    {
        // Try to delete job from queue if possible
        if ($this->job_id) {
            try {
                DB::table('jobs')->where('id', $this->job_id)->delete();
                Log::info('Deleted job from queue', ['job_id' => $this->job_id]);
            } catch (\Exception $e) {
                Log::warning('Could not delete job from queue', [
                    'job_id' => $this->job_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Mark as cancelled
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancelled_by' => auth()->id(),
        ]);

        Log::info('Notification cancelled', [
            'pending_id' => $this->id,
            'incident_id' => $this->incident_id,
            'cancelled_by' => auth()->id()
        ]);
    }

    /**
     * Mark this notification as sent
     */
    public function markAsSent(): void
    {
        $this->update(['status' => 'sent']);

        Log::info('Notification marked as sent', [
            'pending_id' => $this->id,
            'incident_id' => $this->incident_id
        ]);
    }

    /**
     * Scope to get only pending notifications
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get notifications for a specific incident
     */
    public function scopeForIncident($query, $incidentId)
    {
        return $query->where('incident_id', $incidentId);
    }
}
