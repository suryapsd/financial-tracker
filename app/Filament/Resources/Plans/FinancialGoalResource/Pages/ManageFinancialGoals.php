<?php

namespace App\Filament\Resources\Plans\FinancialGoalResource\Pages;

use App\Filament\Resources\Plans\FinancialGoalResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageFinancialGoals extends ManageRecords
{
    protected static string $resource = FinancialGoalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
