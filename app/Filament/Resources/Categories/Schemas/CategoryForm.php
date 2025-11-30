<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Models\Category;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
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
                Select::make('parent_id')
                    ->label('Parent Category')
                    ->options(function ($record) {
                        // Get all categories except the current one (to prevent circular references)
                        $query = Category::where('active', true)->orderBy('name');
                        
                        if ($record) {
                            // Exclude current category and its children
                            $excludeIds = [$record->id];
                            $children = Category::where('parent_id', $record->id)->pluck('id');
                            $excludeIds = array_merge($excludeIds, $children->toArray());
                            $query->whereNotIn('id', $excludeIds);
                        }
                        
                        return $query->pluck('name', 'id');
                    })
                    ->searchable()
                    ->preload()
                    ->nullable()
                    ->helperText('Leave empty for top-level category'),
                Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),
                Toggle::make('active')
                    ->label('Active')
                    ->default(true)
                    ->required(),
            ]);
    }
}
