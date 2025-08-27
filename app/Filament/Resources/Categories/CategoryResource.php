<?php

namespace App\Filament\Resources\Categories;

use BackedEnum;
use App\Models\Category;
use Filament\Tables\Table;
use App\Enums\CategoryType;
use App\Helpers\EnumHelper;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\ColorPicker;
use App\Filament\Resources\Categories\Pages\ManageCategories;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Category Name')
                    ->required()
                    ->columnSpanFull(),
                Select::make('type')
                    ->label('Category Type')
                    ->required()
                    ->options(EnumHelper::getEnum('categories', 'type'))
                    ->searchable(),
                ColorPicker::make('color')
                    ->required()
                    ->regex('/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})\b$/'),
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
                ColorColumn::make('color'),
                TextColumn::make('description')
                    ->searchable()
                    ->wrap(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(CategoryType::class)
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn($record) => !in_array($record->id, [38, 39])),
                DeleteAction::make()
                    ->visible(fn($record) => !in_array($record->id, [38, 39])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCategories::route('/'),
        ];
    }
}
