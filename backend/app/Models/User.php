<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property string $role  SUPER_ADMIN | ADMIN | STAF
 */
class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = ['name', 'email', 'password', 'role', 'is_active', 'last_login_at'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active && in_array($this->role, ['SUPER_ADMIN', 'ADMIN', 'STAF'], true);
    }

    public function isSuperAdmin(): bool { return $this->role === 'SUPER_ADMIN'; }
    public function isAdmin(): bool { return $this->role === 'ADMIN' || $this->isSuperAdmin(); }

    /** STAF hanya bisa manage event yang di-assign */
    public function canManageEvent(int $eventId): bool
    {
        if ($this->isAdmin()) return true;
        return $this->eventAssignments()->where('event_id', $eventId)->exists();
    }

    public function eventAssignments()
    {
        return $this->belongsToMany(Event::class, 'event_staff')->withPivot('assigned_at');
    }
}
