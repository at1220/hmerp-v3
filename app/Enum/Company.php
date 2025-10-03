<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

// Enum Position
enum Company: string implements HasLabel
{
    case HM = 'hm';
    case Homis = 'homis';
    case Homex = 'homex';
    case HP = 'hp';
    public function getLabel(): string
    {
        return match ($this) {
            self::HM => 'Hoàng Minh',
            self::Homis => 'Homis',
            self::Homex => 'Homex',
            self::HP => 'Hoàng Phúc',
        };
    }
}
