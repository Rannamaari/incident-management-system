<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteTechnology extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'technology',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the site this technology belongs to.
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
