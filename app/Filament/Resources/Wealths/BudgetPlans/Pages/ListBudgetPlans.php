<?php

namespace App\Filament\Resources\Wealths\BudgetPlans\Pages;

use App\Filament\Resources\Wealths\BudgetPlans\BudgetPlanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBudgetPlans extends ListRecords
{
    protected static string $resource = BudgetPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
