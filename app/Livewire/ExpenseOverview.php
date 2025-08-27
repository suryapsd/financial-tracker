<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Income;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ExpenseOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $userId = Auth::id();

        $totalIncome = Income::where('account_id', $userId)->sum('amount');
        $totalExpense = Expense::where('account_id', $userId)->sum('amount');
        $totalWealth = $totalIncome - $totalExpense;

        $lastMonth = Carbon::now()->subMonth();
        $currentMonth = Carbon::now();

        $lastMonthExpense = Expense::where('account_id', $userId)
            ->whereMonth('expense_date', $lastMonth->month)
            ->whereYear('expense_date', $lastMonth->year)
            ->sum('amount');

        $currentMonthExpense = Expense::where('account_id', $userId)
            ->whereMonth('expense_date', $currentMonth->month)
            ->whereYear('expense_date', $currentMonth->year)
            ->sum('amount');

        $percentChange = $lastMonthExpense > 0
            ? number_format((($currentMonthExpense - $lastMonthExpense) / $lastMonthExpense) * 100, 2)
            : '100';

        return [
            Stat::make('Last Month Expense', money_idr($lastMonthExpense))
                ->description('Income earned last month')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('warning'),

            Stat::make('This Month Expense', money_idr($currentMonthExpense))
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
