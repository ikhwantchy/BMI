<?php

namespace App\Enums;

enum VisitStatus: string
{
    case Scheduled  = 'scheduled';
    case InProgress = 'in_progress';
    case Completed  = 'completed';
    case Cancelled  = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Scheduled  => 'Ditugaskan',
            self::InProgress => 'Sedang Dikerjakan',
            self::Completed  => 'Selesai / Menunggu Validasi',
            self::Cancelled  => 'Dibatalkan',
        };
    }

    public function badgeColor(): string
    {
        return match($this) {
            self::Scheduled  => 'blue',
            self::InProgress => 'yellow',
            self::Completed  => 'green',
            self::Cancelled  => 'red',
        };
    }
}
