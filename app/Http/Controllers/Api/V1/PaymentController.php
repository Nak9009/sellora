<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Http\Resources\PlanResource;
use App\Http\Resources\SubscriptionResource;
use App\Models\{Payment, Plan, Subscription};
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    public function plans(Request $request)
    {
        $plans = Plan::active()
            ->latest()
            ->paginate($request->per_page ?? 10);

        return response()->json([
            'success' => true,
            'data' => PlanResource::collection($plans),
            'meta' => ['total' => $plans->total()]
        ]);
    }

    public function initiatePayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|in:USD,EUR,GBP,PHP,KHR',
            'method' => 'required|in:card,paypal,bank_transfer,cash',
            'gateway' => 'required|in:stripe,paypal,offline',
            'payment_method_id' => 'required_if:gateway,stripe',
            'description' => 'nullable|string',
        ]);

        try {
            $payment = $this->paymentService->initiatePayment($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Payment initiated',
                'data' => new PaymentResource($payment)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment failed: ' . $e->getMessage()
            ], 422);
        }
    }

    public function show(Payment $payment)
    {
        if ($payment->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'success' => true,
            'data' => new PaymentResource($payment->load('user', 'subscription'))
        ]);
    }

    public function subscribe(Request $request, Plan $plan)
    {
        $request->validate([
            'payment_method_id' => 'required_unless:use_existing,true',
            'use_existing' => 'boolean',
        ]);

        try {
            // Get or create payment
            if ($request->use_existing) {
                // Use last payment method
                $lastPayment = auth()->user()->payments()
                    ->where('status', 'completed')
                    ->latest()
                    ->first();

                if (!$lastPayment) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No saved payment method found'
                    ], 422);
                }

                $payment = Payment::create([
                    'user_id' => auth()->id(),
                    'amount' => $plan->price,
                    'currency' => $plan->currency,
                    'method' => $lastPayment->method,
                    'gateway' => $lastPayment->gateway,
                    'status' => 'completed',
                    'description' => "Subscription to {$plan->name}",
                ]);
            } else {
                $paymentData = array_merge($request->all(), [
                    'amount' => $plan->price,
                    'currency' => $plan->currency,
                    'gateway' => 'stripe',
                    'description' => "Subscription to {$plan->name}",
                ]);

                $payment = $this->paymentService->initiatePayment($paymentData);
            }

            if ($payment->status !== 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not completed'
                ], 422);
            }

            // Create subscription
            $subscription = $this->paymentService->createSubscription(
                auth()->id(),
                $plan->id,
                $payment->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Subscription activated',
                'data' => new SubscriptionResource($subscription->load('plan'))
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription failed: ' . $e->getMessage()
            ], 422);
        }
    }

    public function subscriptions(Request $request)
    {
        $subscriptions = auth()->user()
            ->subscriptions()
            ->with('plan')
            ->latest()
            ->paginate($request->per_page ?? 10);

        return response()->json([
            'success' => true,
            'data' => SubscriptionResource::collection($subscriptions)
        ]);
    }

    public function cancelSubscription(Subscription $subscription)
    {
        if ($subscription->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($subscription->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'Subscription already cancelled'
            ], 422);
        }

        $subscription->cancel();

        return response()->json([
            'success' => true,
            'message' => 'Subscription cancelled',
            'data' => new SubscriptionResource($subscription)
        ]);
    }
}
