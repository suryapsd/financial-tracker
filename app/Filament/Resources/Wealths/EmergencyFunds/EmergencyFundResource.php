<?php

namespace App\Filament\Resources\Wealths\EmergencyFunds;

use BackedEnum;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use App\Models\EmergencyFund;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\Wealths\EmergencyFunds\Pages\EditEmergencyFund;
use App\Filament\Resources\Wealths\EmergencyFunds\Pages\ListEmergencyFunds;
use App\Filament\Resources\Wealths\EmergencyFunds\Pages\CreateEmergencyFund;
use App\Filament\Resources\Wealths\EmergencyFunds\Schemas\EmergencyFundForm;
use App\Filament\Resources\Wealths\EmergencyFunds\Tables\EmergencyFundsTable;
use App\Filament\Resources\Wealths\EmergencyFunds\RelationManagers\SavingsRelationManager;

class EmergencyFundResource extends Resource
{
    protected static ?string $model = EmergencyFund::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;
    protected static string | \UnitEnum | null $navigationGroup = 'Wealths';

    public static function form(Schema $schema): Schema
    {
        return EmergencyFundForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmergencyFundsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            SavingsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmergencyFunds::route('/'),
            'create' => CreateEmergencyFund::route('/create'),
            'edit' => EditEmergencyFund::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }
}
