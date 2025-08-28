<?php

namespace App\Filament\Resources\Wealths\Debts\RelationManagers;

use Carbon\Carbon;
use App\Models\Expense;
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
            ->heading('Debt Payment History')
            ->description('Track and manage your debt repayments here.')
            ->columns([
                ...SavingResource::columnSaving()
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Debt Payment')
                    ->modalHeading('Create Debt Payment History')
                    ->after(function (CreateAction $action, $record) {
                        // Update actual_amount setelah saving berhasil dibuat
                        $debt = $this->getOwnerRecord();
                        $debt->update([
                            'already_paid' => $debt->savings()->sum('amount'),
                        ]);
                        Expense::create([
                            'user_id' => $debt->user_id,
                            'account_id' => $record->account_id,
                            'category_id' => $debt->category_id,
                            'name' => 'Debt: ' . $debt->creditor,
                            'amount' => $record->amount,
                            'expense_date' => $record->saved_date,
                            'frequency' => 'monthly',
                            'description' => $debt->category->name
                                . ' - ' . $debt->creditor
                                . ' | Payment By: #' . $record->account->name
                                . ' | Payment ID: #' . $record->id
                                . ' | Date: ' . $record->created_at->format('d M Y'),
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
                    ->modalHeading('Edit Debt Payment History')
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
