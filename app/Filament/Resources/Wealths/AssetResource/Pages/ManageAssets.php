<?php

namespace App\Filament\Resources\Wealths\AssetResource\Pages;

use App\Filament\Resources\Wealths\AssetResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageAssets extends ManageRecords
{
    protected static string $resource = AssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
