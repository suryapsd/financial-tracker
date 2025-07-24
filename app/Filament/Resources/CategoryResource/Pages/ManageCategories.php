<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use Filament\Actions;
use App\Models\Category;
use App\Enums\CategoryType;
use App\Helpers\EnumHelper;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\CategoryResource;

class ManageCategories extends ManageRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return CategoryResource::getWidgets();
    }

    public function getTabs(): array
    {
        $tabs = [
            null => Tab::make('All')
                ->badge(Category::count()),
        ];

        foreach (CategoryType::cases() as $type) {
            $tabs[$type->value] = Tab::make($type->getLabel())
                ->badge(Category::where('type', $type->value)->count())
                ->icon($type->getIcon())
                ->badgeColor($type->getColor())
                ->query(fn($query) => $query->where('type', $type->value));
        }

        return $tabs;
    }
}
