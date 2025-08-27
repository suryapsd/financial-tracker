<?php

namespace App\Filament\Resources\Wealths\EmergencyFunds\Pages;

use App\Filament\Resources\Wealths\EmergencyFunds\EmergencyFundResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEmergencyFund extends EditRecord
{
    protected static string $resource = EmergencyFundResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
