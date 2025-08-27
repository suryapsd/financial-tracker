<?php

namespace App\Filament\Resources\Finances\Accounts;

use BackedEnum;
use App\Models\Account;
use App\Enums\AccountType;
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
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use App\Filament\Resources\Finances\Accounts\Pages\ManageAccounts;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;
    protected static string | \UnitEnum | null $navigationGroup = 'Finances';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')->default(Auth::id()),

                TextInput::make('name')
                    ->label('Account Name')
                    ->placeholder('e.g. BCA, Dana, Gopay')
                    ->required(),

                TextInput::make('account_number')
                    ->label('Account Number')
                    ->placeholder('Optional')
                    ->maxLength(50),

                Select::make('account_type')
                    ->label('Account Type')
                    ->options(AccountType::class)
                    ->placeholder('Select account type')
                    ->required(),

                TextInput::make('balance')
                    ->label('Balance')
                    ->prefix('Rp.')
                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2),

                Checkbox::make('is_primary')
                    ->label('Primary Account'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('name')
                    ->label('Accounts')
                    ->formatStateUsing(function ($record) {
                        return "{$record->name}<br><span class='text-sm text-gray-500'>{$record->account_number}</span>";
                    })
                    ->html()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('account_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn($state) => AccountType::tryFrom($state)?->getColor())
                    ->icon(fn($state) => AccountType::tryFrom($state)?->getIcon())
                    ->formatStateUsing(fn($state) => AccountType::tryFrom($state)?->getLabel()),
                TextColumn::make('balance')
                    ->money('IDR')
                    ->label('Saldo')
                    ->summarize([
                        Sum::make()
                            ->money('IDR', true),
                    ]),
                ToggleColumn::make('is_primary')
                    ->label('Primary')
                    ->afterStateUpdated(function ($record, $state) {
                        if ($state) {
                            $record->newQuery()
                                ->where('id', '!=', $record->id)
                                ->update(['is_primary' => false]);
                        }
                    }),
                ToggleColumn::make('is_active')
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->modalWidth('2xl')
                    ->mutateDataUsing(function (array $data): array {
                        $data['user_id'] = Auth::id();
                        return $data;
                    }),
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
            'index' => ManageAccounts::route('/'),
        ];
    }
}
