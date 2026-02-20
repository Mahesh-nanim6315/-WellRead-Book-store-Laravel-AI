<?php

namespace App\Listeners;

use App\Events\PaymentSuccess;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentSuccessMail;

class SendPaymentSuccessEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
  public function handle(PaymentSuccess $event)
    {
        Mail::to($event->order->user->email)
         ->send(new PaymentSuccessMail($event->order));
    }
}
