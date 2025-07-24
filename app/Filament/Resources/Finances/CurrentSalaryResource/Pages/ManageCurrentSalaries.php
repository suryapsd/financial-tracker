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
            Actions\CreateAction::make(),
        ];
    }

    public function toggleStatus($id)
    {
        $record = \App\Models\CurrentSalary::findOrFail($id);

        $record->update([
            'is_active' => !$record->is_active,
        ]);

        Notification::make()
            ->title('Status berhasil diperbarui.')
            ->success()
            ->send();
    }
}
