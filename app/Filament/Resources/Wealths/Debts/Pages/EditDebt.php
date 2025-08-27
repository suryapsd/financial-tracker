<?php

namespace App\Filament\Resources\Wealths\Debts\Pages;

use App\Filament\Resources\Wealths\Debts\DebtResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDebt extends EditRecord
{
    protected static string $resource = DebtResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
