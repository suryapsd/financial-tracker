<?php

namespace App\Filament\Resources\Wealths\Assets\Pages;

use App\Filament\Resources\Wealths\Assets\AssetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageAssets extends ManageRecords
{
    protected static string $resource = AssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
