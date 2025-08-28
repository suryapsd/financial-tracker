<?php

namespace App\Filament\Resources\Wealths;

use Carbon\Carbon;
use App\Models\Account;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\Summarizers\Sum;

class SavingResource
{
    public static function formSaving(string $type): array
    {
        return [
            Hidden::make('user_id')->default(Auth::id()),
            Hidden::make('type')->default($type),

            DatePicker::make('saved_date')
                ->label('Saved Date')
                ->required()
                ->native(false)
                ->default(Carbon::today()),

            Select::make('account_id')
                ->required()
                ->label('Account')
                ->options(Account::where('user_id', Auth::id())->where('is_active', 1)->pluck('name', 'id'))
                ->searchable()
                ->placeholder('Select an account'),

            TextInput::make('amount')
                ->label('Amount')
                ->required()
                ->rules(['numeric', 'min:0'])
                ->placeholder('Enter saved amount')
                ->columnSpanFull()
                ->default(function ($livewire) use ($type) {
                    if ($type == 'debt') {
                        $debt = $livewire->getOwnerRecord();
                        return $debt->monthly_payment;
                    }
                })
                ->prefix('Rp.')
                ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2),

            Textarea::make('description')
                ->label('Description')
                ->placeholder('Optional notes')
                ->rows(3)
                ->columnSpanFull(),
        ];
    }

    public static function columnSaving(): array
    {
        return [
            TextColumn::make('saved_date')->label('Date')->date(),
            TextColumn::make('account.name')->label('Account'),
            TextColumn::make('amount')->label('Amount')->money('IDR', true)
                ->summarize([
                    Sum::make()
                        ->money('IDR', true),
                ]),
            TextColumn::make('description')->label('Notes')->limit(40),
        ];
    }
}
