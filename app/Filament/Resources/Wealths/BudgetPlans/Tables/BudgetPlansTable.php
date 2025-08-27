<?php

namespace App\Filament\Resources\Wealths\BudgetPlans\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Summarizers\Sum;

class BudgetPlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->color(fn($record) => $record->category?->color),
                TextColumn::make('target_amount')->label('Target')->money('IDR', true)
                    ->summarize([
                        Sum::make()
                            ->money('IDR', true),
                    ]),
                TextColumn::make('current_amount')->label('Current')->money('IDR', true)->default(0)
                    ->summarize([
                        Sum::make()
                            ->money('IDR', true),
                    ]),
                TextColumn::make('description')->label('Description')->limit(50),
                TextColumn::make('created_at')->date()->label('Created At'),
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
