<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdResource;
use App\Models\{Favorite, Ad};
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $favorites = auth()->user()
            ->favorites()
            ->with('ad.user', 'ad.category', 'ad.images')
            ->latest()
            ->paginate($request->per_page ?? 15);

        $ads = $favorites->pluck('ad');

        return response()->json([
            'success' => true,
            'data' => AdResource::collection($ads),
            'meta' => ['total' => $favorites->total()]
        ]);
    }

    public function store(Ad $ad)
    {
        $favorite = Favorite::firstOrCreate([
            'user_id' => auth()->id(),
            'ad_id' => $ad->id,
        ]);

        if (!$favorite->wasRecentlyCreated) {
            return response()->json([
                'success' => false,
                'message' => 'Already in favorites'
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Added to favorites',
            'data' => new AdResource($ad->load('user', 'category', 'images'))
        ], 201);
    }

    public function destroy(Ad $ad)
    {
        $favorite = Favorite::where('user_id', auth()->id())
            ->where('ad_id', $ad->id)
            ->first();

        if (!$favorite) {
            return response()->json([
                'success' => false,
                'message' => 'Not in favorites'
            ], 404);
        }

        $favorite->delete();

        return response()->json([
            'success' => true,
            'message' => 'Removed from favorites'
        ]);
    }

    public function check(Ad $ad)
    {
        $isFavorite = Favorite::where('user_id', auth()->id())
            ->where('ad_id', $ad->id)
            ->exists();

        return response()->json([
            'success' => true,
            'data' => ['is_favorite' => $isFavorite]
        ]);
    }
}
