<?php

namespace App\Filament\Resources\Wealths\EmergencyFunds\Pages;

use App\Filament\Resources\Wealths\EmergencyFunds\EmergencyFundResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEmergencyFunds extends ListRecords
{
    protected static string $resource = EmergencyFundResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
