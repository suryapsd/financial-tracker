<?php

namespace App\Filament\Resources\Wealths\EmergencyFunds\RelationManagers;

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
use Filament\Resources\RelationManagers\RelationManager;

class SavingsRelationManager extends RelationManager
{
    protected static string $relationship = 'savings';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')->default(Auth::id()),
                Hidden::make('type')->default('emergency'),

                DatePicker::make('saved_date')
                    ->label('Saved Date')
                    ->required()
                    ->native(false)
                    ->default(Carbon::today()),

                TextInput::make('amount')
                    ->label('Amount')
                    ->required()
                    ->numeric()
                    ->rules(['numeric', 'min:0'])
                    ->placeholder('Enter saved amount')
                    ->prefix('Rp.')
                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2),

                Textarea::make('description')
                    ->label('Description')
                    ->placeholder('Optional notes')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('emergencyFund')
            ->columns([
                TextColumn::make('saved_date')->label('Saved Date')->date(),
                TextColumn::make('amount')->label('Amount')->money('IDR', true)
                    ->summarize([
                        Sum::make()
                            ->money('IDR', true),
                    ]),
                TextColumn::make('description')->label('Notes')->limit(40),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->after(function (CreateAction $action, $record) {
                        // Update actual_amount setelah saving berhasil dibuat
                        $emergencyFund = $this->getOwnerRecord();
                        $emergencyFund->update([
                            'current_amount' => $emergencyFund->savings()->sum('amount'),
                        ]);
                    })
                    ->visible(function () {
                        $emergencyFund = $this->getOwnerRecord();

                        $totalSaving = $emergencyFund->savings()->sum('amount');
                        $targetAmount = $emergencyFund->target_amount ?? 0;

                        return $totalSaving < $targetAmount;
                    }),
                AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->after(function (EditAction $action, $record) {
                        // Update actual_amount setelah saving berhasil dibuat
                        $emergencyFund = $this->getOwnerRecord();
                        $emergencyFund->update([
                            'current_amount' => $emergencyFund->savings()->sum('amount'),
                        ]);
                    }),
                DissociateAction::make(),
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
