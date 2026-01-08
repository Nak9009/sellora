<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Resources\AdResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function profile(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => new UserResource($request->user()->load('subscriptions.plan'))
        ]);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'first_name' => 'string|max:255',
            'last_name' => 'string|max:255',
            'phone' => 'nullable|unique:users,phone,' . auth()->id(),
            'bio' => 'nullable|string|max:1000',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'zip_code' => 'nullable|string',
            'country' => 'nullable|string',
        ]);

        $user = auth()->user();
        $user->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => new UserResource($user)
        ]);
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate(['avatar' => 'required|image|max:2048']);

        $user = auth()->user();

        if ($user->avatar) {
            Storage::delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return response()->json([
            'success' => true,
            'message' => 'Avatar uploaded successfully',
            'data' => new UserResource($user)
        ]);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed|different:current_password'
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 422);
        }

        auth()->user()->update(['password' => Hash::make($request->password)]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully'
        ]);
    }

    public function myAds(Request $request)
    {
        $ads = auth()->user()
            ->ads()
            ->with('category', 'images')
            ->latest()
            ->paginate($request->per_page ?? 10);

        return response()->json([
            'success' => true,
            'data' => AdResource::collection($ads),
            'meta' => [
                'total' => $ads->total(),
                'current_page' => $ads->currentPage(),
            ]
        ]);
    }

    public function stats(Request $request)
    {
        $user = auth()->user();

        return response()->json([
            'success' => true,
            'data' => [
                'total_ads' => $user->ads()->count(),
                'active_ads' => $user->ads()->active()->count(),
                'favorites_count' => $user->favorites()->count(),
                'rating' => $user->rating,
                'total_views' => $user->ads()->sum('views_count'),
                'active_subscription' => $user->subscriptions()->active()->first(),
            ]
        ]);
    }
}
