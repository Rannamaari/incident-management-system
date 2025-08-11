<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResolutionTeam extends Model
{
    protected $fillable = ['name'];

    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }
}
