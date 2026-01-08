<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdRequest;
use App\Http\Requests\UpdateAdRequest;
use App\Http\Resources\AdResource;
use App\Models\Ad;
use App\Models\AdImage;
use App\Services\StorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdController extends Controller
{
    public function __construct(private StorageService $storage) {}

    public function index(Request $request)
    {
        $query = Ad::approved()
            ->active()
            ->with('user', 'category', 'images');

        // Filters
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }
        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }
        if ($request->condition) {
            $query->where('condition', $request->condition);
        }
        if ($request->location) {
            $query->where('location', 'like', "%{$request->location}%");
        }

        // Sort
        $sort = $request->sort ?? 'newest';
        match($sort) {
            'newest' => $query->latest(),
            'oldest' => $query->oldest(),
            'price_low' => $query->orderBy('price', 'asc'),
            'price_high' => $query->orderBy('price', 'desc'),
            'featured' => $query->where('is_featured', true)->latest(),
            default => $query->latest()
        };

        // Featured ads first
        $query->orderByRaw('is_featured DESC, created_at DESC');

        $ads = $query->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => AdResource::collection($ads),
            'meta' => [
                'total' => $ads->total(),
                'count' => $ads->count(),
                'current_page' => $ads->currentPage(),
                'last_page' => $ads->lastPage(),
            ]
        ]);
    }

    public function show(Ad $ad)
    {
        if (!$ad->is_approved && auth()->id() !== $ad->user_id) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $ad->incrementViews();

        return response()->json([
            'success' => true,
            'data' => new AdResource($ad->load('user', 'category', 'images'))
        ]);
    }

    public function store(StoreAdRequest $request)
    {
        $ad = auth()->user()->ads()->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Ad created successfully. Awaiting admin approval.',
            'data' => new AdResource($ad)
        ], 201);
    }

    public function update(UpdateAdRequest $request, Ad $ad)
    {
        if ($ad->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $ad->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Ad updated successfully',
            'data' => new AdResource($ad)
        ]);
    }

    public function destroy(Ad $ad)
    {
        if ($ad->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Delete images
        foreach ($ad->images as $image) {
            Storage::delete($image->image_path);
        }

        $ad->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ad deleted successfully'
        ]);
    }

    public function uploadImages(Request $request, Ad $ad)
    {
        if ($ad->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'images' => 'required|array',
            'images.*' => 'required|image|max:5120', // 5MB
        ]);

        $images = [];
        foreach ($request->file('images') as $image) {
            $path = $this->storage->uploadImage($image, 'ads');

            $adImage = $ad->images()->create([
                'image_path' => $path,
                'is_primary' => $ad->images()->count() === 0,
            ]);

            $images[] = $adImage;
        }

        return response()->json([
            'success' => true,
            'message' => 'Images uploaded successfully',
            'data' => $images
        ]);
    }

    public function deleteImage(Ad $ad, AdImage $image)
    {
        if ($ad->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        Storage::delete($image->image_path);
        $image->delete();

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully'
        ]);
    }

    public function search(Request $request)
    {
        return $this->index($request);
    }
}
