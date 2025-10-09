<?php

namespace App\Enum\Order;

use Filament\Support\Contracts\HasLabel;

// Enum TypeContact
enum TypeArise: string implements HasLabel
{
    case None = 'none';
    case PickUp = 'pick_up';
    case Delivery = 'delivery';
    case All = 'all';
    case Other = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::None => 'Không',
            self::PickUp => 'Đầu lên',
            self::Delivery => 'Đầu xuống',
            self::All => '2 đầu',
            self::Other => 'Khác',
        };
    }
}
