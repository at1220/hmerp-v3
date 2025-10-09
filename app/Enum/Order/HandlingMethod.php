<?php

namespace App\Enum\Order;

use Filament\Support\Contracts\HasLabel;

enum HandlingMethod: string implements HasLabel
{
    case None = 'none';
    case Hands = 'hands';
    case Forklift = 'forklift';
    case Other = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::None => 'Không',
            self::Hands => 'Bốc tay',
            self::Forklift => 'Xe nâng',
            self::Other => 'Khác',
        };
    }
}
