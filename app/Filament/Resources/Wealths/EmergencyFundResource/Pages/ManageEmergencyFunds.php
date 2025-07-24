<?php

namespace App\Filament\Resources\Wealths\EmergencyFundResource\Pages;

use App\Filament\Resources\Wealths\EmergencyFundResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageEmergencyFunds extends ManageRecords
{
    protected static string $resource = EmergencyFundResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
