<?php

namespace App\Filament\Resources\Finances;

use Filament\Forms;
use Filament\Tables;
use App\Models\Account;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\CurrentSalary;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Finances\CurrentSalaryResource\Pages;
use App\Filament\Resources\Finances\CurrentSalaryResource\RelationManagers;

class CurrentSalaryResource extends Resource
{
    protected static ?string $model = CurrentSalary::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'Finances';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('user_id')->default(Auth::id()),

                TextInput::make('income_source')
                    ->label('Income Source')
                    ->placeholder('e.g. Company name, Freelance')
                    ->maxLength(255)
                    ->columnSpan(2),

                Select::make('account_id')
                    ->options(Account::where('user_id', Auth::id())->pluck('name', 'id'))
                    ->label('Account')
                    ->required()
                    ->placeholder('Select account'),

                TextInput::make('amount')
                    ->label('Salary Amount')
                    ->placeholder('Enter salary amount')
                    ->required()
                    ->numeric()
                    ->rules(['numeric', 'min:0'])
                    ->prefix('Rp.')
                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2),

                DatePicker::make('effective_date')
                    ->label('Effective Date'),

                Select::make('frequency')
                    ->label('Frequency')
                    ->placeholder('Select frequency')
                    ->default('monthly')
                    ->options([
                        'daily' => 'Daily',
                        'weekly' => 'Weekly',
                        'monthly' => 'Monthly',
                        'yearly' => 'Yearly',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('account.name')->label('Account'),
                TextColumn::make('amount')->money('IDR', true)->label('Amount')->sortable(),
                TextColumn::make('income_source')->label('Source')->searchable(),
                TextColumn::make('effective_date')->date()->label('Date'),
                ToggleColumn::make('is_active'),
            ])
            ->filters([
                //
            ])
            ->defaultSort('effective_date', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-exclamation-triangle')
            ->emptyStateDescription('Once you write your first ' . static::getModelLabel() . ', it will appear here.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCurrentSalaries::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }
}
