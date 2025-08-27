<?php

namespace App\Filament\Resources\Wealths\Debts\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Summarizers\Sum;

class DebtsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('creditor')->label('Creditor')->searchable(),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->color(fn($record) => $record->category?->color),
                TextColumn::make('amount')->money('IDR', true)->label('Amount')
                    ->summarize([
                        Sum::make()
                            ->money('IDR', true),
                    ]),
                TextColumn::make('already_paid')->money('IDR', true)->label('Already Paid')->default(0)
                    ->summarize([
                        Sum::make()
                            ->money('IDR', true),
                    ]),
                TextColumn::make('monthly_payment')->money('IDR', true)->label('Monthly'),
                TextColumn::make('due_date')->date()->label('Due Date'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
