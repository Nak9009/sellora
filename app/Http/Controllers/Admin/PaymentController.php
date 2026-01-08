<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('user');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->gateway) {
            $query->where('gateway', $request->gateway);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->latest()->paginate($request->per_page ?? 20);

        return response()->json([
            'success' => true,
            'data' => PaymentResource::collection($payments),
            'meta' => [
                'total' => $payments->total(),
                'sum' => $query->sum('amount'),
            ]
        ]);
    }

    public function show(Payment $payment)
    {
        return response()->json([
            'success' => true,
            'data' => new PaymentResource($payment->load('user', 'subscription'))
        ]);
    }
}
