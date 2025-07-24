<?php

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AccountType: string implements HasColor, HasIcon, HasLabel
{
    case Bank = 'bank';
    case Ewallet = 'ewallet';
    case Cash = 'cash';
    case Crypto = 'crypto';

    public function getLabel(): string
    {
        return match ($this) {
            self::Bank => 'Bank',
            self::Ewallet => 'E-Wallet',
            self::Cash => 'Tunai',
            self::Crypto => 'Crypto',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Bank => 'info',
            self::Ewallet => 'secondary',
            self::Cash => 'success',
            self::Crypto => 'warning',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Bank => 'heroicon-o-banknotes',
            self::Ewallet => 'heroicon-o-device-phone-mobile',
            self::Cash => 'heroicon-o-currency-dollar',
            self::Crypto => 'heroicon-o-cube',
        };
    }
}
