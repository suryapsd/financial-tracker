<?php

namespace App\Filament\Widgets;

use App\Models\Income;
use App\Models\Expense;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class FinanceReview extends BaseWidget
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
        $totalIncome = Income::sum('amount');
        $totalExpense = Expense::sum('amount');
        $netWorth = $totalIncome - $totalExpense;

        // Calculate wealth level
        $wealthLevel = match (true) {
            $netWorth >= 100_000 => 'Level 5',
            $netWorth >= 50_000 => 'Level 4',
            $netWorth >= 20_000 => 'Level 3',
            $netWorth >= 5_000 => 'Level 2',
            default => 'Level 1 - Starting',
        };

        return [
            Stat::make('Wealth Level', $wealthLevel)
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
