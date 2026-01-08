<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlanResource;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index(Request $request)
    {
        $plans = Plan::latest()->paginate($request->per_page ?? 20);

        return response()->json([
            'success' => true,
            'data' => PlanResource::collection($plans)
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:plans',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string',
            'duration_days' => 'required|integer|min:1',
            'featured_ads_count' => 'required|integer|min:0',
            'ad_duration_days' => 'required|integer|min:1',
            'features' => 'nullable|json',
        ]);

        $plan = Plan::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Plan created successfully',
            'data' => new PlanResource($plan)
        ], 201);
    }

    public function update(Request $request, Plan $plan)
    {
        $request->validate([
            'name' => "required|string|unique:plans,name,$plan->id",
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'featured_ads_count' => 'required|integer|min:0',
            'ad_duration_days' => 'required|integer|min:1',
            'features' => 'nullable|json',
            'is_active' => 'boolean',
        ]);

        $plan->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Plan updated successfully',
            'data' => new PlanResource($plan)
        ]);
    }

    public function destroy(Plan $plan)
    {
        if ($plan->subscriptions()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete plan with active subscriptions'
            ], 422);
        }

        $plan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Plan deleted successfully'
        ]);
    }
}
