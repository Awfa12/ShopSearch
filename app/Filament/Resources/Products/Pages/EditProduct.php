<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        // Sync to Meilisearch after update
        $this->record->searchable();
    }

    protected function afterDelete(): void
    {
        // Remove from Meilisearch after deletion
        $this->record->unsearchable();
    }
}
