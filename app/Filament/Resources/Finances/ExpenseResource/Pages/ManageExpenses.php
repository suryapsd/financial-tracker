<?php

namespace App\Filament\Resources\Finances\ExpenseResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\Finances\ExpenseResource;
use App\Filament\Resources\Finances\ExpenseResource\Widgets\ExpenseOverview;

class ManageExpenses extends ManageRecords
{
    protected static string $resource = ExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->after(function (Actions\CreateAction $action, $record) {
                    $account = $record->account;

                    if ($account) {
                        $account->update([
                            'balance' => $account->balance - $record->amount,
                        ]);
                    }
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ExpenseOverview::class,
        ];
    }
}
