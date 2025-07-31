<?php

namespace App\Filament\Resources\Finances;

use Filament\Forms;
use Filament\Tables;
use App\Models\Income;
use App\Models\Account;
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
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Finances\IncomeResource\Pages;
use App\Filament\Resources\Finances\IncomeResource\RelationManagers;

class IncomeResource extends Resource
{
    protected static ?string $model = Income::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-left-end-on-rectangle';
    protected static ?string $navigationGroup = 'Finances';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                    ->numeric()
                    ->rules(['numeric', 'min:0'])
                    ->prefix('Rp.')
                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2),

                DatePicker::make('received_at')
                    ->required()
                    ->label('Received Date')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d')
                    ->default(\Carbon\Carbon::today())
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
                        Tables\Columns\Summarizers\Sum::make()
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
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateFormDataUsing(function (array $data, $record): array {
                        // Simpan amount lama untuk perhitungan selisih
                        $record->old_amount = $record->amount;
                        return $data;
                    })
                    ->after(function (Tables\Actions\EditAction $action, $record) {
                        $account = $record->account;

                        if ($account && isset($record->old_amount)) {
                            $difference = $record->amount - $record->old_amount;

                            $account->update([
                                'balance' => $account->balance + $difference,
                            ]);
                        }
                    }),
                Tables\Actions\DeleteAction::make()
                    ->before(function ($record) {
                        $account = $record->account;

                        if ($account) {
                            $account->update([
                                'balance' => $account->balance - $record->amount,
                            ]);
                        }
                    }),
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
            'index' => Pages\ManageIncomes::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }
}
