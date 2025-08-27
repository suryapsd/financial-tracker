<?php

namespace App\Filament\Resources\Wealths\BudgetPlans\Pages;

use App\Filament\Resources\Wealths\BudgetPlans\BudgetPlanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBudgetPlan extends EditRecord
{
    protected static string $resource = BudgetPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
