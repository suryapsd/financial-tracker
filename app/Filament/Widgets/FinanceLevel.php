<?php

namespace App\Filament\Widgets;

use App\Enums\FinancialLevel;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinanceLevel extends StatsOverviewWidget
{
    protected string $view = 'filament.widgets.finance-level';
    protected int|string|array $columnSpan = '1';
    protected static ?int $sort = 2;

    public function getData(): array
    {
        $user = Auth::user();
        $getLevel = $user->determineFinancialLevel();
        $currentLevel = FinancialLevel::from($getLevel);

        $levels = collect(FinancialLevel::cases())
            // ->sortByAsc(fn($level) => $level->value)
            ->map(fn($level) => [
                'level' => $level->value,
                'label' => $level->getLabel(),
                'icon' => $level->getIcon(),
                'current' => $level->value <= $currentLevel->value,
            ])
            ->values()
            ->all();

        $totalAssets = $user->assets->sum('value');
        $totalDebts = $user->debts->sum('amount');
        $netWorth = $totalAssets - $totalDebts;

        $sisaDana = $netWorth;
        $incomePasts = $user->incomes()->whereMonth('received_at', now()->subMonth()->month)->sum('amount');
        $expensePasts = $user->expenses()->whereMonth('expense_date', now()->subMonth()->month)->sum('amount');

        return [
            'naikLevel' => $netWorth,
            'levels' => $levels,
            'sisaDana' => $sisaDana,
            'perBulan' => $incomePasts,
            'bulan' => $incomePasts > 0 ? ceil($sisaDana / $incomePasts) : 0,
            'bulanTanpaGaji' => $expensePasts > 0 ? round($sisaDana / $expensePasts, 2) : 0,
        ];
    }
}
