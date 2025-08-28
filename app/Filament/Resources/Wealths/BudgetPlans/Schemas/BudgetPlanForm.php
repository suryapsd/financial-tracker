<?php

namespace App\Filament\Resources\Wealths\BudgetPlans\Schemas;

use Carbon\Carbon;
use App\Models\Category;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;

class BudgetPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')->default(Auth::id()),

                TextInput::make('title')
                    ->label('Plan name')
                    ->placeholder('Enter the plan name')
                    ->required()
                    ->columnSpanFull(),

                Select::make('category_id')
                    ->label('Category')
                    ->options(Category::where('type', 'plan')->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->placeholder('Select a category'),

                TextInput::make('planned_amount')
                    ->label('Planned Amount')
                    ->placeholder('e.g. 1,500,000')
                    ->prefix('Rp.')
                    ->required()
                    ->rules(['numeric', 'min:0'])
                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2),

                TextInput::make('actual_amount')
                    ->label('Actual Amount')
                    ->prefix('Rp.')
                    ->disabled()
                    ->default(0)
                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2),

                DatePicker::make('target_date')
                    ->label('Target Date')
                    ->placeholder('e.g. 2026-12-31')
                    ->native(false)
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d')
                    ->default(Carbon::today())
                    ->closeOnDateSelection(),

                Textarea::make('description')
                    ->label('Description')
                    ->placeholder('e.g. Saving up for a down payment on a home')
                    ->columnSpanFull(),
            ]);
    }
}
