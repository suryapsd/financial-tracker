<?php

namespace App\Filament\Resources\Finances\Accounts\Pages;

use App\Livewire\ExpenseOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\Finances\Accounts\AccountResource;

class ManageAccounts extends ManageRecords
{
    protected static string $resource = AccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->after(function (CreateAction $action, $record) {
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
