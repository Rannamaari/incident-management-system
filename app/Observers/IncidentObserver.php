<?php

namespace App\Observers;

use App\Models\Incident;
use App\Models\ActivityLog;

class IncidentObserver
{
    /**
     * Fields to exclude from logging (timestamps, auto-calculated fields)
     */
    protected array $excludedFields = [
        'created_at',
        'updated_at',
    ];

    /**
     * Handle the Incident "created" event.
     */
    public function created(Incident $incident): void
    {
        ActivityLog::create([
            'loggable_type' => Incident::class,
            'loggable_id' => $incident->id,
            'user_id' => auth()->id(),
            'action' => 'created',
            'description' => 'Incident created: ' . $incident->incident_code,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Handle the Incident "updated" event.
     */
    public function updated(Incident $incident): void
    {
        // Get all changed attributes
        $dirty = $incident->getDirty();

        // Get original values before update
        $original = $incident->getOriginal();

        foreach ($dirty as $field => $newValue) {
            // Skip excluded fields
            if (in_array($field, $this->excludedFields)) {
                continue;
            }

            $oldValue = $original[$field] ?? null;

            // Format values for display
            $formattedOldValue = $this->formatValue($field, $oldValue);
            $formattedNewValue = $this->formatValue($field, $newValue);

            // Create activity log entry for this field change
            ActivityLog::create([
                'loggable_type' => Incident::class,
                'loggable_id' => $incident->id,
                'user_id' => auth()->id(),
                'action' => 'updated',
                'field_name' => $field,
                'old_value' => $formattedOldValue,
                'new_value' => $formattedNewValue,
                'description' => $this->getFieldChangeDescription($field, $formattedOldValue, $formattedNewValue),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }

    /**
     * Handle the Incident "deleted" event.
     */
    public function deleted(Incident $incident): void
    {
        ActivityLog::create([
            'loggable_type' => Incident::class,
            'loggable_id' => $incident->id,
            'user_id' => auth()->id(),
            'action' => 'deleted',
            'description' => 'Incident deleted: ' . $incident->incident_code,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Format field value for display
     */
    protected function formatValue(string $field, $value): ?string
    {
        if ($value === null) {
            return null;
        }

        // Format datetime fields
        if (in_array($field, ['started_at', 'resolved_at', 'journey_started_at', 'island_arrival_at', 'work_started_at', 'work_completed_at', 'rca_received_at'])) {
            return is_string($value) ? $value : (is_object($value) ? $value->format('Y-m-d H:i:s') : $value);
        }

        // Format boolean fields
        if (in_array($field, ['exceeded_sla', 'rca_required'])) {
            return $value ? 'Yes' : 'No';
        }

        // Return as string
        return (string) $value;
    }

    /**
     * Get human-readable description for field change
     */
    protected function getFieldChangeDescription(string $field, ?string $oldValue, ?string $newValue): string
    {
        $fieldName = ucwords(str_replace('_', ' ', $field));

        if ($oldValue === null && $newValue !== null) {
            return "Set {$fieldName} to \"{$newValue}\"";
        }

        if ($oldValue !== null && $newValue === null) {
            return "Cleared {$fieldName}";
        }

        return "Changed {$fieldName} from \"{$oldValue}\" to \"{$newValue}\"";
    }
}
