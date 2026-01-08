<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'ad_id' => 'required|exists:ads,id',
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        // Check if user already reported this ad
        $existing = Report::where('ad_id', $request->ad_id)
            ->where('reporter_id', auth()->id())
            ->exists();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reported this ad'
            ], 422);
        }

        $report = Report::create([
            'ad_id' => $request->ad_id,
            'reporter_id' => auth()->id(),
            'reason' => $request->reason,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Report submitted successfully',
            'data' => $report
        ], 201);
    }

    public function myReports(Request $request)
    {
        $reports = auth()->user()
            ->reports()
            ->with('ad')
            ->latest()
            ->paginate($request->per_page ?? 10);

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }
}
