<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

// Enum Position
enum Position: string implements HasLabel
{
    case Chairman = 'chairman';
    case CEO = 'ceo';
    case DeputyCEO = 'deputy_ceo';
    case Manager = 'manager';
    case DeputyManager = 'deputy_manager';
    case Staff = 'staff';
    case Leader = 'leader';

    public function getLabel(): string
    {
        return match ($this) {
            self::Chairman => 'Chủ tịch',
            self::CEO => 'Giám đốc',
            self::DeputyCEO => 'Phó giám đốc',
            self::Manager => 'Trưởng phòng',
            self::DeputyManager => 'Phó phòng',
            self::Staff => 'Nhân viên',
            self::Leader => 'Tổ trưởng',
        };
    }
}
