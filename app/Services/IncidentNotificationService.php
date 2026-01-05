<?php

namespace App\Services;

use App\Models\Incident;
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

        try {
            Mail::to($recipients)->send(new IncidentCreatedNotification($incident));

            Log::info('Incident created notification sent', [
                'incident_id' => $incident->id,
                'incident_code' => $incident->incident_code,
                'recipients' => $recipients,
                'severity' => $incident->severity,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send incident created notification', [
                'incident_id' => $incident->id,
                'error' => $e->getMessage(),
            ]);
        }
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

        try {
            Mail::to($recipients)->send(new IncidentUpdatedNotification($incident, $updateMessage, $userName));

            Log::info('Incident updated notification sent', [
                'incident_id' => $incident->id,
                'incident_code' => $incident->incident_code,
                'recipients' => $recipients,
                'update' => $updateMessage,
                'updated_by' => $userName,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send incident updated notification', [
                'incident_id' => $incident->id,
                'error' => $e->getMessage(),
            ]);
        }
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

        try {
            Mail::to($recipients)->send(new IncidentClosedNotification($incident));

            Log::info('Incident closed notification sent', [
                'incident_id' => $incident->id,
                'incident_code' => $incident->incident_code,
                'recipients' => $recipients,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send incident closed notification', [
                'incident_id' => $incident->id,
                'error' => $e->getMessage(),
            ]);
        }
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

        try {
            Mail::to($recipients)->send(new IncidentSlaBreachedNotification($incident));

            Log::info('Incident SLA breach notification sent', [
                'incident_id' => $incident->id,
                'incident_code' => $incident->incident_code,
                'recipients' => $recipients,
                'severity' => $incident->severity,
                'sla_minutes' => $incident->sla_minutes,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send incident SLA breach notification', [
                'incident_id' => $incident->id,
                'error' => $e->getMessage(),
            ]);
        }
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
