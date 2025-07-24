<?php

namespace App\Filament\Resources\Finances;

use Filament\Forms;
use Filament\Tables;
use App\Models\Account;
use Filament\Forms\Form;
use App\Enums\AccountType;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Finances\AccountResource\Pages;
use App\Filament\Resources\Finances\AccountResource\RelationManagers;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Finances';
    protected static ?string $navigationLabel = 'Wallet Accounts';
    protected static ?string $modelLabel = 'wallet account';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                TextColumn::make('balance')->money('IDR')->label('Saldo'),
                ToggleColumn::make('is_primary')
                    ->label('Utama')
                    ->afterStateUpdated(function ($record, $state) {
                        if ($state) {
                            $record->newQuery()
                                ->where('id', '!=', $record->id)
                                ->update(['is_primary' => false]);
                        }
                    })
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalWidth('2xl')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = Auth::id();
                        return $data;
                    }),
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
            'index' => Pages\ManageAccounts::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }
}
