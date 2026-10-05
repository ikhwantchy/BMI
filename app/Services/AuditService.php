<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * AuditService
 *
 * Mencatat semua aktivitas penting ke audit_logs dengan branch_id dan context.
 * Dipanggil dari controller atau listener setelah operasi penting dilakukan.
 */
class AuditService
{
    public function log(
        string $action,
        string $entityType,
        int|string|null $entityId = null,
        array $oldValues = [],
        array $newValues = [],
        ?string $context = null,
        ?int $branchId = null
    ): AuditLog {
        $user = Auth::user();
        $resolvedBranchId = $branchId ?? $user?->branch_id;

        return AuditLog::create([
            'user_id'     => $user?->id,
            'branch_id'   => $resolvedBranchId,
            'action'      => $action,
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'old_values'  => empty($oldValues) ? null : $oldValues,
            'new_values'  => empty($newValues) ? null : $newValues,
            'ip_address'  => Request::ip(),
            'user_agent'  => Request::userAgent(),
            'context'     => $context,
        ]);
    }

    public function logCreate(object $model, ?string $context = null): AuditLog
    {
        $branchId = property_exists($model, 'branch_id') || isset($model->branch_id) ? $model->branch_id : null;
        return $this->log('create', get_class($model), $model->id, [], $model->toArray(), $context, $branchId);
    }

    public function logUpdate(object $model, array $oldValues, ?string $context = null): AuditLog
    {
        $branchId = property_exists($model, 'branch_id') || isset($model->branch_id) ? $model->branch_id : null;
        return $this->log('update', get_class($model), $model->id, $oldValues, $model->toArray(), $context, $branchId);
    }

    public function logDelete(object $model, ?string $context = null): AuditLog
    {
        $branchId = property_exists($model, 'branch_id') || isset($model->branch_id) ? $model->branch_id : null;
        return $this->log('delete', get_class($model), $model->id, $model->toArray(), [], $context, $branchId);
    }

    public function logSubmit(object $model, ?string $context = null): AuditLog
    {
        $branchId = property_exists($model, 'branch_id') || isset($model->branch_id) ? $model->branch_id : null;
        return $this->log('submit', get_class($model), $model->id, [], [], $context, $branchId);
    }

    public function logValidate(object $model, ?string $context = null): AuditLog
    {
        $branchId = property_exists($model, 'branch_id') || isset($model->branch_id) ? $model->branch_id : null;
        return $this->log('validate', get_class($model), $model->id, [], [], $context, $branchId);
    }

    public function logReject(object $model, ?string $reason = null): AuditLog
    {
        $branchId = property_exists($model, 'branch_id') || isset($model->branch_id) ? $model->branch_id : null;
        return $this->log('reject', get_class($model), $model->id, [], [], $reason ? "Alasan: {$reason}" : null, $branchId);
    }

    public function logRevise(object $model, ?string $notes = null): AuditLog
    {
        $branchId = property_exists($model, 'branch_id') || isset($model->branch_id) ? $model->branch_id : null;
        return $this->log('revise', get_class($model), $model->id, [], [], $notes ? "Catatan revisi: {$notes}" : null, $branchId);
    }

    public function logLogin(int $userId): AuditLog
    {
        $user = Auth::user();
        return AuditLog::create([
            'user_id'     => $userId,
            'branch_id'   => $user?->branch_id,
            'action'      => 'login',
            'entity_type' => 'App\Models\User',
            'entity_id'   => $userId,
            'ip_address'  => Request::ip(),
            'user_agent'  => Request::userAgent(),
            'context'     => 'Berhasil masuk',
        ]);
    }

    public function logFailedLogin(string $identifier, string $reason = 'Kredensial tidak valid'): AuditLog
    {
        return AuditLog::create([
            'user_id'     => null,
            'branch_id'   => null,
            'action'      => 'failed_login',
            'entity_type' => 'App\Models\User',
            'entity_id'   => null,
            'old_values'  => ['attempted_identifier' => $identifier],
            'ip_address'  => Request::ip(),
            'user_agent'  => Request::userAgent(),
            'context'     => $reason,
        ]);
    }

    public function logLogout(int $userId): AuditLog
    {
        $user = Auth::user();
        return AuditLog::create([
            'user_id'     => $userId,
            'branch_id'   => $user?->branch_id,
            'action'      => 'logout',
            'entity_type' => 'App\Models\User',
            'entity_id'   => $userId,
            'ip_address'  => Request::ip(),
            'user_agent'  => Request::userAgent(),
            'context'     => 'Keluar sesi',
        ]);
    }

    public function logRoleChanged(int $targetUserId, array $oldRoles, array $newRoles): AuditLog
    {
        $user = Auth::user();
        return AuditLog::create([
            'user_id'     => $user?->id,
            'branch_id'   => $user?->branch_id,
            'action'      => 'role_changed',
            'entity_type' => 'App\Models\User',
            'entity_id'   => $targetUserId,
            'old_values'  => ['roles' => $oldRoles],
            'new_values'  => ['roles' => $newRoles],
            'ip_address'  => Request::ip(),
            'user_agent'  => Request::userAgent(),
            'context'     => 'Perubahan role pengguna',
        ]);
    }
}
