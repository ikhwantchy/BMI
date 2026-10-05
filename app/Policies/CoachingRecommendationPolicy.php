<?php

namespace App\Policies;

use App\Models\CoachingRecommendation;
use App\Models\User;

class CoachingRecommendationPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->role === 'system_admin' || $user->hasRole('system_admin')) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return $this->checkPermission($user, 'coaching.view', ['petugas_lapangan', 'asisten_manajer', 'manajer', 'pengurus', 'pengawas', 'system_admin']);
    }

    public function view(User $user, CoachingRecommendation $coaching): bool
    {
        return $this->checkPermission($user, 'coaching.view', ['petugas_lapangan', 'asisten_manajer', 'manajer', 'pengurus', 'pengawas', 'system_admin']);
    }

    public function create(User $user): bool
    {
        return $this->checkPermission($user, 'coaching.create', ['petugas_lapangan', 'asisten_manajer', 'manajer', 'system_admin']);
    }

    public function update(User $user, CoachingRecommendation $coaching): bool
    {
        return $this->checkPermission($user, 'coaching.update', ['petugas_lapangan', 'asisten_manajer', 'manajer', 'system_admin']);
    }

    private function checkPermission(User $user, string $permission, array $fallbackRoles = []): bool
    {
        if (in_array($user->role, $fallbackRoles)) {
            return true;
        }
        try {
            return $user->hasPermissionTo($permission);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
