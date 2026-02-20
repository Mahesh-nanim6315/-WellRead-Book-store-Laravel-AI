<!DOCTYPE html>
<html>
<head>
    <title>Pay with PayPal</title>

    <script src="https://www.paypal.com/sdk/js?client-id={{ config('paypal.sandbox.client_id') }}&currency=USD"></script>
</head>
<body>

<h2>🅿️ Pay with PayPal</h2>

<div id="paypal-button-container"></div>

<script>
paypal.Buttons({
    createOrder: function (data, actions) {
        return actions.order.create({
            purchase_units: [{
                amount: {
                    value: '{{ $order->total_amount }}'
                }
            }]
        });
    },
    onApprove: function (data, actions) {
        return actions.order.capture().then(function (details) {
            alert('Payment completed by ' + details.payer.name.given_name);
        });
    }
}).render('#paypal-button-container');
</script>

</body>
</html>
