<?php

namespace App\Filament\Resources\Finances\AccountResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\Finances\AccountResource;

class ManageAccounts extends ManageRecords
{
    protected static string $resource = AccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth('2xl')
                ->mutateFormDataUsing(function (array $data): array {
                    $data['user_id'] = Auth::id();
                    return $data;
                }),
        ];
    }
}
