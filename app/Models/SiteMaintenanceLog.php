<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteMaintenanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'maintenance_date',
        'maintenance_type',
        'description',
        'performed_by',
        'notes',
    ];

    protected $casts = [
        'maintenance_date' => 'date',
    ];

    /**
     * Get the site that owns the maintenance log.
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
