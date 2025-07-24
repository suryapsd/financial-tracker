<?php

namespace App\Filament\Resources\Plans\BudgetPlanResource\Pages;

use App\Filament\Resources\Plans\BudgetPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageBudgetPlans extends ManageRecords
{
    protected static string $resource = BudgetPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
