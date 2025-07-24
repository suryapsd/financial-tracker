<?php

namespace App\Filament\Resources\Plans;

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
use Filament\Tables\Actions\CreateAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Plans\BudgetPlanResource\Pages;
use App\Filament\Resources\Plans\BudgetPlanResource\RelationManagers;

class BudgetPlanResource extends Resource
{
    protected static ?string $model = BudgetPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-vertical';
    protected static ?string $navigationGroup = 'Plans';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('user_id')->default(Auth::id()),

                Select::make('category_id')
                    ->label('Category')
                    ->options(Category::where('type', '!=', 'income')->pluck('name', 'id'))
                    ->searchable()
                    ->placeholder('Select a category'),

                TextInput::make('title')
                    ->label('Plan name')
                    ->placeholder('Enter the plan name')
                    ->required(),

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
                    ->placeholder('e.g. 1,250,000')
                    ->prefix('Rp.')
                    ->required()
                    ->numeric()
                    ->rules(['numeric', 'min:0'])
                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2),

                Select::make('month')
                    ->label('Month')
                    ->required()
                    ->options([
                        1 => 'January',
                        2 => 'February',
                        3 => 'March',
                        4 => 'April',
                        5 => 'May',
                        6 => 'June',
                        7 => 'July',
                        8 => 'August',
                        9 => 'September',
                        10 => 'October',
                        11 => 'November',
                        12 => 'December',
                    ])
                    ->placeholder('Select a month'),

                TextInput::make('year')
                    ->label('Year')
                    ->placeholder('e.g. 2025')
                    ->required()
                    ->numeric()
                    ->minValue(2000)
                    ->maxValue(2100),

                Textarea::make('description')
                    ->label('Description')
                    ->placeholder('e.g. Saving up for a down payment on a home')
                    ->columnSpan(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category.name')->label('Category')->searchable(),
                TextColumn::make('title')->label('Plan Name')->searchable(),
                TextColumn::make('planned_amount')->label('Planned')->money('IDR', true),
                TextColumn::make('actual_amount')->label('Actual')->money('IDR', true),
                TextColumn::make('month')->label('Month'),
                TextColumn::make('year')->label('Year'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageBudgetPlans::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }
}
