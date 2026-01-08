<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdResource;
use App\Models\Ad;
use Illuminate\Http\Request;

class AdController extends Controller
{
    public function index(Request $request)
    {
        $query = Ad::with('user', 'category', 'images');

        if ($request->status) {
            match($request->status) {
                'pending' => $query->where('is_approved', false),
                'approved' => $query->where('is_approved', true),
                'rejected' => $query->whereNotNull('rejection_reason'),
                'active' => $query->where('status', 'active'),
                'sold' => $query->where('status', 'sold'),
            };
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhereHas('user', fn($q) => $q->where('email', 'like', "%{$request->search}%"));
            });
        }

        $ads = $query->latest()->paginate($request->per_page ?? 20);

        return response()->json([
            'success' => true,
            'data' => AdResource::collection($ads),
            'meta' => [
                'total' => $ads->total(),
                'current_page' => $ads->currentPage(),
                'last_page' => $ads->lastPage(),
            ]
        ]);
    }

    public function show(Ad $ad)
    {
        return response()->json([
            'success' => true,
            'data' => new AdResource($ad->load('user', 'category', 'images', 'reports'))
        ]);
    }

    public function approve(Request $request, Ad $ad)
    {
        $ad->update([
            'is_approved' => true,
            'rejection_reason' => null,
        ]);

        // Send notification to user
        // $ad->user->notify(new AdApprovedNotification($ad));

        return response()->json([
            'success' => true,
            'message' => 'Ad approved successfully',
            'data' => new AdResource($ad)
        ]);
    }

    public function reject(Request $request, Ad $ad)
    {
        $request->validate(['reason' => 'required|string|min:10']);

        $ad->update([
            'is_approved' => false,
            'rejection_reason' => $request->reason,
        ]);

        // Send notification to user
        // $ad->user->notify(new AdRejectedNotification($ad, $request->reason));

        return response()->json([
            'success' => true,
            'message' => 'Ad rejected successfully',
            'data' => new AdResource($ad)
        ]);
    }

    public function destroy(Ad $ad)
    {
        $ad->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ad deleted successfully'
        ]);
    }
}
