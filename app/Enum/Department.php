<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

// Enum Position
enum Department: string implements HasLabel
{
    case SALE_1 = 'sale_1';
    case SALE_2 = 'sale_2';
    case SALE_3 = 'sale_3';
    case HR = 'hr';
    case ACCOUNTANT = 'accountant';
    case DEV = 'dev';
    case MARKETING = 'markerting';
    case DISPATCHER = 'dispatcher';
    case PARKING_1 = 'parking_1';
    case PARKING_2 = 'parking_2';
    case PARKING_3 = 'parking_3';
    case LEGAL = 'legal';
    case GARAGE = 'garage';

    public function getLabel(): string
    {
        return match ($this) {
            self::SALE_1 => 'Kinh doanh 1',
            self::SALE_2 => 'Kinh doanh 2',
            self::SALE_3 => 'Kinh doanh 3',
            self::HR => 'Nhân sự',
            self::ACCOUNTANT => 'Kế toán',
            self::DEV => 'Lập trình',
            self::MARKETING => 'Marketing',
            self::DISPATCHER => 'Điều phối',
            self::PARKING_1 => 'Bãi Thế Lữ',
            self::PARKING_2 => 'Bãi Trương Văn Đa',
            self::PARKING_3 => 'Bãi Biên Hoà',
            self::LEGAL => 'Pháp chế',
            self::GARAGE => 'Garage',
        };
    }
}
