<?php

namespace App\Filament\Resources\Finances\CurrentSalaryResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\Finances\CurrentSalaryResource;

class ManageCurrentSalaries extends ManageRecords
{
    protected static string $resource = CurrentSalaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth('2xl'),
        ];
    }
}
