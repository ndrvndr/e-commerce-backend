<?php

namespace App\Filament\Resources\Gallery\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GalleryTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('path')
                    ->label('Preview')
                    ->getStateUsing(fn ($record) => r2_url($record->path))
                    ->width(80)
                    ->height(80)
                    ->extraImgAttributes(['class' => 'rounded object-cover']),

                TextColumn::make('sort_order')
                    ->label('Order')
                    ->sortable(),

                TextColumn::make('createdAt')
                    ->label('Uploaded')
                    ->dateTime()
                    ->sortable(),
            ])
            ->reorderable('sort_order')
            ->paginated(false)
            ->recordActions([
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No images yet')
            ->emptyStateDescription('Use the "Upload Images" button above to add images to the gallery.')
            ->emptyStateIcon(Heroicon::OutlinedPhoto);
    }
}
