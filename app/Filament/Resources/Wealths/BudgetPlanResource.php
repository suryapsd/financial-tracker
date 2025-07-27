<?php

namespace App\Filament\Resources\Wealths;

use Filament\Forms;
use Filament\Tables;
use App\Models\Category;
use Filament\Forms\Form;
use App\Models\BudgetPlan;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Wealths\BudgetPlanResource\Pages;
use App\Filament\Resources\Wealths\BudgetPlanResource\RelationManagers;

class BudgetPlanResource extends Resource
{
    protected static ?string $model = BudgetPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-vertical';
    protected static ?string $navigationGroup = 'Wealths';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                    ->numeric()
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
                    ->default(\Carbon\Carbon::today())
                    ->closeOnDateSelection(),

                Textarea::make('description')
                    ->label('Description')
                    ->placeholder('e.g. Saving up for a down payment on a home')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name')
                    ->label('Category')
                    ->searchable()
                    ->color(fn($record) => $record->category?->color),
                TextColumn::make('title')->label('Plan Name')->searchable(),
                TextColumn::make('planned_amount')->label('Planned')->money('IDR', true)
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('IDR', true),
                    ]),
                TextColumn::make('actual_amount')->label('Actual')->money('IDR', true)->default(0)
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('IDR', true),
                    ]),
                TextColumn::make('target_date')->label('Target Date')->date(),
                TextColumn::make('created_at')->label('Created At')->date('d M Y'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-exclamation-triangle')
            ->emptyStateDescription('Once you write your first ' . static::getModelLabel() . ', it will appear here.');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SavingsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBudgetPlans::route('/'),
            'create' => Pages\CreateBudgetPlan::route('/create'),
            'edit' => Pages\EditBudgetPlan::route('/{record}/edit'),
        ];
    }
}
