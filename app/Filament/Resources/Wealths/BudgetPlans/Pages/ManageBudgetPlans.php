<?php

namespace App\Filament\Resources\Wealths\BudgetPlans\Pages;

use App\Filament\Resources\Wealths\BudgetPlans\BudgetPlanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageBudgetPlans extends ManageRecords
{
    protected static string $resource = BudgetPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
