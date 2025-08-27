<?php

namespace App\Filament\Resources\Wealths\Savings\Pages;

use App\Filament\Resources\Wealths\Savings\SavingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSavings extends ManageRecords
{
    protected static string $resource = SavingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
