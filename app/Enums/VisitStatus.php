<?php

namespace App\Enums;

enum VisitStatus: string
{
    case Scheduled         = 'scheduled';
    case InProgress        = 'in_progress';
    case WaitingValidation = 'waiting_validation';
    case NeedsRevision     = 'needs_revision';
    case Completed         = 'completed';
    case Cancelled         = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Scheduled         => 'Ditugaskan',
            self::InProgress        => 'Sedang Dikerjakan',
            self::WaitingValidation => 'Menunggu Validasi',
            self::NeedsRevision     => 'Perlu Revisi',
            self::Completed         => 'Selesai / Tervalidasi',
            self::Cancelled         => 'Dibatalkan',
        };
    }

    public function badgeColor(): string
    {
        return match($this) {
            self::Scheduled         => 'blue',
            self::InProgress        => 'yellow',
            self::WaitingValidation => 'amber',
            self::NeedsRevision     => 'orange',
            self::Completed         => 'green',
            self::Cancelled         => 'red',
        };
    }

    /** Menandakan apakah kunjungan dapat diisi/diedit oleh petugas */
    public function isEditable(): bool
    {
        return in_array($this, [self::Scheduled, self::InProgress, self::NeedsRevision]);
    }
}
