<?php

namespace App\Filament\Resources\Wealths\Debts\RelationManagers;

use Carbon\Carbon;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Hidden;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Actions\DissociateBulkAction;
use Filament\Tables\Columns\Summarizers\Sum;
use App\Filament\Resources\Wealths\SavingResource;
use Filament\Resources\RelationManagers\RelationManager;

class SavingsRelationManager extends RelationManager
{
    protected static string $relationship = 'savings';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                ...SavingResource::formSaving('debt'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('debt')
            ->columns([
                ...SavingResource::columnSaving()
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->after(function (CreateAction $action, $record) {
                        // Update actual_amount setelah saving berhasil dibuat
                        $debt = $this->getOwnerRecord();
                        $debt->update([
                            'already_paid' => $debt->savings()->sum('amount'),
                        ]);
                    })
                    ->visible(function () {
                        $debt = $this->getOwnerRecord();

                        $totalSaving = $debt->savings()->sum('amount');
                        $targetAmount = $debt->amount ?? 0;

                        return $totalSaving < $targetAmount;
                    }),
                // AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->after(function (EditAction $action, $record) {
                        // Update actual_amount setelah saving berhasil dibuat
                        $debt = $this->getOwnerRecord();
                        $debt->update([
                            'already_paid' => $debt->savings()->sum('amount'),
                        ]);
                    }),
                // DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
