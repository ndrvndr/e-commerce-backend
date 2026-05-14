<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GalleryImage;
use Illuminate\Http\JsonResponse;

class GalleryController extends Controller
{
    public function index(): JsonResponse
    {
        $urls = GalleryImage::orderBy('sort_order')
            ->get()
            ->map(fn (GalleryImage $image) => r2_url($image->path))
            ->values()
            ->all();

        return response()->json([
            'data' => $urls,
        ]);
    }
}
