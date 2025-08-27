<?php

namespace App\Filament\Resources\Finances\Incomes\Pages;

use App\Livewire\IncomeOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\Finances\Incomes\IncomeResource;

class ManageIncomes extends ManageRecords
{
    protected static string $resource = IncomeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->after(function (CreateAction $action, $record) {
                    $account = $record->account;

                    if ($account) {
                        $account->update([
                            'balance' => $account->balance + $record->amount,
                        ]);
                    }
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            IncomeOverview::class,
        ];
    }
}
