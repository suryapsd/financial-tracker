<?php

namespace App\Filament\Resources\Wealths\EmergencyFundResource\Pages;

use App\Filament\Resources\Wealths\EmergencyFundResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmergencyFund extends EditRecord
{
    protected static string $resource = EmergencyFundResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
