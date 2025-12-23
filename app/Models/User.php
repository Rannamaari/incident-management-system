<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    // Define role constants
    public const ROLE_ADMIN = 'admin';
    public const ROLE_EDITOR = 'editor';
    public const ROLE_NOC = 'noc';
    public const ROLE_VIEWER = 'viewer';

    public const ROLES = [
        self::ROLE_ADMIN => 'Admin',
        self::ROLE_EDITOR => 'Editor',
        self::ROLE_NOC => 'NOC',
        self::ROLE_VIEWER => 'Viewer',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user has admin role.
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Check if user has editor role or higher.
     */
    public function isEditor(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_EDITOR]);
    }

    /**
     * Check if user has NOC role.
     */
    public function isNoc(): bool
    {
        return $this->role === self::ROLE_NOC;
    }

    /**
     * Check if user has viewer role or higher.
     */
    public function isViewer(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_EDITOR, self::ROLE_NOC, self::ROLE_VIEWER]);
    }

    /**
     * Check if user can create/edit incidents.
     */
    public function canEditIncidents(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_EDITOR, self::ROLE_NOC]);
    }

    /**
     * Check if user can delete incidents.
     */
    public function canDeleteIncidents(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Check if user can export data.
     */
    public function canExportData(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_EDITOR, self::ROLE_NOC]);
    }

    /**
     * Check if user can manage users.
     */
    public function canManageUsers(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Check if user can manage contacts (phone book).
     */
    public function canManageContacts(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_NOC]);
    }

    /**
     * Check if user can manage temporary sites (view and toggle status).
     */
    public function canManageTemporarySites(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_NOC]);
    }

    /**
     * Check if user can manage sites.
     */
    public function canManageSites(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_NOC]);
    }

    /**
     * Get role display name.
     */
    public function getRoleDisplayName(): string
    {
        return self::ROLES[$this->role] ?? 'Unknown';
    }
}
