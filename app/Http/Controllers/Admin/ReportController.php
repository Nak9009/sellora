<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Report::with('ad', 'reporter');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $reports = $query->latest()->paginate($request->per_page ?? 20);

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }

    public function resolve(Request $request, Report $report)
    {
        $request->validate(['action' => 'required|in:dismiss,remove_ad,block_user']);

        match($request->action) {
            'dismiss' => $report->update(['status' => 'dismissed']),
            'remove_ad' => $report->ad->delete(),
            'block_user' => $report->ad->user->update(['is_blocked' => true]),
        };

        $report->update(['status' => 'resolved']);

        return response()->json([
            'success' => true,
            'message' => 'Report resolved successfully'
        ]);
    }
}
