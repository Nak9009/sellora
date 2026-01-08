<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Notifications\Notification;

class NotificationService
{
    public function sendAdApprovedNotification(User $user, $ad)
    {
        // Send notification email
        // $user->notify(new AdApprovedNotification($ad));
    }

    public function sendAdRejectedNotification(User $user, $ad, $reason)
    {
        // Send notification email
        // $user->notify(new AdRejectedNotification($ad, $reason));
    }

    public function sendNewMessageNotification(User $user, $message)
    {
        // Send push notification
        // $user->notify(new NewMessageNotification($message));
    }

    public function sendPaymentConfirmationNotification(User $user, $payment)
    {
        // Send notification email
        // $user->notify(new PaymentConfirmationNotification($payment));
    }

    public function sendSubscriptionActivatedNotification(User $user, $subscription)
    {
        // Send notification email
        // $user->notify(new SubscriptionActivatedNotification($subscription));
    }
}
