<?php

/** -------------------------------------------------------------------------
 * SaaS Admin Routes
 * -------------------------------------------------------------------------*/
Route::middleware('account')->group(function () {

    //ACCOUNT - SIGNUP
    Route::group(['prefix' => 'settings/account'], function () {
        Route::any("/myaccount", "Account\Myaccount@show");
        Route::get("/packages", "Account\Packages@show");
        Route::get("/payments", "Account\Payments@index");
        Route::get("/notices", "Account\Notices@index");
        Route::get("/email", "Account\Settings\Email@index");
        Route::post("/email/local", "Account\Settings\Email@updateLocal");
        Route::post("/email/smtp", "Account\Settings\Email@updateSMTP");
        Route::any("/close-account", "Account\Myaccount@closeAccount");

        //thank you pages
        Route::get("/thankyou/stripe", "Account\Pay\Stripe@thankYouPage");
        Route::get("/thankyou/paypal", "Account\Pay\Paypal@thankYouPage");
        Route::get("/thankyou/razorpay", "Account\Pay\Razorpay@thankYouPage");
        Route::get("/thankyou/mollie", "Account\Pay\Mollie@thankYouPage");
        Route::get("/thankyou/paystack", "Account\Pay\Paystack@thankYouPage");
        Route::get("/thankyou/razorpay", "Account\Pay\Razorpay@thankYouPage");

        //add route for each payment gateway here
        Route::post("/{subscription}/pay/stripe", "Account\Pay\Stripe@payNowButton");
        Route::post("/{subscription}/pay/paypal", "Account\Pay\Paypal@payNowButton");
        Route::post("/{subscription}/pay/razorpay", "Account\Pay\Razorpay@payNowButton");
        Route::post("/{subscription}/pay/mollie", "Account\Pay\Mollie@payNowButton");
        Route::post("/{subscription}/pay/paystack", "Account\Pay\Paystack@payNowButton");
        Route::post("/{subscription}/pay/razorpay", "Account\Pay\Razorpay@payNowButton");
        Route::post("/{subscription}/pay/offline", "Account\Pay\Offline@payNowDetails");
        Route::get("/{subscription}/pay/razorpay/initiate", "Account\Pay\Razorpay@initiatePayment");

        //change package
        Route::get("/{plan}/change-plan", "Account\Myaccount@changePlan");
        Route::post("/{plan}/change-plan", "Account\Myaccount@updatePlan");

        //attach proof of an offline payment
        Route::any("/proof-of-payment", "Account\Pay\Offline@attachPaymentProof");

    });

});

//ONTIME LOGIN
Route::get("/auth", "Account\Auth\OnetimeAuth@OnetimeAuthentication");