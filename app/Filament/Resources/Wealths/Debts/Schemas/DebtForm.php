<?php

namespace App\Filament\Resources\Wealths\Debts\Schemas;

use Carbon\Carbon;
use App\Models\Category;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;

class DebtForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')->default(Auth::id()),

                TextInput::make('creditor')
                    ->required()
                    ->label('Creditor')
                    ->placeholder('e.g. Bank XYZ, Friend, etc.')
                    ->columnSpanFull(),

                Select::make('category_id')
                    ->label('Category')
                    ->options(Category::where('type', 'debt')->pluck('name', 'id'))
                    ->searchable()
                    ->placeholder('Select a category'),

                TextInput::make('amount')
                    ->required()
                    ->label('Debt Amount')
                    ->placeholder('Enter total debt amount')
                    ->prefix('Rp.')
                    ->numeric()
                    ->rules(['numeric', 'min:0'])
                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2),

                TextInput::make('monthly_payment')
                    ->label('Monthly Payment')
                    ->placeholder('Optional monthly payment')
                    ->prefix('Rp.')
                    ->numeric()
                    ->rules(['nullable', 'numeric', 'min:0'])
                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2),

                DatePicker::make('due_date')
                    ->label('Due Date')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d')
                    ->default(Carbon::today())
                    ->closeOnDateSelection(),
            ]);
    }
}
