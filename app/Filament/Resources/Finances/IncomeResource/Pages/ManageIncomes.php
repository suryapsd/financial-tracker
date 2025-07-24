<?php

namespace App\Filament\Resources\Finances\IncomeResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\Finances\IncomeResource;
use App\Filament\Resources\Finances\IncomeResource\Widgets\IncomeOverview;

class ManageIncomes extends ManageRecords
{
    protected static string $resource = IncomeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            IncomeOverview::class,
        ];
    }
}
