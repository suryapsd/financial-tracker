<?php

namespace App\Filament\Resources\Wealths;

use Filament\Forms;
use Filament\Tables;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\EmergencyFund;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Wealths\EmergencyFundResource\Pages;
use App\Filament\Resources\Wealths\EmergencyFundResource\RelationManagers;

class EmergencyFundResource extends Resource
{
    protected static ?string $model = EmergencyFund::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = 'Wealths';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('user_id')->default(Auth::id()),

                Select::make('category_id')
                    ->label('Category')
                    ->options(Category::where('type', 'emergency')->pluck('name', 'id'))
                    ->searchable()
                    ->placeholder('Select a category'),

                TextInput::make('title')
                    ->label('Emergency Title')
                    ->placeholder('Enter emergency title')
                    ->required(),

                TextInput::make('target_amount')
                    ->label('Target Amount')
                    ->placeholder('Enter target amount')
                    ->required()
                    ->numeric()
                    ->rules(['numeric', 'min:0'])
                    ->prefix('Rp.')
                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2),

                TextInput::make('current_amount')
                    ->label('Current Amount')
                    ->placeholder('Enter current amount')
                    ->required()
                    ->numeric()
                    ->rules(['numeric', 'min:0'])
                    ->prefix('Rp.')
                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2),

                Textarea::make('description')
                    ->label('Description')
                    ->placeholder('Optional notes or goals')
                    ->rows(3)
                    ->columnSpan(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name')->label('Category'),
                TextColumn::make('target_amount')->label('Target')->money('IDR', true),
                TextColumn::make('current_amount')->label('Current')->money('IDR', true),
                TextColumn::make('description')->label('Description')->limit(50),
                TextColumn::make('created_at')->date()->label('Created At'),
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
            'index' => Pages\ManageEmergencyFunds::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }
}
