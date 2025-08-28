<?php

namespace App\Filament\Resources\Finances\Incomes;

use BackedEnum;
use Carbon\Carbon;
use App\Models\Income;
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
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;
use App\Filament\Resources\Finances\Incomes\Pages\ManageIncomes;

class IncomeResource extends Resource
{
    protected static ?string $model = Income::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowLeftEndOnRectangle;
    protected static string | \UnitEnum | null $navigationGroup = 'Finances';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')->default(Auth::id()),

                Select::make('category_id')
                    ->label('Category')
                    ->options(Category::where('type', 'income')->pluck('name', 'id'))
                    ->searchable()
                    ->placeholder('Select an category'),

                Select::make('account_id')
                    ->options(Account::where('user_id', Auth::id())->where('is_active', 1)->pluck('name', 'id'))
                    ->required()
                    ->label('Account')
                    ->searchable()
                    ->placeholder('Select an account'),

                TextInput::make('source')
                    ->required()
                    ->label('Income Source')
                    ->placeholder('e.g. Company Name, etc.')
                    ->maxLength(255)
                    ->columnSpanFull(),

                TextInput::make('amount')
                    ->required()
                    ->label('Amount')
                    ->placeholder('Enter the amount')
                    ->rules(['numeric', 'min:0'])
                    ->prefix('Rp.')
                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2),

                DatePicker::make('received_at')
                    ->required()
                    ->label('Received Date')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d')
                    ->default(Carbon::today())
                    ->closeOnDateSelection(),

                Textarea::make('description')
                    ->columnSpanFull()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('account.name')
                    ->label('Account'),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->color(fn($record) => $record->category?->color),
                TextColumn::make('source')
                    ->label('Source')
                    ->searchable(),
                TextColumn::make('amount')
                    ->money('IDR', true)
                    ->sortable()
                    ->summarize([
                        Sum::make()
                            ->money('IDR', true),
                    ]),
                TextColumn::make('received_at')
                    ->date()
                    ->label('Received At'),
                TextColumn::make('created_at')
                    ->since()
                    ->label('Created')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                SelectFilter::make('account_id')
                    ->relationship('account', 'name')
                    ->label('Account')
                    ->preload(),
                SelectFilter::make('category_id')
                    ->relationship('category', 'name')
                    ->label('Category')
                    ->searchable()
                    ->preload(),
            ])
            ->defaultSort('received_at', 'desc')
            ->recordActions([
                EditAction::make()
                    ->mutateDataUsing(function (array $data, $record): array {
                        // Simpan amount lama untuk perhitungan selisih
                        $record->old_amount = $record->amount;
                        return $data;
                    })
                    ->after(function (EditAction $action, $record) {
                        $account = $record->account;

                        if ($account && isset($record->old_amount)) {
                            $difference = $record->amount - $record->old_amount;

                            $account->update([
                                'balance' => $account->balance + $difference,
                            ]);
                        }
                    }),
                DeleteAction::make()
                    ->before(function ($record) {
                        $account = $record->account;

                        if ($account) {
                            $account->update([
                                'balance' => $account->balance - $record->amount,
                            ]);
                        }
                    }),
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
            'index' => ManageIncomes::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }
}
