<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ActionPoint extends Model
{
    protected $fillable = [
        'incident_id',
        'description',
        'due_date',
        'completed',
        'completed_at',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    /**
     * Relationship with Incident
     */
    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }

    /**
     * Mark action point as completed
     */
    public function markCompleted()
    {
        $this->update([
            'completed' => true,
            'completed_at' => now(),
        ]);
    }

    /**
     * Mark action point as pending
     */
    public function markPending()
    {
        $this->update([
            'completed' => false,
            'completed_at' => null,
        ]);
    }

    /**
     * Check if action point is overdue
     */
    public function isOverdue(): bool
    {
        return !$this->completed && $this->due_date < now()->toDateString();
    }

    /**
     * Get status badge color class
     */
    public function getStatusColorClass(): string
    {
        if ($this->completed) {
            return 'bg-green-100 text-green-800';
        }
        
        if ($this->isOverdue()) {
            return 'bg-red-100 text-red-800';
        }
        
        return 'bg-yellow-100 text-yellow-800';
    }

    /**
     * Get status text
     */
    public function getStatusText(): string
    {
        if ($this->completed) {
            return 'Completed';
        }
        
        if ($this->isOverdue()) {
            return 'Overdue';
        }
        
        return 'Pending';
    }
}
