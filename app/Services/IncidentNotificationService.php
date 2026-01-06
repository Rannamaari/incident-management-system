<?php

namespace App\Services;

use App\Models\Incident;
use App\Models\NotificationSetting;
use App\Models\PendingNotification;
use App\Jobs\SendDelayedIncidentNotification;
use App\Mail\IncidentCreatedNotification;
use App\Mail\IncidentUpdatedNotification;
use App\Mail\IncidentClosedNotification;
use App\Mail\IncidentSlaBreachedNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class IncidentNotificationService
{
    /**
     * Send notification when an incident is created
     */
    public function sendCreatedNotification(Incident $incident): void
    {
        if (!$this->isEnabled() || !Config::get('incident-notifications.events.created')) {
            return;
        }

        $recipients = $this->getRecipientsForSeverity($incident->severity);

        if (empty($recipients)) {
            Log::info('No recipients configured for incident creation notification', [
                'incident_id' => $incident->id,
                'severity' => $incident->severity,
            ]);
            return;
        }

        $successCount = 0;
        $failedRecipients = [];

        foreach ($recipients as $recipient) {
            try {
                Mail::to($recipient)->send(new IncidentCreatedNotification($incident));
                $successCount++;
            } catch (\Exception $e) {
                $failedRecipients[] = $recipient;
                Log::error('Failed to send incident created notification to recipient', [
                    'incident_id' => $incident->id,
                    'recipient' => $recipient,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('Incident created notification sent', [
            'incident_id' => $incident->id,
            'incident_code' => $incident->incident_code,
            'total_recipients' => count($recipients),
            'successful' => $successCount,
            'failed' => count($failedRecipients),
            'severity' => $incident->severity,
        ]);
    }

    /**
     * Send notification when an incident is updated
     */
    public function sendUpdatedNotification(Incident $incident, string $updateMessage, string $userName): void
    {
        if (!$this->isEnabled() || !Config::get('incident-notifications.events.updated')) {
            return;
        }

        $recipients = $this->getRecipientsForSeverity($incident->severity);

        if (empty($recipients)) {
            return;
        }

        $successCount = 0;
        $failedRecipients = [];

        foreach ($recipients as $recipient) {
            try {
                Mail::to($recipient)->send(new IncidentUpdatedNotification($incident, $updateMessage, $userName));
                $successCount++;
            } catch (\Exception $e) {
                $failedRecipients[] = $recipient;
                Log::error('Failed to send incident updated notification to recipient', [
                    'incident_id' => $incident->id,
                    'recipient' => $recipient,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('Incident updated notification sent', [
            'incident_id' => $incident->id,
            'incident_code' => $incident->incident_code,
            'total_recipients' => count($recipients),
            'successful' => $successCount,
            'failed' => count($failedRecipients),
            'update' => $updateMessage,
            'updated_by' => $userName,
        ]);
    }

    /**
     * Send notification when an incident is closed
     */
    public function sendClosedNotification(Incident $incident): void
    {
        if (!$this->isEnabled() || !Config::get('incident-notifications.events.closed')) {
            return;
        }

        $recipients = $this->getRecipientsForSeverity($incident->severity);

        if (empty($recipients)) {
            return;
        }

        $successCount = 0;
        $failedRecipients = [];

        foreach ($recipients as $recipient) {
            try {
                Mail::to($recipient)->send(new IncidentClosedNotification($incident));
                $successCount++;
            } catch (\Exception $e) {
                $failedRecipients[] = $recipient;
                Log::error('Failed to send incident closed notification to recipient', [
                    'incident_id' => $incident->id,
                    'recipient' => $recipient,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('Incident closed notification sent', [
            'incident_id' => $incident->id,
            'incident_code' => $incident->incident_code,
            'total_recipients' => count($recipients),
            'successful' => $successCount,
            'failed' => count($failedRecipients),
        ]);
    }

    /**
     * Get recipients based on incident severity
     * Returns array of email addresses from all applicable levels
     */
    private function getRecipientsForSeverity(string $severity): array
    {
        $recipients = [];

        // Get all active notification levels that should receive notifications for this severity
        $levels = \App\Models\NotificationLevel::active()
            ->ordered()
            ->get();

        foreach ($levels as $level) {
            // Check if this level should receive notifications for this severity
            if ($level->shouldReceiveForSeverity($severity)) {
                // Get active recipients for this level
                $levelRecipients = $level->activeRecipients()->pluck('email')->toArray();
                $recipients = array_merge($recipients, $levelRecipients);
            }
        }

        // Remove duplicates
        $recipients = array_unique($recipients);

        return array_values($recipients);
    }

    /**
     * Check if notifications are enabled
     */
    private function isEnabled(): bool
    {
        return Config::get('incident-notifications.enabled', false);
    }

    /**
     * Send notification when an incident breaches SLA
     */
    public function sendSlaBreachedNotification(Incident $incident): void
    {
        if (!$this->isEnabled() || !Config::get('incident-notifications.events.sla_breached')) {
            return;
        }

        $recipients = $this->getRecipientsForSeverity($incident->severity);

        if (empty($recipients)) {
            Log::info('No recipients configured for SLA breach notification', [
                'incident_id' => $incident->id,
                'severity' => $incident->severity,
            ]);
            return;
        }

        $successCount = 0;
        $failedRecipients = [];

        foreach ($recipients as $recipient) {
            try {
                Mail::to($recipient)->send(new IncidentSlaBreachedNotification($incident));
                $successCount++;
            } catch (\Exception $e) {
                $failedRecipients[] = $recipient;
                Log::error('Failed to send SLA breach notification to recipient', [
                    'incident_id' => $incident->id,
                    'recipient' => $recipient,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('Incident SLA breach notification sent', [
            'incident_id' => $incident->id,
            'incident_code' => $incident->incident_code,
            'total_recipients' => count($recipients),
            'successful' => $successCount,
            'failed' => count($failedRecipients),
            'severity' => $incident->severity,
            'sla_minutes' => $incident->sla_minutes,
        ]);
    }

    /**
     * Queue a delayed notification (5 minutes) with ability to cancel
     */
    public function queueDelayedNotification(Incident $incident, string $type): ?PendingNotification
    {
        // Don't queue if auto-send is disabled
        if (!NotificationSetting::isAutoSendEnabled()) {
            Log::info('Auto-send disabled, skipping queue', [
                'incident_id' => $incident->id
            ]);
            return null;
        }

        // Don't queue if no recipients
        $recipients = $this->getRecipientsForSeverity($incident->severity);
        if (empty($recipients)) {
            Log::warning('No recipients configured, skipping notification', [
                'incident_id' => $incident->id,
                'severity' => $incident->severity
            ]);
            return null;
        }

        // Create pending notification record
        $pending = PendingNotification::create([
            'incident_id' => $incident->id,
            'notification_type' => $type,
            'scheduled_for' => now()->addMinutes(5),
            'status' => 'pending',
        ]);

        // Dispatch job with 5-minute delay
        $job = new SendDelayedIncidentNotification($incident, $type, $pending->id);
        $dispatchedJob = dispatch($job->delay(now()->addMinutes(5)));

        // Try to get job ID (depends on queue driver)
        try {
            $jobId = $dispatchedJob->id ?? null;
            if ($jobId) {
                $pending->update(['job_id' => $jobId]);
            }
        } catch (\Exception $e) {
            Log::warning('Could not store job ID', ['error' => $e->getMessage()]);
        }

        Log::info('Queued delayed notification', [
            'incident_id' => $incident->id,
            'pending_id' => $pending->id,
            'scheduled_for' => $pending->scheduled_for,
            'type' => $type
        ]);

        return $pending;
    }

    /**
     * Get all configured notification levels
     */
    public function getConfiguredLevels()
    {
        return \App\Models\NotificationLevel::with('recipients')
            ->active()
            ->ordered()
            ->get();
    }
}
