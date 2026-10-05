<?php

namespace App\Policies;

use App\Enums\EvaluationStatus;
use App\Models\Evaluation;
use App\Models\User;

class EvaluationPolicy
{
    /**
     * Validator (super admin) can bypass all gates.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('system_admin')) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['evaluations.view']);
    }

    public function view(User $user, Evaluation $evaluation): bool
    {
        return $user->hasPermissionTo('evaluations.view');
    }

    /**
     * Hanya petugas yang bisa mengisi evaluasi (masih draft/needs_revision).
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('evaluations.create');
    }

    /**
     * Edit hanya diizinkan jika status masih bisa diubah (draft atau needs_revision).
     */
    public function update(User $user, Evaluation $evaluation): bool
    {
        if ($evaluation->isLocked()) {
            return false;
        }
        return $user->hasPermissionTo('evaluations.update');
    }

    /**
     * Submit hanya dari status editable → waiting_validation.
     */
    public function submit(User $user, Evaluation $evaluation): bool
    {
        if (!$evaluation->isEditable()) {
            return false;
        }
        return $user->hasPermissionTo('evaluations.submit');
    }

    /**
     * Approve/Validate hanya oleh asisten/manajer, hanya saat waiting_validation.
     */
    public function approve(User $user, Evaluation $evaluation): bool
    {
        if (!$evaluation->isWaitingValidation()) {
            return false;
        }
        return $user->hasPermissionTo('evaluations.approve');
    }

    /**
     * Reject hanya saat waiting_validation.
     */
    public function reject(User $user, Evaluation $evaluation): bool
    {
        if (!$evaluation->isWaitingValidation()) {
            return false;
        }
        return $user->hasPermissionTo('evaluations.reject');
    }

    /**
     * Revise — kirim balik ke petugas dengan catatan (needs_revision).
     */
    public function revise(User $user, Evaluation $evaluation): bool
    {
        if (!$evaluation->isWaitingValidation()) {
            return false;
        }
        return $user->hasPermissionTo('evaluations.revise');
    }
}
