<?php

namespace App\Enums;

enum CoachingStatus: string
{
    case Pending    = 'pending';
    case InProgress = 'in_progress';
    case Done       = 'done';

    public function label(): string
    {
        return match($this) {
            self::Pending    => 'Belum Dimulai',
            self::InProgress => 'Sedang Berjalan',
            self::Done       => 'Selesai',
        };
    }
}
