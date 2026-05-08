<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index()
    {
        $catalogs = \App\Models\Catalog::all();
        return \App\Http\Resources\CatalogResource::collection($catalogs);
    }

    public function show($slug)
    {
        $catalog = \App\Models\Catalog::where('slug', $slug)
            ->with(['products' => function($query) {
                $query->where('is_active', true)->with('variations');
            }])
            ->firstOrFail();

        return new \App\Http\Resources\CatalogResource($catalog);
    }
}
