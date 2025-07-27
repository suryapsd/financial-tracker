<?php

namespace App\Filament\Resources\Wealths\EmergencyFundResource\RelationManagers;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class SavingsRelationManager extends RelationManager
{
    protected static string $relationship = 'savings';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
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
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('IDR', true),
                    ]),
                TextColumn::make('description')->label('Notes')->limit(40),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function (Tables\Actions\CreateAction $action, $record) {
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
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function (Tables\Actions\EditAction $action, $record) {
                        // Update actual_amount setelah saving berhasil dibuat
                        $emergencyFund = $this->getOwnerRecord();
                        $emergencyFund->update([
                            'current_amount' => $emergencyFund->savings()->sum('amount'),
                        ]);
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
