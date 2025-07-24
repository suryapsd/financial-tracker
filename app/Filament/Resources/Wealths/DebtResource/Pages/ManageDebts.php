<?php

namespace App\Filament\Resources\Wealths\DebtResource\Pages;

use App\Filament\Resources\Wealths\DebtResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDebts extends ManageRecords
{
    protected static string $resource = DebtResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
