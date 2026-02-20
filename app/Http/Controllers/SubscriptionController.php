<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Cashier\Billable;

class SubscriptionController extends Controller
{
    public function index()
{
    $plans = [
        [
            'name' => 'Free Reader',
            'monthly' => 0,
            'yearly' => 0,
            'features' => [
                'Access to free books',
                '2 downloads per month',
                'Standard support'
            ]
        ],
        [
            'name' => 'Premium Reader',
            'monthly' => 9,
            'yearly' => 90,
            'features' => [
                'Unlimited ebooks',
                '5 audiobooks per month',
                'Early access releases',
                'No ads'
            ],
            'popular' => true
        ],
        [
            'name' => 'Ultimate Reader',
            'monthly' => 19,
            'yearly' => 190,
            'features' => [
                'Unlimited ebooks',
                'Unlimited audiobooks',
                'Offline downloads',
                'Exclusive content',
                'Priority support'
            ]
        ],
    ];

    return view('subscriptions.index', compact('plans'));
}


public function checkout(Request $request)
{
    $user = Auth::user();

    $plan = $request->plan;
    $billing = $request->billing_cycle;

    $priceId = null;
    

    if ($plan === 'premium') {
        $priceId = $billing === 'monthly'
            ? env('STRIPE_PREMIUM_MONTHLY')
            : env('STRIPE_PREMIUM_YEARLY');
    }

    if ($plan === 'ultimate') {
        $priceId = $billing === 'monthly'
            ? env('STRIPE_ULTIMATE_MONTHLY')
            : env('STRIPE_ULTIMATE_YEARLY');
    }

    if ($user->plan === $plan && $user->billing_cycle === $billing) {
    return back()->with('error', 'You are already on this plan.');
}


    if ($user->subscribed('default')) {
            $user->subscription('default')->swap($priceId);
        } else {
            return $user->newSubscription('default', $priceId)
                ->trialDays(7)
                ->checkout([
                     'success_url' => route('profile'). '?plan='.$plan.'&billing='.$billing,
                      'cancel_url' => route('plans.index'),
                    // 'success_url' => route('subscription.success')
]);
}
}

public function success(Request $request)
{
    $user = Auth::user();

    $plan = $request->plan;
    $billing = $request->billing;

    Auth::user()->update([
        'plan' => $plan,
        'billing_cycle' => $billing,
        'plan_expires_at' => $billing === 'monthly'
            ? now()->addMonth()
            : now()->addYear(),
    ]);

    return redirect()->route('profile')
        ->with('success', 'Subscription activated successfully!');
}


public function cancel()
{
    $user = Auth::user();

    if ($user->subscribed('default')) {
        $user->subscription('default')->cancel();
    }

    return back()->with('success', 'Subscription will be cancelled at the end of billing period.');
}


public function resume()
{
    $user = Auth::user();

    if ($user->subscription('default')->onGracePeriod()) {
        $user->subscription('default')->resume();
    }

    return back()->with('success', 'Subscription resumed successfully.');
}



}



