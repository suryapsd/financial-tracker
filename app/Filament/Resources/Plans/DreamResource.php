<?php

namespace App\Filament\Resources\Plans;

use Filament\Forms;
use Filament\Tables;
use App\Models\Dream;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\CreateAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Plans\DreamResource\Pages;
use App\Filament\Resources\Plans\DreamResource\RelationManagers;

class DreamResource extends Resource
{
    protected static ?string $model = Dream::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationGroup = 'Plans';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('user_id')->default(Auth::id()),

                TextInput::make('title')
                    ->label('Title')
                    ->placeholder('e.g. Buy a House')
                    ->required()
                    ->columnSpanFull(),

                Select::make('category_id')
                    ->label('Category')
                    ->options(Category::where('type', 'dream')->pluck('name', 'id'))
                    ->searchable()
                    ->placeholder('Select a category'),

                TextInput::make('target_amount')
                    ->required()
                    ->label('Target Amount')
                    ->placeholder('e.g. 100000000')
                    ->numeric()
                    ->rules(['numeric', 'min:0'])
                    ->prefix('Rp.')
                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2),

                TextInput::make('saved_amount')
                    ->required()
                    ->label('Saved Amount')
                    ->placeholder('e.g. 25000000')
                    ->numeric()
                    ->rules(['numeric', 'min:0'])
                    ->prefix('Rp.')
                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2),

                DatePicker::make('target_date')
                    ->label('Target Date')
                    ->placeholder('e.g. 2026-12-31')
                    ->closeOnDateSelection(),

                Textarea::make('description')
                    ->label('Description')
                    ->placeholder('e.g. Save for a dream house in Bali')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Title')->searchable(),
                TextColumn::make('category.name')->label('Category'),
                TextColumn::make('target_amount')->label('Target')->money('IDR'),
                TextColumn::make('saved_amount')->label('Saved')->money('IDR'),
                TextColumn::make('target_date')->label('Target Date')->date('d M Y'),
                TextColumn::make('created_at')->label('Created')->date('d M Y'),
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
            'index' => Pages\ManageDreams::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }
}
