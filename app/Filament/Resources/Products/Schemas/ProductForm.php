<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Filament\Schemas\Components\Utilities\Set;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('catalog_id')
                    ->relationship('catalog', 'name')
                    ->searchable()
                    ->required()
                    ->preload(),
                TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                RichEditor::make('description')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                TextInput::make('discount_price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                TextInput::make('material')
                    ->required(),
                TextInput::make('weight')
                    ->required()
                    ->numeric()
                    ->suffix('gram'),
                Toggle::make('is_active')
                    ->default(true)
                    ->required(),
                FileUpload::make('image_primary')
                    ->label('Gambar Utama')
                    ->required()
                    ->image()
                    ->directory('products'),
                FileUpload::make('image_hover')
                    ->label('Gambar Saat Hover')
                    ->required()
                    ->image()
                    ->directory('products'),
                FileUpload::make('size_chart_image')
                    ->label('Panduan Ukuran (Size Chart)')
                    ->image()
                    ->directory('products')
                    ->columnSpanFull(),
                Repeater::make('color_images')
                    ->label('Daftar Warna & Foto')
                    ->schema([
                        TextInput::make('color')
                            ->label('Nama Warna (Contoh: Putih)')
                            ->live(onBlur: true)
                            ->required(),
                        FileUpload::make('images')
                            ->label('Foto Baju (Maks. 5)')
                            ->image()
                            ->multiple()
                            ->maxFiles(5)
                            ->directory('products/colors')
                            ->required(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Repeater::make('variations')
                    ->relationship('variations')
                    ->schema([
                        Select::make('color')
                            ->required()
                            ->label('Warna')
                            ->options(function (\Filament\Schemas\Components\Utilities\Get $get) {
                                $colors = $get('../../color_images');
                                if (!is_array($colors)) return [];
                                $options = [];
                                foreach ($colors as $item) {
                                    if (!empty($item['color'])) {
                                        $options[$item['color']] = $item['color'];
                                    }
                                }
                                return $options;
                            }),
                        TextInput::make('size')
                            ->required()
                            ->label('Ukuran'),
                        TextInput::make('stock')
                            ->label('Stok')
                            ->numeric()
                            ->default(0)
                            ->required(),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }
}
