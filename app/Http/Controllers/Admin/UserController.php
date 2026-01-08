<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('email', 'like', "%{$request->search}%")
                  ->orWhere('first_name', 'like', "%{$request->search}%")
                  ->orWhere('last_name', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        if ($request->is_blocked !== null) {
            $query->where('is_blocked', $request->is_blocked);
        }

        if ($request->is_verified !== null) {
            $query->where('is_verified', $request->is_verified);
        }

        $users = $query->latest()->paginate($request->per_page ?? 20);

        return response()->json([
            'success' => true,
            'data' => UserResource::collection($users),
            'meta' => [
                'total' => $users->total(),
                'current_page' => $users->currentPage(),
            ]
        ]);
    }

    public function show(User $user)
    {
        return response()->json([
            'success' => true,
            'data' => new UserResource($user->load('subscriptions.plan', 'ads'))
        ]);
    }

    public function block(User $user)
    {
        $user->update(['is_blocked' => true]);

        return response()->json([
            'success' => true,
            'message' => 'User blocked successfully',
            'data' => new UserResource($user)
        ]);
    }

    public function unblock(User $user)
    {
        $user->update(['is_blocked' => false]);

        return response()->json([
            'success' => true,
            'message' => 'User unblocked successfully',
            'data' => new UserResource($user)
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }
}
