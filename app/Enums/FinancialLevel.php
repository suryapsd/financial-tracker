<?php

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum FinancialLevel: int implements HasColor, HasIcon, HasLabel
{
    case Level0 = 0;
    case Level1 = 1;
    case Level2 = 2;
    case Level3 = 3;
    case Level4 = 4;
    case Level5 = 5;
    case Level6 = 6;

    public function getLabel(): string
    {
        return match ($this) {
            self::Level0 => '💥 Bankrupt (Assets < Debt)',
            self::Level1 => '🔗 Trapped in Debt (Debt > Net Worth)',
            self::Level2 => '🎭 Looks Rich (Money < Debt)',
            self::Level3 => '💸 Paycheck to Paycheck',
            self::Level4 => '🌸 Has Emergency Fund',
            self::Level5 => '💰 Retirement Fund',
            self::Level6 => '🏆 Has Inheritance',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Level0 => 'danger',
            self::Level1 => 'danger',
            self::Level2 => 'warning',
            self::Level3 => 'warning',
            self::Level4 => 'success',
            self::Level5 => 'success',
            self::Level6 => 'success',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Level0 => 'heroicon-o-x-circle',
            self::Level1 => 'heroicon-o-link',
            self::Level2 => 'heroicon-o-eye',
            self::Level3 => 'heroicon-o-currency-dollar',
            self::Level4 => 'heroicon-o-sparkles',
            self::Level5 => 'heroicon-o-banknotes',
            self::Level6 => 'heroicon-o-trophy',
        };
    }
}
