<?php

namespace App\Filament\Resources\Categories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Category Name')
                    ->searchable()
                    ->sortable()
                    ->default('Null'),
                TextColumn::make('parentId.name')
                    ->label('Parent Category')
                    ->sortable()
                    ->sortable(query: fn (Builder $query, string $direction): Builder => $query->orderBy('parent_id', $direction)) // Optional: Sort by ID for performance
                    ->formatStateUsing(fn (string $state): string => $state ?: 'No Parent') // Handles null parents
                    ->default('Null'),
                TextColumn::make('description')
                    ->label('Description'),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->default('Null')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->default('Null')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('userCreator.username')
                    ->label('Created By')
                    ->numeric()
                    ->sortable()
                    ->default('Null')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('userUpdator.username')
                    ->label('Updated By')
                    ->numeric()
                    ->sortable()
                    ->default('Null')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->label('Deleted At')
                    ->dateTime()
                    ->sortable()
                    ->default('Null')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
