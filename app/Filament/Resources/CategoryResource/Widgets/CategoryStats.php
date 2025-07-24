<?php

namespace App\Filament\Resources\CategoryResource\Widgets;

use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use App\Filament\Resources\CategoryResource\Pages\ManageCategories;

class CategoryStats extends BaseWidget
{
    use InteractsWithPageTable;

    protected static ?string $pollingInterval = null;

    protected function getTablePage(): string
    {
        return ManageCategories::class;
    }

    protected function getStats(): array
    {
        return [
            // Stat::make('Open orders', $this->getPageTableQuery()->whereIn('status', ['open', 'processing'])->count()),
            // Stat::make('Average price', number_format($this->getPageTableQuery()->avg('total_price'), 2)),
        ];
    }
}
