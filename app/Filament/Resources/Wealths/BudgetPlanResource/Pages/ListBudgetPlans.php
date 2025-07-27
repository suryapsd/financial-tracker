<?php

namespace App\Filament\Resources\Wealths\BudgetPlanResource\Pages;

use App\Filament\Resources\Wealths\BudgetPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBudgetPlans extends ListRecords
{
    protected static string $resource = BudgetPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
