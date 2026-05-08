<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Product::where('is_active', true)->with(['catalog', 'variations']);

        if ($request->has('search')) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($request->search) . '%']);
        }

        if ($request->has('catalog_slug') && $request->catalog_slug !== 'all-products') {
            $query->whereHas('catalog', function($q) use ($request) {
                $q->where('slug', $request->catalog_slug);
            });
        }
       
        if ($request->has('availability')) {
            $availabilities = (array) $request->availability;

            $query->where(function($q) use ($availabilities) {
                if (in_array('in_stock', $availabilities)) {
                    $q->orWhereHas('variations', function($subQ) {
                        $subQ->where('stock', '>', 0);
                    });
                }

                if (in_array('out_of_stock', $availabilities)) {
                    $q->orWhereDoesntHave('variations', function($subQ) {
                        $subQ->where('stock', '>', 0);
                    });
                }
            });
        }

        if ($request->has('price_min')) {
            $query->whereRaw('COALESCE(discount_price, price) >= ?', [(float) $request->price_min]);
        }
        if ($request->has('price_max')) {
            $query->whereRaw('COALESCE(discount_price, price) <= ?', [(float) $request->price_max]);
        }

        $sort = $request->get('sort', 'newest'); // default to newest
        switch ($sort) {
            case 'price_asc':
                $query->orderByRaw('COALESCE(discount_price, price) ASC');
                break;
            case 'price_desc':
                $query->orderByRaw('COALESCE(discount_price, price) DESC');
                break;
            case 'a_z':
                $query->orderBy('name', 'ASC');
                break;
            case 'z_a':
                $query->orderBy('name', 'DESC');
                break;
            case 'oldest':
                $query->orderBy('createdAt', 'ASC');
                break;
            case 'best_selling':
                // TODO: Requires an 'orders' table or a 'sold_count' column.
                // For now, we fallback to newest.
                $query->orderBy('createdAt', 'DESC');
                break;
            case 'newest':
            default:
                $query->orderBy('createdAt', 'DESC');
                break;
        }

        $perPage = $request->get('per_page', 12);
        $products = $query->paginate($perPage);

        return \App\Http\Resources\ProductResource::collection($products);
    }

    public function show($slug)
    {
        $product = \App\Models\Product::where('slug', $slug)
            ->where('is_active', true)
            ->with(['catalog', 'variations'])
            ->firstOrFail();

        return new \App\Http\Resources\ProductResource($product);
    }
}
