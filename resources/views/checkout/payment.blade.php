@include('common.header')

<div class="payment-wrapper">
    <h2>💳 Choose Payment Method</h2>

    <div class="payment-card">

        <form method="POST" action="{{ route('payment.process', $order->id) }}">
            @csrf

            <label class="payment-option">
                <input type="radio" name="payment_method" value="stripe" checked>
                <span>💳 Credit / Debit Card (Stripe)</span>
            </label>

            <label class="payment-option">
                <input type="radio" name="payment_method" value="paypal">
                <span>🅿️ PayPal Wallet / Card</span>
            </label>

            <label class="payment-option">
                <input type="radio" name="payment_method" value="cod">
                <span>🚚 Cash on Delivery</span>
            </label>

            <button type="submit" class="pay-btn">
                Continue to Payment →
            </button>
        </form>
        <div style="margin-top: 20px; text-align: center;">
            <a href="{{ route('checkout.index') }}" style="color: #666;">
                ← Back to Checkout
            </a>
        </div>

    </div>
</div>

@include('common.footer')

