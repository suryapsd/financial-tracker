<?php

namespace App\Filament\Widgets;

use App\Models\FinancialReport;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class FinanceChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'financeChart';
    protected static ?int $sort = 3;
    // protected int|string|array $columnSpan = '1';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Pergerakan Kekayaan Bersih';
    protected static ?string $subheading = 'Lihat tren kenaikan atau penurunan kekayaan bersih Anda setiap bulan.';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */

    protected function getOptions(): array
    {
        $user = Auth::user();
        $year = request('year') ?? now()->year;

        $reports = FinancialReport::where('user_id', $user->id)
            ->where('year', $year)
            ->orderBy('month')
            ->get();
        // for ($month = 1; $month <= 12; $month++) {
        //     $user->checkFinancialReports($month, $year);
        // }
        $months = [];
        $incomes = [];
        $expenses = [];

        foreach ($reports as $report) {
            $months[] = date('M', mktime(0, 0, 0, $report->month, 1)); // Nama bulan singkat (Jan, Feb, dst.)
            $incomes[] = (float) $report->total_income;
            $expenses[] = (float) $report->total_expense;
        }

        return [
            'chart' => [
                'type' => 'line',
                'height' => 350,
                'toolbar' => [
                    'show' => false,
                ],
            ],

            'legend' => [
                'show' => false,
            ],
            'series' => [
                [
                    'name' => 'Pemasukan',
                    'data' => $incomes,
                ],
                [
                    'name' => 'Pengeluaran',
                    'data' => $expenses,
                ],
            ],
            'xaxis' => [
                'categories' => $months,
            ],
            'colors' => ['#00E396', '#FF4560'],
        ];
    }
}
