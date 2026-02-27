<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;

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
                    'Standard support',
                ],
            ],
            [
                'name' => 'Premium Reader',
                'monthly' => 9,
                'yearly' => 90,
                'features' => [
                    'Unlimited ebooks',
                    '5 audiobooks per month',
                    'Early access releases',
                    'No ads',
                ],
                'popular' => true,
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
                    'Priority support',
                ],
            ],
        ];

        $subscriptionsEnabled = (bool) Setting::get('subscriptions_enabled', 1);
        $freeTrialDays = (int) Setting::get('free_trial_days', 7);

        return view('subscriptions.index', compact('plans', 'subscriptionsEnabled', 'freeTrialDays'));
    }

    public function checkout(Request $request)
    {
        $data = $request->validate([
            'plan' => 'required|in:free,premium,ultimate',
            'billing_cycle' => 'required|in:monthly,yearly',
        ]);

        $user = Auth::user();
        $plan = $data['plan'];
        $billing = $data['billing_cycle'];

        // If subscriptions are disabled, block any non-free plan changes.
        if (! (bool) Setting::get('subscriptions_enabled', 1) && $plan !== 'free') {
            abort(403, 'Subscriptions are currently disabled.');
        }

        if ($user->plan === $plan && $user->billing_cycle === $billing && ! $user->subscription('default')?->onGracePeriod()) {
            return back()->with('error', 'You are already on this plan.');
        }

        if ($plan === 'free') {
            if ($user->subscribed('default')) {
                $user->subscription('default')->cancelNow();
            }

            $this->syncPlanData($user, 'free', null, null);

            return redirect()->route('profile')->with('success', 'Downgraded to Free plan successfully.');
        }

        $priceId = $this->resolvePriceId($plan, $billing);

        if (! $priceId) {
            return back()->with('error', 'Invalid subscription configuration.');
        }

        $trialDays = (int) Setting::get('free_trial_days', 7);

        if ($user->subscribed('default')) {
            if ($user->subscription('default')->onGracePeriod()) {
                $user->subscription('default')->resume();
            }

            $user->subscription('default')->swap($priceId);
            $this->syncPlanData($user, $plan, $billing, $this->nextExpiry($billing));

            return redirect()->route('profile')->with('success', 'Subscription updated successfully.');
        }

        $request->session()->put('pending_subscription_plan', $plan);
        $request->session()->put('pending_subscription_billing', $billing);

        return $user->newSubscription('default', $priceId)
            ->trialDays($trialDays)
            ->checkout([
                'success_url' => route('subscription.success'),
                'cancel_url' => route('plans.index'),
            ]);
    }

    public function success(Request $request)
    {
        $user = Auth::user();

        // Primary source for fresh Checkout completion: values set by server before redirect.
        $pendingPlan = $request->session()->pull('pending_subscription_plan');
        $pendingBilling = $request->session()->pull('pending_subscription_billing');

        if (in_array($pendingPlan, ['premium', 'ultimate'], true) && in_array($pendingBilling, ['monthly', 'yearly'], true)) {
            $this->syncPlanData($user, $pendingPlan, $pendingBilling, $this->nextExpiry($pendingBilling));

            return redirect()->route('profile')->with('success', 'Subscription activated successfully.');
        }

        // Fallback when session is missing: infer from local Stripe subscription item.
        $subscription = $user->subscription('default');

        if (! $subscription) {
            return redirect()->route('plans.index')->with('error', 'No active Stripe subscription found.');
        }

        $priceId = optional($subscription->items->first())->stripe_price;

        if (! $priceId) {
            return redirect()->route('plans.index')->with('error', 'Could not determine subscription plan from Stripe.');
        }

        $planMeta = $this->resolvePlanFromPriceId($priceId);

        if (! $planMeta) {
            return redirect()->route('plans.index')->with('error', 'Unknown Stripe price mapping for your plan.');
        }

        $this->syncPlanData($user, $planMeta['plan'], $planMeta['billing'], $this->nextExpiry($planMeta['billing']));

        return redirect()->route('profile')->with('success', 'Subscription activated successfully.');
    }

    public function cancel()
    {
        $user = Auth::user();

        if (! $user->subscribed('default')) {
            return back()->with('error', 'No active subscription found.');
        }

        $subscription = $user->subscription('default');

        if ($subscription->onGracePeriod()) {
            return back()->with('error', 'Subscription is already set to cancel.');
        }

        $subscription->cancel();

        $user->update([
            'plan_expires_at' => $subscription->fresh()->ends_at,
        ]);

        return back()->with('success', 'Subscription will be cancelled at the end of billing period.');
    }

    public function resume()
    {
        $user = Auth::user();
        $subscription = $user->subscription('default');

        if (! $subscription) {
            return back()->with('error', 'No subscription found.');
        }

        if (! $subscription->onGracePeriod()) {
            return back()->with('error', 'Subscription is already active.');
        }

        $subscription->resume();

        $user->update([
            'plan_expires_at' => $this->nextExpiry($user->billing_cycle),
        ]);

        return back()->with('success', 'Subscription resumed successfully.');
    }

    private function resolvePriceId(string $plan, string $billing): ?string
    {
        $prices = [
            'premium' => [
                'monthly' => env('STRIPE_PREMIUM_MONTHLY'),
                'yearly' => env('STRIPE_PREMIUM_YEARLY'),
            ],
            'ultimate' => [
                'monthly' => env('STRIPE_ULTIMATE_MONTHLY'),
                'yearly' => env('STRIPE_ULTIMATE_YEARLY'),
            ],
        ];

        return $prices[$plan][$billing] ?? null;
    }

    private function resolvePlanFromPriceId(string $priceId): ?array
    {
        $map = [
            env('STRIPE_PREMIUM_MONTHLY') => ['plan' => 'premium', 'billing' => 'monthly'],
            env('STRIPE_PREMIUM_YEARLY') => ['plan' => 'premium', 'billing' => 'yearly'],
            env('STRIPE_ULTIMATE_MONTHLY') => ['plan' => 'ultimate', 'billing' => 'monthly'],
            env('STRIPE_ULTIMATE_YEARLY') => ['plan' => 'ultimate', 'billing' => 'yearly'],
        ];

        return $map[$priceId] ?? null;
    }

    private function nextExpiry(?string $billing)
    {
        if ($billing === 'yearly') {
            return now()->addYear();
        }

        return now()->addMonth();
    }

    private function syncPlanData($user, string $plan, ?string $billing, $expiresAt): void
    {
        $user->update([
            'plan' => $plan,
            'billing_cycle' => $billing,
            'plan_expires_at' => $expiresAt,
        ]);
    }
}
