<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Incident extends Model
{
    use HasFactory;

    protected $fillable = [
        'incident_code',
        'summary',
        'outage_category',
        'category',
        'outage_category_id',
        'category_id',
        'fault_type_id',
        'resolution_team_id',
        'affected_services',
        'started_at',
        'resolved_at',
        'duration_minutes',
        'fault_type',
        'root_cause',
        'delay_reason',
        'resolution_team',
        'journey_started_at',
        'island_arrival_at',
        'work_started_at',
        'work_completed_at',
        'travel_time',
        'work_time',
        'pir_rca_no',
        'status',
        'severity',
        'sla_minutes',
        'exceeded_sla',
        'sla_status',
        'rca_required',
        'rca_file_path',
        'rca_received_at',
        'corrective_actions',
        'workaround',
        'solution',
        'recommendation',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'resolved_at' => 'datetime',
        'journey_started_at' => 'datetime',
        'island_arrival_at' => 'datetime',
        'work_started_at' => 'datetime',
        'work_completed_at' => 'datetime',
        'rca_received_at' => 'datetime',
        'exceeded_sla' => 'boolean',
        'rca_required' => 'boolean',
        'duration_minutes' => 'integer',
        'sla_minutes' => 'integer',
        'travel_time' => 'integer',
        'work_time' => 'integer',
    ];

    // Constants for dropdowns and validation
    public const OUTAGE_CATEGORIES = [
        'Power',
        'Core Network',
        'Database',
        'Partner End Issue Planned',
        'RAN',
        'Transmission',
        'Unknown'
    ];

    public const CATEGORIES = [
        'FBB',
        'RAN',
        'ICT',
        'International',
        'Packet Core',
        'Enterprise'
    ];

    public const FAULT_TYPES = [
        'Fiber Cut',
        'Local Power',
        'RRU Faulty'
    ];

    public const SEVERITIES = [
        'Critical',
        'High',
        'Medium',
        'Low'
    ];

    public const STATUSES = [
        'Open',
        'In Progress',
        'Monitoring',
        'Closed'
    ];

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Incident $incident) {
            if (empty($incident->incident_code)) {
                $incident->incident_code = static::generateIncidentCode($incident);
            }
        });

        static::saving(function (Incident $incident) {
            // Validate delay reason for closed incidents with duration > 5 hours
            if ($incident->status === 'Closed' && 
                $incident->started_at && 
                $incident->resolved_at && 
                empty($incident->delay_reason)) {
                
                $durationHours = $incident->started_at->diffInHours($incident->resolved_at);
                if ($durationHours > 5) {
                    throw new \InvalidArgumentException(
                        "Delay reason is required for closed incidents with duration > 5 hours. " .
                        "Incident duration: {$durationHours} hours."
                    );
                }
            }

            // Derive sla_minutes from severity
            $slaMap = [
                'Critical' => 120, // 2 hours
                'High' => 120,     // 2 hours
                'Medium' => 360,   // 6 hours
                'Low' => 720,      // 12 hours
            ];
            
            $incident->sla_minutes = $slaMap[$incident->severity] ?? 720;

            // Auto-calculate duration if both dates present and duration not manually set
            if ($incident->started_at && 
                $incident->resolved_at && 
                !$incident->isDirty('duration_minutes')) {
                $incident->duration_minutes = $incident->started_at->diffInMinutes($incident->resolved_at);
            }

            // Set exceeded_sla and sla_status based on duration vs SLA
            if ($incident->duration_minutes !== null) {
                // For closed incidents, use actual duration
                $incident->exceeded_sla = $incident->duration_minutes > $incident->sla_minutes;
                $incident->sla_status = $incident->exceeded_sla ? 'SLA Breached' : 'SLA Achieved';
            } elseif ($incident->started_at && $incident->status !== 'Closed') {
                // For open incidents, calculate elapsed time vs SLA
                $elapsedMinutes = $incident->started_at->diffInMinutes(now());
                $incident->exceeded_sla = $elapsedMinutes > $incident->sla_minutes;
                $incident->sla_status = $incident->exceeded_sla ? 'SLA Breached' : 'Within SLA';
            } else {
                // Default values for new incidents
                $incident->exceeded_sla = false;
                $incident->sla_status = 'Within SLA';
            }

            // Set rca_required for High/Critical incidents
            $incident->rca_required = in_array($incident->severity, ['High', 'Critical']);
        });
    }

    /**
     * Get duration in human-readable format.
     */
    protected function durationHms(): Attribute
    {
        return Attribute::make(
            get: function () {
                $totalMinutes = null;

                // For ongoing incidents (Open, In Progress, or Monitoring status), always calculate from started_at to now
                if ($this->started_at && in_array($this->status, ['Open', 'In Progress', 'Monitoring'])) {
                    $totalMinutes = $this->started_at->diffInMinutes(now());
                    return $this->formatDuration($totalMinutes) . ' (ongoing)';
                }

                // For closed incidents, use stored duration_minutes first
                if ($this->duration_minutes !== null) {
                    $totalMinutes = $this->duration_minutes;
                }
                // If no stored duration, calculate from dates
                elseif ($this->started_at && $this->resolved_at) {
                    $totalMinutes = $this->started_at->diffInMinutes($this->resolved_at);
                }

                if ($totalMinutes === null) {
                    return null;
                }

                return $this->formatDuration($totalMinutes);
            }
        );
    }
    
    /**
     * Format duration in human-readable format.
     */
    private function formatDuration(int $totalMinutes): string
    {
        $days = intval($totalMinutes / (24 * 60));
        $remainingMinutes = $totalMinutes % (24 * 60);
        $hours = intval($remainingMinutes / 60);
        $minutes = $remainingMinutes % 60;
        
        // If 24+ hours, show as "X day(s) Y hrs Z mins"
        if ($days > 0) {
            $result = $days . ($days === 1 ? ' day' : ' days');
            if ($hours > 0) {
                $result .= ' ' . $hours . ($hours === 1 ? ' hr' : ' hrs');
            }
            if ($minutes > 0) {
                $result .= ' ' . $minutes . ($minutes === 1 ? ' min' : ' mins');
            }
            return $result;
        }
        
        // If less than 24 hours, show as "X hrs Y mins"
        $result = '';
        if ($hours > 0) {
            $result = $hours . ($hours === 1 ? ' hr' : ' hrs');
        }
        if ($minutes > 0) {
            if ($result) $result .= ' ';
            $result .= $minutes . ($minutes === 1 ? ' min' : ' mins');
        }
        
        // If zero duration
        if (!$result) {
            $result = '0 mins';
        }
        
        return $result;
    }

    /**
     * Scope for filtering by search term.
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('incident_code', 'LIKE', "%{$search}%")
                  ->orWhere('summary', 'LIKE', "%{$search}%")
                  ->orWhere('category', 'LIKE', "%{$search}%")
                  ->orWhere('affected_services', 'LIKE', "%{$search}%");
            });
        }
        
        return $query;
    }

    /**
     * Scope for filtering by status.
     */
    public function scopeStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        
        return $query;
    }

    /**
     * Scope for filtering by severity.
     */
    public function scopeSeverity($query, $severity)
    {
        if ($severity) {
            return $query->where('severity', $severity);
        }
        
        return $query;
    }

    /**
     * Check if RCA file exists.
     */
    public function hasRcaFile(): bool
    {
        return !empty($this->rca_file_path) && file_exists(storage_path('app/' . $this->rca_file_path));
    }

    /**
     * Get RCA file URL.
     */
    public function getRcaFileUrl(): ?string
    {
        if ($this->hasRcaFile()) {
            return asset('storage/' . $this->rca_file_path);
        }
        
        return null;
    }

    /**
     * Get RCA status badge text.
     */
    public function getRcaStatus(): string
    {
        if (!$this->rca_required) {
            return 'Not Required';
        }
        
        if ($this->hasRcaFile()) {
            return 'Attached';
        }
        
        return 'Pending';
    }

    /**
     * Get current SLA status (real-time for open incidents).
     */
    public function getCurrentSlaStatus(): string
    {
        if ($this->status === 'Closed') {
            // For closed incidents, calculate actual duration
            if ($this->duration_minutes !== null) {
                // Use stored duration if available
                return $this->duration_minutes > $this->sla_minutes ? 'SLA Breached' : 'SLA Achieved';
            } elseif ($this->started_at && $this->resolved_at) {
                // Calculate duration from start to resolution
                $actualDuration = $this->started_at->diffInMinutes($this->resolved_at);
                return $actualDuration > $this->sla_minutes ? 'SLA Breached' : 'SLA Achieved';
            }
            return 'SLA Achieved'; // Default for closed incidents
        } elseif ($this->started_at) {
            // For open incidents, calculate current elapsed time
            $elapsedMinutes = $this->started_at->diffInMinutes(now());
            return $elapsedMinutes > $this->sla_minutes ? 'SLA Breached' : 'Within SLA';
        }
        
        return 'Within SLA';
    }

    /**
     * Check if SLA is currently exceeded (real-time for open incidents).
     */
    public function isCurrentlySlaExceeded(): bool
    {
        if ($this->status === 'Closed') {
            // For closed incidents, calculate actual duration
            if ($this->duration_minutes !== null) {
                // Use stored duration if available
                return $this->duration_minutes > $this->sla_minutes;
            } elseif ($this->started_at && $this->resolved_at) {
                // Calculate duration from start to resolution
                $actualDuration = $this->started_at->diffInMinutes($this->resolved_at);
                return $actualDuration > $this->sla_minutes;
            }
            return false; // Default for closed incidents
        } elseif ($this->started_at) {
            // For open incidents, calculate current elapsed time
            $elapsedMinutes = $this->started_at->diffInMinutes(now());
            return $elapsedMinutes > $this->sla_minutes;
        }
        
        return false;
    }

    /**
     * Get SLA badge color class (real-time).
     */
    public function getSlaColorClass(): string
    {
        return $this->isCurrentlySlaExceeded() ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800';
    }

    /**
     * Get RCA badge color class.
     */
    public function getRcaColorClass(): string
    {
        if (!$this->rca_required) {
            return 'bg-gray-100 text-gray-800';
        }
        
        if ($this->hasRcaFile()) {
            return 'bg-green-100 text-green-800';
        }
        
        return 'bg-yellow-100 text-yellow-800';
    }

    /**
     * Generate unique incident code.
     * Format: INC-YYYYMMDD-####
     */
    protected static function generateIncidentCode(Incident $incident): string
    {
        $date = $incident->started_at ? $incident->started_at->format('Ymd') : now()->format('Ymd');
        
        // Get daily count of incidents for the same date
        $dailyCount = static::whereDate('started_at', $incident->started_at ?? now())->count();
        
        // Generate code with zero-padded sequential number
        $sequentialNumber = str_pad($dailyCount + 1, 4, '0', STR_PAD_LEFT);
        
        return "INC-{$date}-{$sequentialNumber}";
    }

    /**
     * Relationships
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function outageCategory()
    {
        return $this->belongsTo(OutageCategory::class);
    }

    public function faultType()
    {
        return $this->belongsTo(FaultType::class);
    }

    public function resolutionTeam()
    {
        return $this->belongsTo(ResolutionTeam::class);
    }

    public function logs()
    {
        return $this->hasMany(IncidentLog::class)->orderBy('occurred_at', 'asc');
    }

    public function actionPoints()
    {
        return $this->hasMany(ActionPoint::class)->orderBy('due_date', 'asc');
    }

    public function rca()
    {
        return $this->hasOne(Rca::class);
    }

    /**
     * Check if all action points are completed (for Critical incidents)
     */
    public function hasAllActionPointsCompleted(): bool
    {
        $actionPoints = $this->actionPoints;
        
        if ($actionPoints->isEmpty()) {
            return false;
        }
        
        return $actionPoints->every(function ($actionPoint) {
            return $actionPoint->completed;
        });
    }

    /**
     * Get pending action points count
     */
    public function getPendingActionPointsCount(): int
    {
        return $this->actionPoints()->where('completed', false)->count();
    }
}