<!-- STRIPE PAY NOWBUTTON-->

<!--STRIPE PAYMENT BUTTON-->
<button type="button" class="btn waves-effect waves-light btn-block btn-danger button-loading-annimation"
    id="payment_button_stripe" disabled data-stripe-public-key="{{ $payload['settings_stripe_public_key'] }}"
    data-stripe-checkout-session="{{ $payload['checkout_session_id'] }}">@lang('lang.pay_now')</button>

<!--STRIPE JS-->
<script src="https://js.stripe.com/v3/"></script>

<!--STRIPE-->
<script src="/public/js/landlord/dynamic/stripe/checkout.stripe.js?v={{ config('system.versioning') }}"></script>