<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RcaActionPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'rca_id',
        'action_item',
        'responsible_person',
        'due_date',
        'status',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function rca()
    {
        return $this->belongsTo(Rca::class);
    }

    public function getStatusBadgeColorClass()
    {
        return match($this->status) {
            'Pending' => 'bg-gray-100 text-gray-800',
            'In Progress' => 'bg-blue-100 text-blue-800',
            'Completed' => 'bg-green-100 text-green-800',
            'Cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
