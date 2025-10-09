<?php

namespace App\Enum\Order;

use Filament\Support\Contracts\HasLabel;

// Enum TypeContact
enum Type: string implements HasLabel
{
    case Trip = 'trip';
    case Frozen = 'frozen';
    case Container = 'container';
    case Freight = 'freight';
    case Crane = 'crane';

    public function getLabel(): string
    {
        return match ($this) {
            self::Trip => 'Chuyến',
            self::Frozen => 'Đông lạnh',
            self::Container => 'Container',
            self::Freight => 'Hàng chành',
            self::Crane => 'Cẩu',
        };
    }
}
