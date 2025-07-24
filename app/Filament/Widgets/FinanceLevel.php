<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Enums\FinancialLevel;

class FinanceLevel extends Widget
{
    protected static string $view = 'filament.widgets.finance-level';
    protected int|string|array $columnSpan = '1';
    protected static ?int $sort = 2;

    public function getData(): array
    {
        $currentLevel = FinancialLevel::from(1);;

        $levels = collect(FinancialLevel::cases())
            // ->sortByAsc(fn($level) => $level->value)
            ->map(fn($level) => [
                'level' => $level->value,
                'label' => $level->getLabel(),
                'icon' => $level->getIcon(),
                'current' => $level === $currentLevel,
            ])
            ->values()
            ->all();

        $sisaDana = 5675269623;
        $perBulan = 10000000;

        return [
            'levels' => $levels,
            'sisaDana' => $sisaDana,
            'perBulan' => $perBulan,
            'bulan' => ceil($sisaDana / $perBulan),
            'bulanTanpaGaji' => round($sisaDana / 1297071582, 2),
        ];
    }
}
