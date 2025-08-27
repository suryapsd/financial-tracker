<?php

namespace App\Filament\Resources\Wealths\Debts\Pages;

use App\Filament\Resources\Wealths\Debts\DebtResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDebts extends ListRecords
{
    protected static string $resource = DebtResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
