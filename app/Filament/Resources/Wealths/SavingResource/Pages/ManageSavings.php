<?php

namespace App\Filament\Resources\Wealths\SavingResource\Pages;

use App\Filament\Resources\Wealths\SavingResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageSavings extends ManageRecords
{
    protected static string $resource = SavingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
