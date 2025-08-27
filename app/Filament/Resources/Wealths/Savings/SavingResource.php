<?php

namespace App\Filament\Resources\Wealths\Savings;

use BackedEnum;
use App\Models\Saving;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use App\Models\FinancialGoal;
use Illuminate\Support\Carbon;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\Wealths\Savings\Pages\ManageSavings;

class SavingResource extends Resource
{
    protected static ?string $model = Saving::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCloud;
    protected static string | \UnitEnum | null $navigationGroup = 'Wealths';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')->default(Auth::id()),

                Select::make('goal_id')
                    ->label('Financial Goal')
                    ->options(FinancialGoal::all()->pluck('title', 'id'))
                    ->required()
                    ->searchable()
                    ->placeholder('Select a financial goal'),

                TextInput::make('title')
                    ->label('Saving Title')
                    ->placeholder('e.g. Monthly deposit')
                    ->required()
                    ->maxLength(255),

                TextInput::make('amount')
                    ->label('Amount')
                    ->required()
                    ->numeric()
                    ->rules(['numeric', 'min:0'])
                    ->placeholder('Enter saved amount')
                    ->prefix('Rp.')
                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2),

                DatePicker::make('saved_date')
                    ->label('Saved Date')
                    ->required()
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d')
                    ->default(Carbon::today())
                    ->closeOnDateSelection(),

                Textarea::make('description')
                    ->label('Description')
                    ->placeholder('Optional notes')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('goal.title')->label('Goal'),
                TextColumn::make('title')->label('Saving Title')->searchable(),
                TextColumn::make('amount')->label('Amount')->money('IDR', true),
                TextColumn::make('saved_date')->label('Saved Date')->date(),
                TextColumn::make('description')->label('Notes')->limit(40),
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
            'index' => ManageSavings::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }
}
