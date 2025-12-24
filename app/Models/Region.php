<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
    ];

    /**
     * Get the locations for this region.
     */
    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    /**
     * Get the sites for this region.
     */
    public function sites()
    {
        return $this->hasMany(Site::class);
    }
}
