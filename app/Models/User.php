<?php

namespace App\Models;

use App\Enums\MembershipStatus;
use App\Enums\VisitStatus;
use App\Enums\EvaluationStatus;
use App\Enums\RecommendationStatus;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ─── Relationships ───────────────────────────────────────────────────────

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class, 'officer_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function isOfficer(): bool
    {
        return $this->role === 'petugas_lapangan';
    }

    public function isAssistantManager(): bool
    {
        return $this->role === 'asisten_manajer';
    }

    public function isManager(): bool
    {
        return $this->role === 'manajer';
    }

    public function canValidate(): bool
    {
        return in_array($this->role, ['asisten_manajer', 'manajer']);
    }

    public function roleLabel(): string
    {
        return match($this->role) {
            'petugas_lapangan' => 'Petugas Lapangan',
            'asisten_manajer'  => 'Asisten Manajer',
            'manajer'          => 'Manajer',
            default            => $this->role,
        };
    }
}
