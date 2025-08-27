<?php

namespace App\Filament\Resources\Finances\CurrentSalaries\Pages;

use Filament\Actions\CreateAction;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\Finances\CurrentSalaries\CurrentSalaryResource;

class ManageCurrentSalaries extends ManageRecords
{
    protected static string $resource = CurrentSalaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modalWidth('2xl'),
        ];
    }
}
