<?php

namespace App\Filament\Resources\Finances\CurrentSalaries;

use BackedEnum;
use Carbon\Carbon;
use App\Models\Account;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use App\Models\CurrentSalary;
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
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\ToggleColumn;
use App\Filament\Resources\Finances\CurrentSalaries\Pages\ManageCurrentSalaries;

class CurrentSalaryResource extends Resource
{
    protected static ?string $model = CurrentSalary::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;
    protected static string | \UnitEnum | null $navigationGroup = 'Finances';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')->default(Auth::id()),

                TextInput::make('income_source')
                    ->label('Income Source')
                    ->placeholder('e.g. Company name, Freelance')
                    ->maxLength(255)
                    ->columnSpanFull(),

                Select::make('account_id')
                    ->options(Account::where('user_id', Auth::id())->pluck('name', 'id'))
                    ->label('Account')
                    ->required()
                    ->placeholder('Select account'),

                TextInput::make('amount')
                    ->label('Salary Amount')
                    ->placeholder('Enter salary amount')
                    ->required()
                    ->rules(['numeric', 'min:0'])
                    ->prefix('Rp.')
                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2),

                DatePicker::make('effective_date')
                    ->label('Effective Date')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d')
                    ->default(Carbon::today())
                    ->closeOnDateSelection(),

                Select::make('frequency')
                    ->label('Frequency')
                    ->placeholder('Select frequency')
                    ->default('monthly')
                    ->options([
                        'daily' => 'Daily',
                        'weekly' => 'Weekly',
                        'monthly' => 'Monthly',
                        'yearly' => 'Yearly',
                    ]),

                Checkbox::make('is_auto_create_income')
                    ->label('Aktifkan otomatisasi pencatatan income sesuai frekuensi')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('account.name')->label('Account'),
                TextColumn::make('amount')->money('IDR', true)->label('Amount')->sortable(),
                TextColumn::make('income_source')->label('Source')->searchable(),
                TextColumn::make('effective_date')->date()->label('Date'),
                ToggleColumn::make('is_active'),
                ToggleColumn::make('is_auto_create_income'),
            ])
            ->filters([
                //
            ])
            ->defaultSort('effective_date', 'desc')
            ->recordActions([
                EditAction::make()
                    ->modalWidth('2xl'),
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
            'index' => ManageCurrentSalaries::route('/'),
        ];
    }
}
