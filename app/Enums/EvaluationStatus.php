<?php

namespace App\Enums;

enum EvaluationStatus: string
{
    case Draft            = 'draft';
    case WaitingValidation = 'waiting_validation';
    case Validated        = 'validated';
    case Rejected         = 'rejected';

    public function label(): string
    {
        return match($this) {
            self::Draft             => 'Draft',
            self::WaitingValidation => 'Menunggu Validasi',
            self::Validated         => 'Tervalidasi',
            self::Rejected          => 'Ditolak',
        };
    }

    public function badgeColor(): string
    {
        return match($this) {
            self::Draft             => 'gray',
            self::WaitingValidation => 'yellow',
            self::Validated         => 'green',
            self::Rejected          => 'red',
        };
    }
}
