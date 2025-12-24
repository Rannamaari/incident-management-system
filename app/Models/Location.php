<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'region_id',
        'location_key',
        'location_name',
    ];

    /**
     * Get the region this location belongs to.
     */
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Get the sites for this location.
     */
    public function sites()
    {
        return $this->hasMany(Site::class);
    }
}
