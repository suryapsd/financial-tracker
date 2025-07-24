<?php

namespace App\Filament\Resources\Plans\DreamResource\Pages;

use App\Filament\Resources\Plans\DreamResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDreams extends ManageRecords
{
    protected static string $resource = DreamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
