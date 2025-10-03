<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

// Enum TypeContact
enum TypeContact: string implements HasLabel
{
    case Fulltime = 'fulltime';
    case Probation = 'probation';
    case Parttime = 'parttime';
    case Intern = 'intern';
    case Partner = 'partner';

    public function getLabel(): string
    {
        return match ($this) {
            self::Fulltime => 'Toàn thời gian',
            self::Probation => 'Thử việc',
            self::Parttime => 'Bán thời gian',
            self::Intern => 'Thực tập',
            self::Partner => 'Cộng tác viên',
        };
    }
}
