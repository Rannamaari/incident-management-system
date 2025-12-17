<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RcaTimeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'rca_id',
        'occurred_at',
        'event_description',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
    ];

    public function rca()
    {
        return $this->belongsTo(Rca::class);
    }
}
