<?php

namespace App\Filament\Resources\Gallery\Pages;

use App\Filament\Resources\Gallery\GalleryResource;
use App\Models\GalleryImage;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;
use Filament\Facades\Filament;

class ListGalleryImages extends ListRecords
{
    protected static string $resource = GalleryResource::class;

    protected ?string $heading = "Gallery";

    protected ?string $subheading = "Manage your gallery images. Maximum 30 images allowed.";

    protected function getHeaderActions(): array
    {
        return [
            Action::make("upload")
                ->label("Upload Images")
                ->icon(Heroicon::OutlinedArrowUpTray)
                ->authorize(fn() => auth()->user()->can("Create:GalleryImage"))
                ->modalHeading("Upload Gallery Images")
                ->modalDescription(function () {
                    $current = GalleryImage::count();
                    $remaining = GalleryImage::MAX_IMAGES - $current;
                    return "Gallery usage: {$current} / " .
                        GalleryImage::MAX_IMAGES .
                        " images. You can upload up to {$remaining} more.";
                })
                ->modalSubmitActionLabel("Upload")
                ->form([
                    FileUpload::make("images")
                        ->label("Images")
                        ->multiple()
                        ->image()
                        ->directory("gallery")
                        ->maxFiles(GalleryImage::MAX_IMAGES)
                        ->hint(
                            "Only the first available slots will be saved if the gallery limit is reached.",
                        )
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $existing = GalleryImage::count();
                    $remaining = GalleryImage::MAX_IMAGES - $existing;

                    if ($remaining <= 0) {
                        Notification::make()
                            ->title("Gallery is full")
                            ->body(
                                "The gallery already contains " .
                                    GalleryImage::MAX_IMAGES .
                                    " images. Delete some before uploading new ones.",
                            )
                            ->warning()
                            ->send();

                        return;
                    }

                    $allFiles = array_values($data["images"]);
                    $toSave = array_slice($allFiles, 0, $remaining);
                    $toDiscard = array_slice($allFiles, $remaining);

                    // Remove files that exceeded the cap from storage
                    foreach ($toDiscard as $path) {
                        Storage::disk("s3")->delete($path);
                    }

                    $maxOrder = GalleryImage::max("sort_order") ?? 0;

                    foreach ($toSave as $index => $path) {
                        GalleryImage::create([
                            "path" => $path,
                            "sort_order" => $maxOrder + $index + 1,
                        ]);
                    }

                    $saved = count($toSave);
                    $skipped = count($toDiscard);

                    if ($skipped > 0) {
                        Notification::make()
                            ->title("{$saved} image(s) uploaded")
                            ->body(
                                "{$skipped} image(s) were skipped because the gallery limit of " .
                                    GalleryImage::MAX_IMAGES .
                                    " was reached.",
                            )
                            ->warning()
                            ->send();
                    } else {
                        Notification::make()
                            ->title("{$saved} image(s) uploaded successfully")
                            ->success()
                            ->send();
                    }
                }),
        ];
    }
}
