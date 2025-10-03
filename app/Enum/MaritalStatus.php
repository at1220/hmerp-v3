<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

// Enum MaritalStatus
enum MaritalStatus: string implements HasLabel
{
    case Unmarried = 'unmarried';
    case Married = 'married';

    public function getLabel(): string
    {
        return match ($this) {
            self::Unmarried => 'Chưa kết hôn',
            self::Married => 'Đã kết hôn',
        };
    }
}
