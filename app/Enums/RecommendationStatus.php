<?php

namespace App\Enums;

enum RecommendationStatus: string
{
    case Recommended        = 'recommended';
    case ContinuedCoaching  = 'continued_coaching';
    case NotRecommended     = 'not_recommended';

    public function label(): string
    {
        return match($this) {
            self::Recommended       => 'Direkomendasikan',
            self::ContinuedCoaching => 'Pembinaan Lanjutan',
            self::NotRecommended    => 'Tidak Direkomendasikan',
        };
    }

    public function badgeColor(): string
    {
        return match($this) {
            self::Recommended       => 'green',
            self::ContinuedCoaching => 'yellow',
            self::NotRecommended    => 'red',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Recommended       => '✅',
            self::ContinuedCoaching => '⚠️',
            self::NotRecommended    => '❌',
        };
    }
}
