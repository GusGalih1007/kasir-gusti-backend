<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;
    
    // Override this method to redirect to Index after create
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index') . '?highlight=' . $this->record->id;
    }
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Category created'; // Optional: Customize success message
    }
}
