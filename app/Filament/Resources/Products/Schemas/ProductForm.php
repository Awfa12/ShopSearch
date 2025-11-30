<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Brand;
use App\Models\Category;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $operation, $state, callable $set) {
                        if ($operation !== 'create') {
                            return;
                        }
                        $set('slug', Str::slug($state));
                    }),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->alphaDash(),
                Textarea::make('description')
                    ->rows(4)
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('€')
                    ->step(0.01)
                    ->minValue(0),
                Select::make('category_id')
                    ->label('Category')
                    ->options(Category::where('active', true)->orderBy('name')->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search) => 
                        Category::where('active', true)
                            ->where('name', 'like', "%{$search}%")
                            ->limit(50)
                            ->pluck('name', 'id')
                    )
                    ->preload(),
                Select::make('brand_id')
                    ->label('Brand')
                    ->options(Brand::where('active', true)->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search) => 
                        Brand::where('active', true)
                            ->where('name', 'like', "%{$search}%")
                            ->limit(50)
                            ->pluck('name', 'id')
                    )
                    ->preload()
                    ->nullable(),
                KeyValue::make('attributes')
                    ->label('Product Attributes')
                    ->keyLabel('Attribute Name')
                    ->valueLabel('Attribute Value')
                    ->columnSpanFull(),
                TextInput::make('stock')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
                TextInput::make('image_url')
                    ->label('Image URL')
                    ->url()
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }
}
