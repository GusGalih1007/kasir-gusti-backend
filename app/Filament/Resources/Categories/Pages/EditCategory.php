<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    // Override this method to redirect to Index after create
      protected function getRedirectUrl(): string
      {
          return $this->getResource()::getUrl('index'); // Redirects to /admin/categories
      }
      protected function getCreatedNotificationTitle(): ?string
      {
          return 'Category created'; // Optional: Customize success message
      }
}
