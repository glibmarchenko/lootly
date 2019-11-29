<script src="https://js.stripe.com/v3/"></script>
<form action="/settings/payment/stripe/subscription" method="post" id="payment-form">
    <div class="form">
        <label for="card-element">
            Credit or debit card
        </label>
        <div id="card-element">
            <!-- A Stripe Element will be inserted here. -->
        </div>
        <div id="plan" class="form-group"></div>
        <div class="form-group">
            <select class="form-control" name="plane" id="subscription_plan">
                <option value="10">month/500</option>
                <option value="11">month/1000</option>
                <option value="12">month/300</option>
            </select>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input class="form-control" type="text" name="email">
        </div>
        <input type="hidden" name="token" id="stripe-token">
        <div>

        </div>
        <!-- Used to display form errors. -->
        <div id="card-errors" role="alert"></div>
    </div>

    <button>Submit Payment</button>
</form>
<script>
    var stripe = Stripe('pk_test_yWGuJFZPo8OLwA3paiEE3V5d');
    // Create an instance of Elements.
    var elements = stripe.elements();


    // Custom styling can be passed to options when creating an Element.
    // (Note that this demo uses a wider set of styles than the guide below.)
    var style = {
        base: {
            color: '#32325d',
            lineHeight: '18px',
            fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '16px',
            '::placeholder': {
                color: '#aab7c4'
            }
        },
        invalid: {
            color: '#fa755a',
            iconColor: '#fa755a'
        }
    };

    // Create an instance of the card Element.
    var card = elements.create('card', {style: style});

    // Add an instance of the card Element into the `card-element` <div>.
    card.mount('#card-element');
    console.log(card);
    // Handle real-time validation errors from the card Element.
    card.addEventListener('change', function (event) {
        var displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });

    // Handle form submission.
    var form = document.getElementById('payment-form');
    form.addEventListener('submit', function (event) {
        event.preventDefault();

        stripe.createToken(card).then(function (result) {
            if (result.error) {
                // Inform the user if there was an error.
                var errorElement = document.getElementById('card-errors');
                errorElement.textContent = result.error.message;
            } else {
                document.getElementById('stripe-token').val(result.token);
                // Send the token to your server.
                stripeTokenHandler(result.token);
            }
        });
    })
</script>