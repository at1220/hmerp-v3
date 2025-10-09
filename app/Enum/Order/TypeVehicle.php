<?php

namespace App\Enum\Order;

use Filament\Support\Contracts\HasLabel;

// Enum TypeContact
enum TypeVehicle: string implements HasLabel
{
    case Truck = 'truck';
    case Container = 'container';
    case Forklift = 'forklift';

    public function getLabel(): string
    {
        return match ($this) {
            self::Truck => 'Tải',
            self::Forklift => 'Nâng',
            self::Container => 'Container',
        };
    }
}
