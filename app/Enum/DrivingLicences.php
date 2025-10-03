<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

// Enum Position
enum DrivingLicences: string implements HasLabel
{
    case B1 = 'b1';
    case B2 = 'b2';
    case C  = 'c';
    case D  = 'd';
    case E  = 'e';
    case F  = 'f';
    case FB2 = 'fb2';
    case FC  = 'fc';
    case FD  = 'fd';
    case FE  = 'fe';
    public function getLabel(): string
    {
        return match ($this) {
             self::B1  => 'Loại B1',
            self::B2  => 'Loại B2',
            self::C   => 'Loại C',
            self::D   => 'Loại D',
            self::E   => 'Loại E',
            self::F   => 'Loại F',
            self::FB2 => 'Loại FB2',
            self::FC  => 'Loại FC',
            self::FD  => 'Loại FD',
            self::FE  => 'Loại FE',
        };
    }
}
