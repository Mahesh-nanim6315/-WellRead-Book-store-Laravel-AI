@include('common.header')

@section('content')
<div class="plans-container">

    <div class="plans-header">
        <h1>Choose Your Reading Plan</h1>
        <p>Unlock unlimited stories and immersive audiobooks.</p>
    </div>

    <div class="billing-toggle">
        <button id="monthlyBtn" class="active">Monthly</button>
        <button id="yearlyBtn">Yearly</button>
    </div>

    <div class="plans-grid">
        @foreach($plans as $plan)
            @php
                $planKey = strtolower(explode(' ', $plan['name'])[0]);
                $isCurrentPlan = auth()->check() && auth()->user()->plan === $planKey;
            @endphp

            <div class="plan-card {{ isset($plan['popular']) ? 'popular' : '' }}">
                @if(isset($plan['popular']))
                    <div class="popular-badge">Most Popular</div>
                @endif

                <h2>{{ $plan['name'] }}</h2>

                <div class="price">
                    <span class="amount" data-monthly="{{ $plan['monthly'] }}" data-yearly="{{ $plan['yearly'] }}">
                        ${{ $plan['monthly'] }}
                    </span>
                    <small>/month</small>
                </div>

                <ul>
                    @foreach($plan['features'] as $feature)
                        <li>&#10004; {{ $feature }}</li>
                    @endforeach
                </ul>

                @if($isCurrentPlan)
                    <div class="current-plan-badge">Current Plan</div>
                @endif

                <form method="POST" action="{{ route('subscription.checkout') }}">
                    @csrf
                    <input type="hidden" name="plan" value="{{ $planKey }}">
                    <input type="hidden" name="billing_cycle" value="monthly">

                    @php
                        $buttonText = 'Choose Plan';

                        if ($isCurrentPlan) {
                            $buttonText = 'Current Plan';
                        } elseif (auth()->check() && $planKey === 'free' && auth()->user()->plan !== 'free') {
                            $buttonText = 'Downgrade to Free';
                        } elseif (auth()->check() && auth()->user()->plan === 'premium' && $planKey === 'ultimate') {
                            $buttonText = 'Upgrade to Ultimate';
                        }
                    @endphp

                    <button type="submit" class="subscribe-btn" {{ $isCurrentPlan ? 'disabled' : '' }}>
                        {{ $buttonText }}
                    </button>
                </form>
            </div>
        @endforeach
    </div>
</div>
@include('common.footer')

<script>
    const monthlyBtn = document.getElementById('monthlyBtn');
    const yearlyBtn = document.getElementById('yearlyBtn');
    const prices = document.querySelectorAll('.amount');

    monthlyBtn.onclick = function() {
        monthlyBtn.classList.add('active');
        yearlyBtn.classList.remove('active');

        prices.forEach(p => {
            p.textContent = '$' + p.dataset.monthly;
        });

        document.querySelectorAll('input[name="billing_cycle"]').forEach(i => {
            i.value = 'monthly';
        });
    };

    yearlyBtn.onclick = function() {
        yearlyBtn.classList.add('active');
        monthlyBtn.classList.remove('active');

        prices.forEach(p => {
            p.textContent = '$' + p.dataset.yearly;
        });

        document.querySelectorAll('input[name="billing_cycle"]').forEach(i => {
            i.value = 'yearly';
        });
    };
</script>
