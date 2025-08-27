<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Income;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class IncomeOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $userId = Auth::id();

        $totalIncome = Income::where('account_id', $userId)->sum('amount');
        $totalExpense = Expense::where('account_id', $userId)->sum('amount');
        $totalWealth = $totalIncome - $totalExpense;

        $lastMonth = Carbon::now()->subMonth();
        $currentMonth = Carbon::now();

        $lastMonthIncome = Income::where('account_id', $userId)
            ->whereMonth('received_at', $lastMonth->month)
            ->whereYear('received_at', $lastMonth->year)
            ->sum('amount');

        $currentMonthIncome = Income::where('account_id', $userId)
            ->whereMonth('received_at', $currentMonth->month)
            ->whereYear('received_at', $currentMonth->year)
            ->sum('amount');

        $percentChange = $lastMonthIncome > 0
            ? number_format((($currentMonthIncome - $lastMonthIncome) / $lastMonthIncome) * 100, 2)
            : '100';

        return [
            Stat::make('Last Month Income', money_idr($lastMonthIncome))
                ->description('Income earned last month')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('warning'),

            Stat::make('This Month Income', money_idr($currentMonthIncome))
                ->description($percentChange . '% ' . ($percentChange >= 0 ? 'increase' : 'decrease'))
                ->descriptionIcon($percentChange >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
                ->color($percentChange >= 0 ? 'success' : 'danger'),

            Stat::make('Current Total Wealth', money_idr($totalWealth))
                ->description('Assets after subtracting expenses')
                ->descriptionIcon('heroicon-o-wallet')
                ->color('primary'),
        ];
    }
}
