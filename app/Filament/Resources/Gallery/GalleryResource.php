<?php

namespace App\Filament\Resources\Gallery;

use App\Filament\Resources\Gallery\Pages\ListGalleryImages;
use App\Filament\Resources\Gallery\Tables\GalleryTable;
use App\Models\GalleryImage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GalleryResource extends Resource
{
    protected static ?string $model = GalleryImage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?string $navigationLabel = 'Gallery';

    protected static ?string $modelLabel = 'Image';

    protected static ?string $pluralModelLabel = 'Gallery';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return GalleryTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGalleryImages::route('/'),
        ];
    }
}
