<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class GalleryImage extends Model
{
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    const MAX_IMAGES = 30;

    protected $fillable = [
        'path',
        'sort_order',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function (GalleryImage $image) {
            Storage::disk('s3')->delete($image->path);
        });
    }
}
