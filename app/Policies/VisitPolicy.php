<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Visit;

class VisitPolicy
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
        return $this->checkPermission($user, 'visits.view', ['petugas_lapangan', 'asisten_manajer', 'manajer', 'pengurus', 'pengawas', 'system_admin']);
    }

    public function view(User $user, Visit $visit): bool
    {
        return $this->checkPermission($user, 'visits.view', ['petugas_lapangan', 'asisten_manajer', 'manajer', 'pengurus', 'pengawas', 'system_admin']);
    }

    public function create(User $user): bool
    {
        return $this->checkPermission($user, 'visits.create', ['petugas_lapangan', 'manajer', 'asisten_manajer', 'pengurus', 'system_admin']);
    }

    public function update(User $user, Visit $visit): bool
    {
        // Manajer dan Asisten Manajer dapat memperbarui jadwal & instruksi kunjungan
        if (in_array($user->role, ['manajer', 'asisten_manajer', 'system_admin'])) {
            return true;
        }

        // Petugas Lapangan hanya dapat memperbarui/mengisi kunjungan miliknya
        if ($user->role === 'petugas_lapangan') {
            return $visit->officer_id === $user->id;
        }

        return $this->checkPermission($user, 'visits.update', ['petugas_lapangan', 'manajer', 'asisten_manajer', 'system_admin']);
    }

    public function delete(User $user, Visit $visit): bool
    {
        return $this->checkPermission($user, 'visits.delete', ['manajer', 'system_admin']);
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
