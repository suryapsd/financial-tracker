<?php

namespace App\Filament\Resources\Wealths\BudgetPlans\RelationManagers;

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
                ...SavingResource::formSaving('plan')
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('budgetPlan')
            ->heading('Budget Plan History')
            ->description('Plan, allocate, and monitor your monthly budget effectively.')
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
                    ->label('Budget Plan')
                    ->modalHeading('Create Budget Plan History')
                    ->after(function (CreateAction $action, $record) {
                        // Update actual_amount setelah saving berhasil dibuat
                        $budgetPlan = $this->getOwnerRecord();
                        $budgetPlan->update([
                            'actual_amount' => $budgetPlan->savings()->sum('amount'),
                        ]);
                    })
                    ->visible(function () {
                        $budgetPlan = $this->getOwnerRecord();

                        $totalSaving = $budgetPlan->savings()->sum('amount');
                        $targetAmount = $budgetPlan->planned_amount ?? 0;

                        return $totalSaving < $targetAmount;
                    }),
                // AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->modalHeading('Edit Budget Plan History')
                    ->after(function (EditAction $action, $record) {
                        // Update actual_amount setelah saving berhasil dibuat
                        $budgetPlan = $this->getOwnerRecord();
                        $budgetPlan->update([
                            'actual_amount' => $budgetPlan->savings()->sum('amount'),
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
