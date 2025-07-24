<?php

namespace App\Filament\Resources\Wealths;

use Filament\Forms;
use Filament\Tables;
use App\Models\Asset;
use App\Models\Account;
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
use App\Filament\Resources\Wealths\AssetResource\Pages;
use App\Filament\Resources\Wealths\AssetResource\RelationManagers;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationGroup = 'Wealths';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('user_id')->default(Auth::id()),

                Select::make('category_id')
                    ->label('Category')
                    ->options(Category::where('type', 'asset')->pluck('name', 'id'))
                    ->searchable()
                    ->placeholder('Select an category'),

                Select::make('account_id')
                    ->options(Account::all()->pluck('name', 'id'))
                    ->label('Account')
                    ->searchable()
                    ->placeholder('Select an account'),

                TextInput::make('name')
                    ->required()
                    ->label('Asset Name')
                    ->placeholder('Enter asset name')
                    ->columnSpanFull(),

                TextInput::make('category')
                    ->required()
                    ->label('Category')
                    ->placeholder('Enter asset category'),

                TextInput::make('institution')
                    ->label('Institution')
                    ->placeholder('Enter institution (optional)')
                    ->maxLength(255),

                TextInput::make('value')
                    ->required()
                    ->label('Asset Value')
                    ->placeholder('Enter the asset value')
                    ->prefix('Rp.')
                    ->numeric()
                    ->rules(['numeric', 'min:0'])
                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2),

                DatePicker::make('acquired_date')
                    ->label('Acquired Date'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Asset Name')->searchable(),
                TextColumn::make('category')->label('Category'),
                TextColumn::make('institution')->label('Institution'),
                TextColumn::make('value')->money('IDR', true)->label('Value'),
                TextColumn::make('acquired_date')->date()->label('Date'),
                TextColumn::make('account.name')->label('Account'),
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
            'index' => Pages\ManageAssets::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }
}
