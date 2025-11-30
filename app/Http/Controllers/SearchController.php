<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q', '');
        $categoryId = $request->input('category');
        $brandId = $request->input('brand');
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');

        // Start with base search
        $search = Product::search($query);

        // Apply filters
        if ($categoryId) {
            $search->where('category_id', $categoryId);
        }

        if ($brandId) {
            $search->where('brand_id', $brandId);
        }

        if ($minPrice !== null) {
            $search->where('price', '>=', $minPrice);
        }

        if ($maxPrice !== null) {
            $search->where('price', '<=', $maxPrice);
        }

        // Get results with pagination
        $products = $search->paginate(24);

        // Eager load relationships
        $products->load(['category', 'brand']);

        return response()->json([
            'products' => $products,
            'query' => $query,
            'filters' => [
                'category_id' => $categoryId,
                'brand_id' => $brandId,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
            ],
        ]);
    }
}
