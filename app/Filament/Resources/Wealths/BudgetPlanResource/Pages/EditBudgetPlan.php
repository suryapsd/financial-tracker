<?php

namespace App\Filament\Resources\Wealths\BudgetPlanResource\Pages;

use App\Filament\Resources\Wealths\BudgetPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBudgetPlan extends EditRecord
{
    protected static string $resource = BudgetPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
