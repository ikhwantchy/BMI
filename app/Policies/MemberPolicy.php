<?php

namespace App\Policies;

use App\Models\Member;
use App\Models\User;

class MemberPolicy
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
        return $this->checkPermission($user, 'members.view', ['petugas_lapangan', 'asisten_manajer', 'manajer', 'pengurus', 'pengawas', 'system_admin']);
    }

    public function view(User $user, Member $member): bool
    {
        return $this->checkPermission($user, 'members.view', ['petugas_lapangan', 'asisten_manajer', 'manajer', 'pengurus', 'pengawas', 'system_admin']);
    }

    public function create(User $user): bool
    {
        return $this->checkPermission($user, 'members.create', ['petugas_lapangan', 'system_admin']);
    }

    public function update(User $user, Member $member): bool
    {
        return $this->checkPermission($user, 'members.update', ['petugas_lapangan', 'manajer', 'system_admin']);
    }

    public function delete(User $user, Member $member): bool
    {
        return $this->checkPermission($user, 'members.delete', ['manajer', 'system_admin']);
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
