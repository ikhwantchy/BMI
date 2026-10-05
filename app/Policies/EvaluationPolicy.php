<?php

namespace App\Policies;

use App\Models\Evaluation;
use App\Models\User;

class EvaluationPolicy
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
        return $this->checkPermission($user, 'evaluations.view', ['petugas_lapangan', 'asisten_manajer', 'manajer', 'pengurus', 'pengawas', 'system_admin']);
    }

    public function view(User $user, Evaluation $evaluation): bool
    {
        return $this->checkPermission($user, 'evaluations.view', ['petugas_lapangan', 'asisten_manajer', 'manajer', 'pengurus', 'pengawas', 'system_admin']);
    }

    public function create(User $user): bool
    {
        return $this->checkPermission($user, 'evaluations.create', ['petugas_lapangan', 'system_admin']);
    }

    public function update(User $user, Evaluation $evaluation): bool
    {
        if ($evaluation->isLocked()) {
            return false;
        }
        return $this->checkPermission($user, 'evaluations.update', ['petugas_lapangan', 'system_admin']);
    }

    public function submit(User $user, Evaluation $evaluation): bool
    {
        if (!$evaluation->isEditable()) {
            return false;
        }
        return $this->checkPermission($user, 'evaluations.submit', ['petugas_lapangan', 'system_admin']);
    }

    public function approve(User $user, Evaluation $evaluation): bool
    {
        if (!$evaluation->isWaitingValidation()) {
            return false;
        }
        return $this->checkPermission($user, 'evaluations.approve', ['asisten_manajer', 'manajer', 'system_admin']);
    }

    public function reject(User $user, Evaluation $evaluation): bool
    {
        if (!$evaluation->isWaitingValidation()) {
            return false;
        }
        return $this->checkPermission($user, 'evaluations.reject', ['asisten_manajer', 'manajer', 'system_admin']);
    }

    public function revise(User $user, Evaluation $evaluation): bool
    {
        if (!$evaluation->isWaitingValidation()) {
            return false;
        }
        return $this->checkPermission($user, 'evaluations.revise', ['asisten_manajer', 'manajer', 'system_admin']);
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
