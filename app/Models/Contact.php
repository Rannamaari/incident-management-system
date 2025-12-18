<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'company',
        'role',
        'category',
        'region',
        'atoll',
        'island',
        'site',
        'notes',
        'source_sheet',
        'raw',
    ];

    /**
     * Scope for searching contacts
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            $searchLower = strtolower($search);
            return $query->where(function ($q) use ($searchLower) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('LOWER(phone) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('LOWER(email) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('LOWER(company) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('LOWER(island) LIKE ?', ["%{$searchLower}%"])
                  ->orWhereRaw('LOWER(atoll) LIKE ?', ["%{$searchLower}%"]);
            });
        }

        return $query;
    }

    /**
     * Scope for filtering by category
     */
    public function scopeCategory($query, $category)
    {
        if ($category) {
            return $query->where('category', $category);
        }

        return $query;
    }

    /**
     * Scope for filtering by atoll
     */
    public function scopeAtoll($query, $atoll)
    {
        if ($atoll) {
            return $query->where('atoll', $atoll);
        }

        return $query;
    }

    /**
     * Get initials for avatar
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($this->name, 0, 2));
    }
}
