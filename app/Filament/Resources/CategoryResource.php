<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\CategoryType;
use App\Helpers\EnumHelper;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CategoryResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Filament\Resources\CategoryResource\Widgets\CategoryStats;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Category Name')
                    ->required(),
                Select::make('type')
                    ->label('Category Type')
                    ->required()
                    ->options(EnumHelper::getEnum('categories', 'type'))
                    ->searchable(),
                Textarea::make('description')
                    ->columnSpan(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Category Name')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn($state) => CategoryType::tryFrom($state)?->getColor())
                    ->icon(fn($state) => CategoryType::tryFrom($state)?->getIcon())
                    ->formatStateUsing(fn($state) => CategoryType::tryFrom($state)?->getLabel()),
                TextColumn::make('description')
                    ->searchable()
                    ->wrap(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(CategoryType::class)
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
            'index' => Pages\ManageCategories::route('/'),
        ];
    }
}
