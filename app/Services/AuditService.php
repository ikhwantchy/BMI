<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * AuditService
 *
 * Mencatat semua aktivitas penting ke audit_logs.
 * Dipanggil dari controller setelah operasi penting dilakukan.
 */
class AuditService
{
    public function log(
        string $action,
        string $entityType,
        int|string|null $entityId = null,
        array $oldValues = [],
        array $newValues = []
    ): AuditLog {
        return AuditLog::create([
            'user_id'     => Auth::id(),
            'action'      => $action,
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'old_values'  => empty($oldValues) ? null : $oldValues,
            'new_values'  => empty($newValues) ? null : $newValues,
            'ip_address'  => Request::ip(),
            'user_agent'  => Request::userAgent(),
        ]);
    }

    public function logCreate(object $model): AuditLog
    {
        return $this->log('create', get_class($model), $model->id, [], $model->toArray());
    }

    public function logUpdate(object $model, array $oldValues): AuditLog
    {
        return $this->log('update', get_class($model), $model->id, $oldValues, $model->toArray());
    }

    public function logDelete(object $model): AuditLog
    {
        return $this->log('delete', get_class($model), $model->id, $model->toArray());
    }

    public function logSubmit(object $model): AuditLog
    {
        return $this->log('submit', get_class($model), $model->id);
    }

    public function logValidate(object $model): AuditLog
    {
        return $this->log('validate', get_class($model), $model->id);
    }

    public function logReject(object $model): AuditLog
    {
        return $this->log('reject', get_class($model), $model->id);
    }

    public function logLogin(int $userId): AuditLog
    {
        return AuditLog::create([
            'user_id'     => $userId,
            'action'      => 'login',
            'entity_type' => 'App\Models\User',
            'entity_id'   => $userId,
            'ip_address'  => Request::ip(),
            'user_agent'  => Request::userAgent(),
        ]);
    }
}
