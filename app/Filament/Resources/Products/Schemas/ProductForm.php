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
use Filament\Forms\Components\Hidden;
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
                    ->unique(ignoreRecord: true)
                    ->columnSpanFull(),
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
                Hidden::make('is_active')
                    ->default(true),
                FileUpload::make('image_primary')
                    ->label('Main Image')
                    ->required()
                    ->image()
                    ->directory('products'),
                FileUpload::make('image_hover')
                    ->label('Hover Image')
                    ->required()
                    ->image()
                    ->directory('products'),
                FileUpload::make('size_chart_image')
                    ->required()
                    ->label('Size Chart')
                    ->image()
                    ->directory('products')
                    ->columnSpanFull(),
                Repeater::make('color_images')
                    ->required()
                    ->label('Color Images')
                    ->schema([
                        TextInput::make('color')
                            ->label('Color')
                            ->live(onBlur: true)
                            ->required(),
                        FileUpload::make('images')
                            ->label('Clothes Pictures (Max 5)')
                            ->image()
                            ->multiple()
                            ->maxFiles(5)
                            ->directory('products/colors')
                            ->required(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Repeater::make('variations')
                    ->required()
                    ->relationship('variations')
                    ->schema([
                        Select::make('color')
                            ->required()
                            ->label('Color')
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
                            ->label('Size'),
                        TextInput::make('stock')
                            ->label('Stock')
                            ->numeric()
                            ->default(0)
                            ->required(),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
            ]);
    }
}
