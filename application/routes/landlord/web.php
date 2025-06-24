<?php

/** -------------------------------------------------------------------------
 * Install Sanity - Installation already been done. Redirect to home
 * -------------------------------------------------------------------------*/
Route::any('install', 'Landlord\Home@index');
Route::any("/install/{anything}", 'Landlord\Home@index')->where('anything', '(.*)');

/** -------------------------------------------------------------------------
 * SaaS Admin Routes
 * -------------------------------------------------------------------------*/
Route::middleware('landlord')->group(function () {

    Route::group(['prefix' => 'app-admin'], function () {

        //HOME
        Route::any('/', 'Landlord\Home@index');
        Route::any('/home', 'Landlord\Home@index');

        //TEST
        Route::any('/test', 'Landlord\Test@index');

        //LOGIN & SIGNUP
        Route::get("/login", "Landlord\Authenticate@logIn")->name('login');
        Route::post("/login", "Landlord\Authenticate@logInAction");
        Route::get("/forgotpassword", "Landlord\Authenticate@forgotPassword");
        Route::post("/forgotpassword", "Landlord\Authenticate@forgotPasswordAction");
        Route::get("/signup", "Landlord\Authenticate@signUp");
        Route::post("/signup", "Landlord\Authenticate@signUpAction");
        Route::get("/resetpassword", "Landlord\Authenticate@resetPassword");
        Route::post("/resetpassword", "Landlord\Authenticate@resetPasswordAction");
        Route::any('logout', function () {
            Auth::logout();
            return redirect('/app-admin/login');
        });

        //CUSTOMERS
        Route::group(['prefix' => 'customers'], function () {
            Route::any("/search", "Landlord\Customers@index");
            Route::get("/{customer}/events", "Landlord\Customers@events")->where('customer', '[0-9]+');
            Route::delete("/{customer}/delete", "Landlord\Customers@destroy")->where('customer', '[0-9]+');
            Route::get("/{customer}/subscription", "Landlord\Customers@showSubscription")->where('customer', '[0-9]+');
            Route::get("/{customer}/update-password", "Landlord\Customers@editPassword")->where('customer', '[0-9]+');
            Route::post("/{customer}/update-password", "Landlord\Customers@updatePassword")->where('customer', '[0-9]+');
            Route::get("/{customer}/set-active", "Landlord\Customers@setStatusActive")->where('customer', '[0-9]+');
            Route::post("/{customer}/set-active", "Landlord\Customers@updateStatusActive")->where('customer', '[0-9]+');
            Route::get("/{customer}/sync-account", "Landlord\Customers@syncAccount")->where('customer', '[0-9]+');
            Route::post("/{customer}/sync-account", "Landlord\Customers@updateSyncAccount")->where('customer', '[0-9]+');
            Route::get("/{customer}/email", "Landlord\Customers@showEmailSettings")->where('customer', '[0-9]+');
            Route::get("/{customer}/updated-email-forwarding", "Landlord\Customers@markEmailSettingsDone")->where('customer', '[0-9]+');
            Route::get("/{customer}/login", "Landlord\Customers@LoginAsCustomer")->where('customer', '[0-9]+');
        });
        Route::resource('customers', 'Landlord\Customers');

        //SUBSCRIPTIONS
        Route::group(['prefix' => 'subscriptions'], function () {
            Route::get("/{subscription}/info", "Landlord\Subscriptions@subscriptionInfo")->where('subscription', '[0-9]+');
            Route::post("/{subscription}/cancel", "Landlord\Subscriptions@cancelSubscription")->where('subscription', '[0-9]+');
            Route::get("/{customer}/create-edit-plan", "Landlord\Subscriptions@createEditPlan")->where('customer', '[0-9]+');
            Route::post("/{customer}/create-edit-plan", "Landlord\Subscriptions@storeUpdatePlan")->where('customer', '[0-9]+');
        });
        Route::resource('subscriptions', 'Landlord\Subscriptions');

        //PAYMENTS
        Route::group(['prefix' => 'payments'], function () {
            Route::any("/search", "Landlord\Payments@index");
        });
        Route::resource('payments', 'Landlord\Payments');

        //OFFLINE PAYMENTS
        Route::get("/offline-payments", "Landlord\OfflinePayments@index");
        Route::delete("/offline-payments/{id}", "Landlord\OfflinePayments@destroy")->where('id', '[0-9]+');

        //PACKAGES
        Route::group(['prefix' => 'packages'], function () {
            Route::any("/search", "Landlord\Packages@index");
            Route::get("/{package}/archive", "Landlord\Packages@archive")->where('package', '[0-9]+');
            Route::get("/{package}/restore", "Landlord\Packages@restore")->where('package', '[0-9]+');
        });
        Route::resource('packages', 'Landlord\Packages');

        //BLOGS
        Route::resource('blogs', 'Landlord\Blogs');

        //FILE UPLOADS
        Route::post("/upload-tinymce-image", "Landlord\Fileupload@saveTinyMCEImage");

        //LOGO UPLOADS
        Route::post("/upload-logo", "Landlord\Fileupload@saveAppLogo");

        //LOGO IMAGES
        Route::post("/upload-image", "Landlord\Fileupload@saveImage");

        //AVATAR FILEUPLOAD
        Route::post("/avatarupload", "Landlord\Fileupload@saveAvatar");

        //FRONTEND
        Route::group(['prefix' => 'frontend'], function () {
            Route::get("/general", "Landlord\Frontend\General@show");
            Route::post("/general", "Landlord\Frontend\General@update")->middleware(['demoModeCheck']);
        });

        //EVENTS
        Route::get("/events", "Landlord\Events@index");

        //TEAM
        Route::resource('team', 'Landlord\Team');

        //SETTINGS
        Route::group(['prefix' => 'settings'], function () {
            Route::get("/general", "Landlord\Settings\General@show");
            Route::post("/general", "Landlord\Settings\General@update")->middleware(['demoModeCheck']);
            Route::get("/domain", "Landlord\Settings\Domain@show");
            Route::post("/domain", "Landlord\Settings\Domain@update")->middleware(['demoModeCheck']);
            Route::get("/company", "Landlord\Settings\Company@show");
            Route::post("/company", "Landlord\Settings\Company@update")->middleware(['demoModeCheck']);
            Route::get("/freetrial", "Landlord\Settings\Freetrial@show");
            Route::post("/freetrial", "Landlord\Settings\Freetrial@update")->middleware(['demoModeCheck']);
            Route::get("/offlinepayments", "Landlord\Settings\Offlinepayments@show");
            Route::post("/offlinepayments", "Landlord\Settings\Offlinepayments@update")->middleware(['demoModeCheck']);
            Route::get("/currency", "Landlord\Settings\Currency@show");
            Route::post("/currency", "Landlord\Settings\Currency@update")->middleware(['demoModeCheck']);
            Route::get("/emailtemplates", "Landlord\Settings\Emailtemplates@show");
            Route::post("/emailtemplates", "Landlord\Settings\Emailtemplates@update")->middleware(['demoModeCheck']);
            Route::get("/emailtemplates/{id}", "Landlord\Settings\Emailtemplates@showTemplate")->where('id', '[0-9]+');
            Route::post("/emailtemplates/{id}", "Landlord\Settings\Emailtemplates@update")->where('id', '[0-9]+')->middleware(['demoModeCheck']);
            Route::get("/email", "Landlord\Settings\Email@show");
            Route::post("/email", "Landlord\Settings\Email@update")->middleware(['demoModeCheck']);
            Route::get("/smtp", "Landlord\Settings\Smtp@show");
            Route::post("/smtp", "Landlord\Settings\Smtp@update")->middleware(['demoModeCheck']);
            Route::get("/testsmtp", "Landlord\Settings\Smtp@testSMTP")->middleware(['demoModeCheck']);
            Route::get("/updates", "Landlord\Settings\Updates@show");
            Route::post("/updates", "Landlord\Settings\Updates@update")->middleware(['demoModeCheck']);
            Route::post("/updates/check", "Landlord\Settings\Updates@checkUpdates")->middleware(['demoModeCheck']);
            Route::get("/logo", "Landlord\Settings\Logo@show");
            Route::get("/logo/uploadlogo", "Landlord\Settings\Logo@edit");
            Route::put("/logo/uploadlogo", "Landlord\Settings\Logo@update")->middleware(['demoModeCheck']);
            Route::get("/cronjob", "Landlord\Settings\Cronjob@show");
            Route::post("/cronjob", "Landlord\Settings\Cronjob@update")->middleware(['demoModeCheck']);
            Route::get("/email/testemail", "Landlord\Settings\Email@testEmail")->middleware(['demoModeCheck']);
            Route::post("/email/testemail", "Landlord\Settings\Email@testEmailAction")->middleware(['demoModeCheck']);
            Route::get("/gateways", "Landlord\Settings\Gateways@show");
            Route::post("/gateways", "Landlord\Settings\Gateways@update")->middleware(['demoModeCheck']);
            Route::get("/database", "Landlord\Settings\Database@show");
            Route::post("/database/user", "Landlord\Settings\Database@updateUser")->middleware(['demoModeCheck']);
            Route::post("/database/cpanel", "Landlord\Settings\Database@updateCpanel")->middleware(['demoModeCheck']);
            Route::post("/database/plesk", "Landlord\Settings\Database@updatePlesk")->middleware(['demoModeCheck']);
            Route::get("/system", "Landlord\Settings\System@show");
            Route::get("/emaillog", "Landlord\Settings\Email@logShow");
            Route::get("/emaillog/{id}", "Landlord\Settings\Email@logRead")->where('id', '[0-9]+');
            Route::delete("/emaillog/{id}", "Landlord\Settings\Email@logDelete")->where('id', '[0-9]+');
            Route::delete("/emaillog/purge", "Landlord\Settings\Email@logPurge");
            Route::get("/updateslog", "Landlord\Settings\Updateslog@logShow");
            Route::get("/updateslog/{id}", "Landlord\Settings\Updateslog@logRead")->where('id', '[0-9]+');
            Route::get("/errorlogs", "Landlord\Settings\Errorlogs@index");
            Route::delete("/errorlogs/delete", "Landlord\Settings\Errorlogs@delete")->where('id', '[0-9]+');
            Route::get("/errorlogs/download", "Landlord\Settings\Errorlogs@download");
            Route::get("/defaults", "Landlord\Settings\Defaults@show");
            Route::post("/defaults", "Landlord\Settings\Defaults@update")->middleware(['demoModeCheck']);

            //payment gateways
            Route::get("/stripe", "Landlord\Settings\Gateways\Stripe@show");
            Route::post("/stripe", "Landlord\Settings\Gateways\Stripe@update")->middleware(['demoModeCheck']);

            Route::get("/paypal", "Landlord\Settings\Gateways\Paypal@show");
            Route::post("/paypal", "Landlord\Settings\Gateways\Paypal@update")->middleware(['demoModeCheck']);

            Route::get("/paystack", "Landlord\Settings\Gateways\Paystack@show");
            Route::post("/paystack", "Landlord\Settings\Gateways\Paystack@update")->middleware(['demoModeCheck']);

            Route::get("/razorpay", "Landlord\Settings\Gateways\Razorpay@show");
            Route::post("/razorpay", "Landlord\Settings\Gateways\Razorpay@update")->middleware(['demoModeCheck']);

            Route::get("/captcha", "Landlord\Settings\Captcha@show");
            Route::post("/captcha", "Landlord\Settings\Captcha@update")->middleware(['demoModeCheck']);
        });

        //FRONTEND
        Route::group(['prefix' => 'frontend'], function () {

            //start
            Route::get("/start", "Landlord\Frontend\Start@show");
            Route::post("/start", "Landlord\Frontend\Start@update")->middleware(['demoModeCheck']);

            //hero
            Route::get("/hero", "Landlord\Frontend\Heroheader@edit");
            Route::post("/hero", "Landlord\Frontend\Heroheader@update")->middleware(['demoModeCheck']);

            //image-content sections
            Route::get("/section/{id}/image-content", "Landlord\Frontend\ImageContent@edit")->where('id', '[0-9]+');
            Route::post("/section/{id}/image-content", "Landlord\Frontend\ImageContent@update")->where('id', '[0-9]+')->middleware(['demoModeCheck']);

            //list features section
            Route::get("/section/{id}/list", "Landlord\Frontend\SectionList@edit")->where('id', '[0-9]+');
            Route::post("/section/{id}/list", "Landlord\Frontend\SectionList@update")->where('id', '[0-9]+')->middleware(['demoModeCheck']);

            //image-content sections
            Route::get("/section/{id}/feature", "Landlord\Frontend\SectionFeature@edit");
            Route::post("/section/{id}/feature", "Landlord\Frontend\SectionFeature@update")->middleware(['demoModeCheck']);

            //image-content sections
            Route::get("/section/{id}/cta", "Landlord\Frontend\SectionCTA@edit");
            Route::post("/section/{id}/cta", "Landlord\Frontend\SectionCTA@update")->middleware(['demoModeCheck']);

            //list features section
            Route::get("/section/splash", "Landlord\Frontend\SectionSplash@edit");
            Route::post("/section/splash", "Landlord\Frontend\SectionSplash@update")->middleware(['demoModeCheck']);
            Route::get("/section/{id}/splash", "Landlord\Frontend\SectionSplash@editImage")->where('id', '[0-9]+');
            Route::post("/section/{id}/splash", "Landlord\Frontend\SectionSplash@updateImage")->where('id', '[0-9]+')->middleware(['demoModeCheck']);

            //mainmenu
            Route::resource('mainmenu', 'Landlord\Frontend\MainMenu');
            Route::post("/mainmenu/update-positions", "Landlord\Frontend\MainMenu@updatePositions")->middleware(['demoModeCheck']);

            //pricing
            Route::get("/pricing", "Landlord\Frontend\Pricing@edit");
            Route::post("/pricing", "Landlord\Frontend\Pricing@update")->middleware(['demoModeCheck']);

            //contact us
            Route::get("/contact", "Landlord\Frontend\Contact@edit");
            Route::post("/contact", "Landlord\Frontend\Contact@update")->middleware(['demoModeCheck']);

            //faq
            Route::resource('faq', 'Landlord\Frontend\Faq');
            Route::post("/faq/update-positions", "Landlord\Frontend\Faq@updatePositions");
            Route::post("/faq/update", "Landlord\Frontend\Faq@updateDetails")->middleware(['demoModeCheck']);

            //pages
            Route::resource('pages', 'Landlord\Frontend\Pages');

            //footer
            Route::get("/footer", "Landlord\Frontend\Footer@edit");
            Route::post("/footer", "Landlord\Frontend\Footer@update")->middleware(['demoModeCheck']);

            //footer cta
            Route::get("/footercta", "Landlord\Frontend\Footercta@edit");
            Route::post("/footercta", "Landlord\Frontend\Footercta@update")->middleware(['demoModeCheck']);

            //logo
            Route::get("/logo", "Landlord\Frontend\Logo@show");
            Route::get("/logo/uploadlogo", "Landlord\Frontend\Logo@edit");
            Route::put("/logo/uploadlogo", "Landlord\Frontend\Logo@update")->middleware(['demoModeCheck']);

            //meta tags
            Route::get("/metatags", "Landlord\Frontend\Metatags@edit");
            Route::post("/metatags", "Landlord\Frontend\Metatags@update")->middleware(['demoModeCheck']);

            //customer code
            Route::get("/customcode", "Landlord\Frontend\Customcode@edit")->middleware(['demoModeCheck']);
            Route::post("/customcode", "Landlord\Frontend\Customcode@update")->middleware(['demoModeCheck']);

            //signup
            Route::get("/signup", "Landlord\Frontend\Signup@edit");
            Route::post("/signup", "Landlord\Frontend\Signup@update")->middleware(['demoModeCheck']);

            //login
            Route::get("/login", "Landlord\Frontend\Login@edit");
            Route::post("/login", "Landlord\Frontend\Login@update")->middleware(['demoModeCheck']);
        });

        //ADMIN USER
        Route::group(['prefix' => 'users'], function () {
            Route::get("/preferences/leftmenu", "Landlord\Users@preferenceMenu");
            Route::get("/profile", "Landlord\Profile@show");
            Route::put("/profile", "Landlord\Profile@update")->middleware(['demoModeCheck']);
            Route::get("/avatar", "Landlord\Users@avatar");
            Route::put("/avatar", "Landlord\Users@updateAvatar")->middleware(['demoModeCheck']);
        });

        //AUTOCOMPLETE AJAX FEED
        Route::group(['prefix' => 'feed'], function () {
            Route::get("/customers", "Landlord\Feed@customerNames");
        });

        //PAYMENT GATEWAY WEB HOOKS
        Route::group(['prefix' => 'webhooks'], function () {

            //NOTE - must add any new routes (names) to this file to avoid error - ..\Middleware\General\StripHtmlTags.php
            Route::any("/stripe", "Landlord\Webhooks\Stripe\Stripe@index")->name('webhooks-stripe');
            Route::any("/paypal", "Landlord\Webhooks\Paypal\Paypal@index")->name('webhooks-paypal');
            Route::any("/paystack", "Landlord\Webhooks\Paystack\Paystack@index")->name('webhooks-paystack');
            //Route::any("/razorpay", "Landlord\Webhooks\Razorpay\Razorpay@index")->name('webhooks-razorpay');
            Route::any("/razorpay2", "Landlord\Webhooks\Razorpay\Razorpay@index")->name('webhooks-razorpay'); //temp

            //NOTE - must add any new routes (names) to this file to avoid error - ..\Middleware\General\StripHtmlTags.php

        });

    });

});

/** -------------------------------------------------------------------------
 * Installation Routes
 * -------------------------------------------------------------------------*/
Route::group(['prefix' => 'setup', 'as' => 'setup'], function () {
    //requirements
    Route::get("/requirements", "Setup\Setup@checkRequirements");
    //server phpinfo()
    Route::get("/serverinfo", "Setup\Setup@serverInfo");
    //database
    Route::get("/database", "Setup\Setup@showDatabase");
    Route::post("/database", "Setup\Setup@updateDatabase");
    //settings
    Route::get("/settings", "Setup\Setup@showSettings");
    Route::post("/settings", "Setup\Setup@updateSettings");
    //admin user
    Route::get("/adminuser", "Setup\Setup@showUser");
    Route::post("/adminuser", "Setup\Setup@updateUser");
    //load first page -put this as last item
    Route::any("/", "Setup\Setup@index");
});
