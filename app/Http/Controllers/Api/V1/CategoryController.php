<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::active();

        if ($request->parent_only) {
            $query->whereNull('parent_id');
        }

        $categories = $query
            ->with('children')
            ->latest()
            ->paginate($request->per_page ?? 50);

        return response()->json([
            'success' => true,
            'data' => CategoryResource::collection($categories),
            'meta' => ['total' => $categories->total()]
        ]);
    }

    public function show(Category $category)
    {
        $category->load('parent', 'children', 'ads');

        return response()->json([
            'success' => true,
            'data' => new CategoryResource($category)
        ]);
    }
}
