<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Ad, Payment, Report};
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $metrics = [
            'total_users' => User::count(),
            'active_users' => User::active()->count(),
            'total_ads' => Ad::count(),
            'pending_ads' => Ad::pending()->count(),
            'approved_ads' => Ad::approved()->count(),
            'total_revenue' => Payment::completed()->sum('amount'),
            'pending_payments' => Payment::pending()->count(),
            'reports' => Report::pending()->count(),
        ];

        $charts = [
            'ads_per_month' => $this->getAdsPerMonth(),
            'revenue_per_month' => $this->getRevenuePerMonth(),
            'users_per_month' => $this->getUsersPerMonth(),
        ];

        $recent = [
            'ads' => Ad::latest()->take(10)->get(),
            'users' => User::latest()->take(10)->get(),
            'payments' => Payment::latest()->take(10)->get(),
            'reports' => Report::latest()->take(10)->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => compact('metrics', 'charts', 'recent')
        ]);
    }

    private function getAdsPerMonth()
    {
        return Ad::selectRaw('DATE_TRUNC(\'month\', created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    private function getRevenuePerMonth()
    {
        return Payment::selectRaw('DATE_TRUNC(\'month\', created_at) as month, SUM(amount) as total')
            ->where('status', 'completed')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }

    private function getUsersPerMonth()
    {
        return User::selectRaw('DATE_TRUNC(\'month\', created_at) as month, COUNT(*) as count')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
    }
}
