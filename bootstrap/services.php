<?php

use Illuminate\Support\Facades\Event;
use App\Events\OrderPlaced;
use App\Events\PaymentSuccess;
use App\Listeners\SendOrderPlacedEmail;
use App\Listeners\SendPaymentSuccessEmail;

/*
|--------------------------------------------------------------------------
| Event Registrations (Laravel 11+)
|--------------------------------------------------------------------------
*/

// Event::listen(
//     OrderPlaced::class,
//     SendOrderPlacedEmail::class
// );

Event::listen(
    OrderPlaced::class,
    \App\Listeners\SendOrderNotification::class
);

// Event::listen(
//     PaymentSuccess::class,
//     SendPaymentSuccessEmail::class
// );


