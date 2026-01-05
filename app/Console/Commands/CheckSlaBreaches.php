<?php

namespace App\Console\Commands;

use App\Models\Incident;
use App\Services\IncidentNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckSlaBreaches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'incidents:check-sla-breaches';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check open incidents for SLA breaches and send notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for SLA breaches on open incidents...');

        // Get all open incidents that haven't been notified of SLA breach yet
        $incidents = Incident::where('status', '!=', 'Closed')
            ->whereNull('sla_breach_notified_at')
            ->get();

        $breachedCount = 0;
        $notifiedCount = 0;

        foreach ($incidents as $incident) {
            // Calculate current elapsed time
            if (!$incident->started_at) {
                continue;
            }

            $elapsedMinutes = $incident->started_at->diffInMinutes(now());
            $slaMinutes = $incident->sla_minutes ?? 720; // Default to 12 hours if not set

            // Check if SLA is breached
            if ($elapsedMinutes > $slaMinutes) {
                $breachedCount++;

                $this->warn("SLA BREACH: {$incident->incident_code} - Elapsed: {$elapsedMinutes}min, SLA: {$slaMinutes}min");

                // Send notification
                try {
                    $notificationService = new IncidentNotificationService();
                    $notificationService->sendSlaBreachedNotification($incident);

                    // Mark as notified
                    $incident->sla_breach_notified_at = now();
                    $incident->save();

                    $notifiedCount++;
                    $this->info("  → Notification sent for {$incident->incident_code}");
                } catch (\Exception $e) {
                    $this->error("  → Failed to send notification for {$incident->incident_code}: {$e->getMessage()}");
                    Log::error('Failed to send SLA breach notification in scheduled command', [
                        'incident_id' => $incident->id,
                        'incident_code' => $incident->incident_code,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        // Summary
        $this->newLine();
        $this->info("Summary:");
        $this->info("  Total open incidents checked: {$incidents->count()}");
        $this->info("  SLA breaches found: {$breachedCount}");
        $this->info("  Notifications sent: {$notifiedCount}");

        Log::info('SLA breach check completed', [
            'total_checked' => $incidents->count(),
            'breaches_found' => $breachedCount,
            'notifications_sent' => $notifiedCount,
        ]);

        return Command::SUCCESS;
    }
}
