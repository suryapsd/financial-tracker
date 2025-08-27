<?php

namespace App\Filament\Widgets;

use App\Models\Income;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinanceReview extends StatsOverviewWidget
{
    // protected int|string|array $columnSpan = '3';
    protected static ?int $sort = 1;

    protected function getHeading(): ?string
    {
        return 'Profil Kekayaan Saat Ini';
    }

    protected function getDescription(): ?string
    {
        return 'An overview of some analytics.';
    }

    protected function getStats(): array
    {
        $now = now();
        $lastYear = $now->copy()->subYear();

        // Get monthly trend of income (you can do the same for expenses if needed)
        $incomeTrend = Trend::model(Income::class)
            ->between(start: $lastYear, end: $now)
            ->perMonth()
            ->count();

        // Total income and expense for net worth calculation
        $user = Auth::user();
        $totalAccounts = $user->accounts->sum('balance');
        $totalAssets = $user->assets->sum('value');
        $totalDebts = $user->debts->sum('amount');
        $netWorth = $totalAccounts + $totalAssets - $totalDebts;

        return [
            Stat::make('Wealth Level', 'Level ' . $user->determineFinancialLevel())
                ->description('Automatically calculated from your net worth'),

            Stat::make('Net Worth', money_idr($netWorth))
                ->description('Based on income and expenses')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart(
                    $incomeTrend
                        ->map(fn(TrendValue $value) => $value->aggregate)
                        ->toArray()
                )
                ->color('success'),

            Stat::make('Cash & Savings', money_idr($netWorth))
                ->description('Current financial buffer')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart(
                    $incomeTrend
                        ->map(fn(TrendValue $value) => $value->aggregate)
                        ->toArray()
                )
                ->color('success'),
        ];
    }
}
