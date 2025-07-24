<?php

namespace App\Filament\Resources\Wealths;

use Filament\Forms;
use App\Models\Debt;
use Filament\Tables;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Wealths\DebtResource\Pages;
use App\Filament\Resources\Wealths\DebtResource\RelationManagers;

class DebtResource extends Resource
{
    protected static ?string $model = Debt::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-circle';
    protected static ?string $navigationGroup = 'Wealths';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('user_id')->default(Auth::id()),

                TextInput::make('creditor')
                    ->required()
                    ->label('Creditor')
                    ->placeholder('e.g. Bank XYZ, Friend, etc.')
                    ->columnSpanFull(),

                Select::make('category_id')
                    ->label('Category')
                    ->options(Category::where('type', 'debt')->pluck('name', 'id'))
                    ->searchable()
                    ->placeholder('Select a category'),

                TextInput::make('amount')
                    ->required()
                    ->label('Debt Amount')
                    ->placeholder('Enter total debt amount')
                    ->prefix('Rp.')
                    ->numeric()
                    ->rules(['numeric', 'min:0'])
                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2),

                TextInput::make('monthly_payment')
                    ->label('Monthly Payment')
                    ->placeholder('Optional monthly payment')
                    ->prefix('Rp.')
                    ->numeric()
                    ->rules(['nullable', 'numeric', 'min:0'])
                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2),

                DatePicker::make('due_date')
                    ->required()
                    ->label('Due Date'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('creditor')->label('Creditor')->searchable(),
                TextColumn::make('category.name')->label('Category'),
                TextColumn::make('amount')->money('IDR', true)->label('Amount'),
                TextColumn::make('monthly_payment')->money('IDR', true)->label('Monthly'),
                TextColumn::make('due_date')->date()->label('Due Date'),
            ])
            ->filters([
                //
            ])
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
            'index' => Pages\ManageDebts::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }
}
