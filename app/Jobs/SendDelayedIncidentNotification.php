<?php

namespace App\Jobs;

use App\Models\Incident;
use App\Models\PendingNotification;
use App\Services\IncidentNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendDelayedIncidentNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $incident;
    public $notificationType;
    public $pendingNotificationId;

    /**
     * Create a new job instance.
     */
    public function __construct(Incident $incident, string $notificationType, int $pendingNotificationId)
    {
        $this->incident = $incident;
        $this->notificationType = $notificationType;
        $this->pendingNotificationId = $pendingNotificationId;
    }

    /**
     * Execute the job.
     */
    public function handle(IncidentNotificationService $notificationService): void
    {
        Log::info('Processing delayed notification job', [
            'pending_id' => $this->pendingNotificationId,
            'incident_id' => $this->incident->id,
            'type' => $this->notificationType
        ]);

        // Check if notification was cancelled
        $pending = PendingNotification::find($this->pendingNotificationId);

        if (!$pending) {
            Log::warning('Pending notification not found', [
                'pending_id' => $this->pendingNotificationId
            ]);
            return;
        }

        if ($pending->status !== 'pending') {
            Log::info('Skipping cancelled/sent notification', [
                'pending_id' => $this->pendingNotificationId,
                'status' => $pending->status
            ]);
            return;
        }

        // Refresh incident data to get latest edits
        $this->incident->refresh();

        // Send notification
        try {
            match($this->notificationType) {
                'created' => $notificationService->sendCreatedNotification($this->incident),
                'updated' => $notificationService->sendUpdatedNotification($this->incident, 'Timeline updated', 'System'),
                'closed' => $notificationService->sendClosedNotification($this->incident),
                default => throw new \Exception("Unsupported notification type: {$this->notificationType}")
            };

            // Mark as sent
            $pending->markAsSent();

            Log::info('Delayed notification sent successfully', [
                'pending_id' => $this->pendingNotificationId,
                'incident_id' => $this->incident->id
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send delayed notification', [
                'pending_id' => $this->pendingNotificationId,
                'incident_id' => $this->incident->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-throw to mark job as failed
            throw $e;
        }
    }
}
