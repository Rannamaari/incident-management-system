<?php

namespace App\Observers;

use App\Models\Rca;
use App\Models\ActivityLog;

class RcaObserver
{
    /**
     * Fields to exclude from logging (timestamps, auto-calculated fields)
     */
    protected array $excludedFields = [
        'created_at',
        'updated_at',
    ];

    /**
     * Handle the Rca "created" event.
     */
    public function created(Rca $rca): void
    {
        ActivityLog::create([
            'loggable_type' => Rca::class,
            'loggable_id' => $rca->id,
            'user_id' => auth()->id(),
            'action' => 'created',
            'description' => 'RCA created: ' . $rca->rca_number,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Handle the Rca "updated" event.
     */
    public function updated(Rca $rca): void
    {
        // Get all changed attributes
        $dirty = $rca->getDirty();

        // Get original values before update
        $original = $rca->getOriginal();

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
                'loggable_type' => Rca::class,
                'loggable_id' => $rca->id,
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
     * Handle the Rca "deleted" event.
     */
    public function deleted(Rca $rca): void
    {
        ActivityLog::create([
            'loggable_type' => Rca::class,
            'loggable_id' => $rca->id,
            'user_id' => auth()->id(),
            'action' => 'deleted',
            'description' => 'RCA deleted: ' . $rca->rca_number,
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
        if (in_array($field, ['reviewed_at'])) {
            return is_string($value) ? $value : (is_object($value) ? $value->format('Y-m-d H:i:s') : $value);
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
