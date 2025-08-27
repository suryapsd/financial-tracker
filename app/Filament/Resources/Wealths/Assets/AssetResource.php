<?php

namespace App\Filament\Resources\Wealths\Assets;

use BackedEnum;
use Carbon\Carbon;
use App\Models\Asset;
use App\Models\Account;
use App\Models\Category;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;
use App\Filament\Resources\Wealths\Assets\Pages\ManageAssets;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingLibrary;
    protected static string | \UnitEnum | null $navigationGroup = 'Wealths';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')->default(Auth::id()),

                Select::make('category_id')
                    ->label('Category')
                    ->options(Category::where('type', 'asset')->pluck('name', 'id'))
                    ->searchable()
                    ->placeholder('Select an category'),

                Select::make('account_id')
                    ->options(Account::where('user_id', Auth::id())->where('is_active', 1)->pluck('name', 'id'))
                    ->label('Account')
                    ->searchable()
                    ->placeholder('Select an account'),

                TextInput::make('name')
                    ->required()
                    ->label('Asset Name')
                    ->placeholder('Enter asset name'),

                TextInput::make('institution')
                    ->label('Institution')
                    ->placeholder('Enter institution (optional)')
                    ->maxLength(255),

                TextInput::make('value')
                    ->required()
                    ->label('Asset Value')
                    ->placeholder('Enter the asset value')
                    ->prefix('Rp.')
                    ->rules(['numeric', 'min:0'])
                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2),

                DatePicker::make('acquired_date')
                    ->label('Acquired Date')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d')
                    ->default(Carbon::today())
                    ->closeOnDateSelection(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Asset Name')->searchable(),
                TextColumn::make('category.name')->label('Category'),
                TextColumn::make('institution')->label('Institution'),
                TextColumn::make('value')->money('IDR', true)->label('Value')
                    ->summarize([
                        Sum::make()
                            ->money('IDR', true),
                    ]),
                TextColumn::make('acquired_date')->date()->label('Date'),
                TextColumn::make('account.name')->label('Account'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
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
            'index' => ManageAssets::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }
}
