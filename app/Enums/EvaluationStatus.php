<?php

namespace App\Enums;

enum EvaluationStatus: string
{
    case Draft            = 'draft';
    case WaitingValidation = 'waiting_validation';
    case NeedsRevision    = 'needs_revision';
    case Validated        = 'validated';
    case Rejected         = 'rejected';

    public function label(): string
    {
        return match($this) {
            self::Draft             => 'Draft',
            self::WaitingValidation => 'Menunggu Validasi',
            self::NeedsRevision     => 'Perlu Revisi',
            self::Validated         => 'Tervalidasi',
            self::Rejected          => 'Ditolak',
        };
    }

    public function badgeColor(): string
    {
        return match($this) {
            self::Draft             => 'gray',
            self::WaitingValidation => 'yellow',
            self::NeedsRevision     => 'orange',
            self::Validated         => 'green',
            self::Rejected          => 'red',
        };
    }

    /** Status di mana data masih bisa diubah oleh petugas */
    public function isEditable(): bool
    {
        return in_array($this, [self::Draft, self::NeedsRevision]);
    }

    /** Status final — tidak bisa diubah siapapun */
    public function isFinal(): bool
    {
        return $this === self::Validated;
    }
}
