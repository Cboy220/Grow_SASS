"use strict";



/** --------------------------------------------------------------------------------------------------
 *  [stripe][pay now button] initiat ethe button and monitor teh click event and fire a new payment
 *  to stripes checkout page
 * -------------------------------------------------------------------------------------------------*/
$(document).ready(function () {

    //get the public api key & checkout session id, from the button
    var stripe_api_public_key = $("#payment_button_stripe").attr('data-stripe-public-key');
    var stripe_checkout_session = $("#payment_button_stripe").attr('data-stripe-checkout-session');

    //remove loadig class & enable the pay now button
    $("#payment_button_stripe").removeClass('button-loading-annimation');
    $("#payment_button_stripe").prop('disabled', false);


    //on-clicking the button, redirect user to the stripe checkout page
    $(document).on('click', '#payment_button_stripe', function () {
        //set stripe api public key
        var stripe = Stripe(stripe_api_public_key);
        //redirect to stripe checkout page
        stripe.redirectToCheckout({
            sessionId: stripe_checkout_session
        }).then(function (result) {
            //an error occured and stripe could not redirect
        });
    });
});