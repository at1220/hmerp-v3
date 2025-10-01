<?php

namespace App\Enum;

use Filament\Support\Contracts\HasLabel;

enum UserStatus: string implements HasLabel
{
    case Active = 'active';
    case Unactive = 'unactive';
    case Break = 'break';

    public function getLabel(): string
    {
        return match ($this) {
            self::Active => '✅ Đang hoạt động',
            self::Unactive => '🚫 Ngưng hoạt động',
            self::Break => '⏸️ Tạm nghỉ',
        };
    }

    public function getTitle(): string
    {
        return match ($this) {
            self::Active => 'Đang hoạt động',
            self::Unactive => 'Ngưng hoạt động',
            self::Break => 'Tạm nghỉ',
        };
    }
}
