<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

// Enum Level
enum Level: string implements HasLabel
{
    case Secondary = 'secondary';
    case Highschool = 'highschool';
    case Intermediate = 'intermediate';
    case College = 'college';
    case Bachelor = 'bachelor';
    case Master = 'master';
    case Doctorate = 'doctorate';

    public function getLabel(): string
    {
        return match ($this) {
            self::Secondary => 'Trung học cơ sở',
            self::Highschool => 'Trung học phổ thông',
            self::Intermediate => 'Trung cấp',
            self::College => 'Cao đẳng',
            self::Bachelor => 'Đại học',
            self::Master => 'Thạc sĩ',
            self::Doctorate => 'Tiến sĩ',
        };
    }
}
