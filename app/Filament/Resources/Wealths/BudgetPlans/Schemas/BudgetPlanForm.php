<?php

namespace App\Filament\Resources\Wealths\BudgetPlans\Schemas;

use App\Models\Category;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class BudgetPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')->default(Auth::id()),

                Select::make('category_id')
                    ->label('Category')
                    ->options(Category::where('type', 'emergency')->pluck('name', 'id'))
                    ->searchable()
                    ->placeholder('Select a category'),

                TextInput::make('title')
                    ->label('Emergency Title')
                    ->placeholder('Enter emergency title')
                    ->required(),

                TextInput::make('target_amount')
                    ->label('Target Amount')
                    ->placeholder('Enter target amount')
                    ->required()
                    ->numeric()
                    ->rules(['numeric', 'min:0'])
                    ->prefix('Rp.')
                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2),

                TextInput::make('current_amount')
                    ->label('Current Amount')
                    ->disabled()
                    ->default(0)
                    ->prefix('Rp.')
                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2),

                Textarea::make('description')
                    ->label('Description')
                    ->placeholder('Optional notes or goals')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
