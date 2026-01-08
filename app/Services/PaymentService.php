<?php
namespace App\Services;

use App\Models\Payment;
use App\Models\Subscription;
use Stripe\Stripe;
use Stripe\Charge;
use Exception;

class PaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config('payment.stripe.secret_key'));
    }

    public function initiatePayment(array $data)
    {
        $payment = Payment::create([
            'user_id' => auth()->id(),
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'USD',
            'method' => $data['method'] ?? 'card',
            'gateway' => $data['gateway'] ?? 'stripe',
            'description' => $data['description'] ?? null,
            'status' => 'pending',
            'metadata' => $data['metadata'] ?? null,
        ]);

        try {
            match ($data['gateway'] ?? 'stripe') {
                'stripe' => $this->processStripe($payment, $data),
                'paypal' => $this->processPayPal($payment, $data),
                'offline' => $this->processOffline($payment, $data),
                default => throw new Exception('Invalid gateway')
            };
        } catch (Exception $e) {
            $payment->update(['status' => 'failed']);
            throw $e;
        }

        return $payment;
    }

    private function processStripe(Payment $payment, array $data)
    {
        $charge = Charge::create([
            'amount' => (int) ($payment->amount * 100),
            'currency' => strtolower($payment->currency),
            'source' => $data['stripe_token'] ?? $data['payment_method_id'],
            'description' => $payment->description,
            'metadata' => [
                'payment_id' => $payment->id,
                'user_id' => auth()->id(),
            ]
        ]);

        $payment->update([
            'transaction_id' => $charge->id,
            'status' => 'completed',
        ]);

        return $payment;
    }

    private function processPayPal(Payment $payment, array $data)
    {
        // PayPal SDK implementation
        // For now, returning payment in pending status
        // Actual implementation requires PayPal SDK
        $payment->update([
            'status' => 'pending',
            'reference' => $data['paypal_order_id'] ?? null,
        ]);

        return $payment;
    }

    private function processOffline(Payment $payment, array $data)
    {
        $payment->update([
            'status' => 'pending',
            'description' => $data['offline_notes'] ?? 'Offline payment initiated',
        ]);

        return $payment;
    }

    public function refundPayment(Payment $payment)
    {
        if ($payment->gateway === 'stripe' && $payment->transaction_id) {
            try {
                $charge = Charge::retrieve($payment->transaction_id);
                $charge->refund();

                $payment->update(['status' => 'refunded']);
                return true;
            } catch (Exception $e) {
                return false;
            }
        }

        return false;
    }

    public function createSubscription($userId, $planId, $paymentId)
    {
        $plan = \App\Models\Plan::find($planId);

        $subscription = Subscription::create([
            'user_id' => $userId,
            'plan_id' => $planId,
            'payment_id' => $paymentId,
            'status' => 'active',
            'started_at' => now(),
            'ends_at' => now()->addDays($plan->duration_days),
            'auto_renew' => false,
        ]);

        return $subscription;
    }
}
