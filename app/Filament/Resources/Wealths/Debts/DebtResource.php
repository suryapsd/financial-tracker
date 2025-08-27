<?php

namespace App\Filament\Resources\Wealths\Debts;

use BackedEnum;
use App\Models\Debt;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\Wealths\Debts\Pages\EditDebt;
use App\Filament\Resources\Wealths\Debts\Pages\ListDebts;
use App\Filament\Resources\Wealths\Debts\Pages\CreateDebt;
use App\Filament\Resources\Wealths\Debts\Schemas\DebtForm;
use App\Filament\Resources\Wealths\Debts\Tables\DebtsTable;
use App\Filament\Resources\Wealths\Debts\RelationManagers\SavingsRelationManager;

class DebtResource extends Resource
{
    protected static ?string $model = Debt::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationCircle;
    protected static string | \UnitEnum | null $navigationGroup = 'Wealths';

    public static function form(Schema $schema): Schema
    {
        return DebtForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DebtsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            SavingsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDebts::route('/'),
            'create' => CreateDebt::route('/create'),
            'edit' => EditDebt::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }
}
