<?php

namespace App\Filament\Resources\Wealths\BudgetPlans;

use BackedEnum;
use App\Models\BudgetPlan;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\Wealths\BudgetPlans\Pages\EditBudgetPlan;
use App\Filament\Resources\Wealths\BudgetPlans\Pages\ListBudgetPlans;
use App\Filament\Resources\Wealths\BudgetPlans\Pages\CreateBudgetPlan;
use App\Filament\Resources\Wealths\BudgetPlans\Schemas\BudgetPlanForm;
use App\Filament\Resources\Wealths\BudgetPlans\Tables\BudgetPlansTable;
use App\Filament\Resources\Wealths\BudgetPlans\RelationManagers\SavingsRelationManager;

class BudgetPlanResource extends Resource
{
    protected static ?string $model = BudgetPlan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsVertical;
    protected static string | \UnitEnum | null $navigationGroup = 'Wealths';

    public static function form(Schema $schema): Schema
    {
        return BudgetPlanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BudgetPlansTable::configure($table);
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
            'index' => ListBudgetPlans::route('/'),
            'create' => CreateBudgetPlan::route('/create'),
            'edit' => EditBudgetPlan::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }
}
