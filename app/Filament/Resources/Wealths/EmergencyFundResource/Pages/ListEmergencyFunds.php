<?php

namespace App\Filament\Resources\Wealths\EmergencyFundResource\Pages;

use App\Filament\Resources\Wealths\EmergencyFundResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmergencyFunds extends ListRecords
{
    protected static string $resource = EmergencyFundResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
