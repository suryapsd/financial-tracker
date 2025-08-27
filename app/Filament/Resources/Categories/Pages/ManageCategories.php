<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Models\Category;
use App\Enums\CategoryType;
use Filament\Actions\CreateAction;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\Categories\CategoryResource;

class ManageCategories extends ManageRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
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
