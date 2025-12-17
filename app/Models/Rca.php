<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rca extends Model
{
    use HasFactory;

    protected $fillable = [
        'incident_id',
        'title',
        'rca_number',
        'problem_description',
        'problem_analysis',
        'root_cause',
        'workaround',
        'solution',
        'recommendation',
        'status',
        'created_by',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    // Relationships
    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }

    public function timeLogs()
    {
        return $this->hasMany(RcaTimeLog::class)->orderBy('occurred_at', 'desc');
    }

    public function actionPoints()
    {
        return $this->hasMany(RcaActionPoint::class)->orderBy('due_date', 'asc');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'loggable')->orderBy('created_at', 'desc');
    }

    // Accessors & Helpers
    public function getStatusBadgeColorClass()
    {
        return match($this->status) {
            'Draft' => 'bg-gray-100 text-gray-800',
            'In Review' => 'bg-yellow-100 text-yellow-800',
            'Approved' => 'bg-green-100 text-green-800',
            'Closed' => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    // Boot method to auto-generate RCA number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($rca) {
            if (empty($rca->rca_number)) {
                $rca->rca_number = 'RCA-' . date('Y') . '-' . str_pad(static::whereYear('created_at', date('Y'))->count() + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
