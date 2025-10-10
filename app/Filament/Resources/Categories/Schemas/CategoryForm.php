<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Models\Category;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Ramsey\Uuid\FeatureSet;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(100)
                    ->string(),
                Select::make('parent_id')
                    ->label('Category Parent')
                    ->options(Category::query()->pluck('name', 'category_id'))
                    ->searchable(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull()
                    ->string(),
                // TextInput::make('created_by')
                //     ->numeric()
                //     ->readOnly(),
                // TextInput::make('updated_by')
                //     ->numeric()
                //     ->readOnly(),
            ]);
    }
}
