<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum CategoryType: string implements HasColor, HasIcon, HasLabel
{
    case Income = 'income';
    case Expense = 'expense';
    case Asset = 'asset';
    case Debt = 'debt';
    case Emergency = 'emergency';
    case Plan = 'plan';

    public function getLabel(): string
    {
        return match ($this) {
            self::Income => 'Income',
            self::Expense => 'Expense',
            self::Asset => 'Asset',
            self::Debt => 'Debt',
            self::Emergency => 'Emergency',
            self::Plan => 'Budget Plan',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Income => 'success',
            self::Expense => 'danger',
            self::Asset => 'info',
            self::Debt => 'warning',
            self::Emergency => 'gray',
            self::Plan => 'primary',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Income => 'heroicon-o-banknotes',
            self::Expense => 'heroicon-o-arrow-trending-down',
            self::Asset => 'heroicon-o-building-library',
            self::Debt => 'heroicon-o-credit-card',
            self::Emergency => 'heroicon-o-exclamation-triangle',
            self::Plan => 'heroicon-o-star',
        };
    }
}
