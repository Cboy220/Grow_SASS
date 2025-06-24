<?php

/** -------------------------------------------------------------------------
 * SaaS Frontend Routes
 * -------------------------------------------------------------------------*/
Route::any('/', 'Frontend\Home@index');

//FIX - redirect [admin] who is already logged in
Route::get('/home', function () {
    return redirect('/app-admin/home');
});

//PRICING
Route::any('pricing', 'Frontend\Pricing@index');

//CONATCT US
Route::get('contact', 'Frontend\Contact@index');
Route::post('contact', 'Frontend\Contact@submitForm');

//FAQ
Route::get('faq', 'Frontend\Faq@index');

//PAGES
Route::get('page/{slug}', 'Frontend\Pages@show');

//ACCOUNT - SIGNUP
Route::group(['prefix' => 'account'], function () {
    Route::any("/signup", "Frontend\Signup@index");
    Route::post("/signup", "Frontend\Signup@createAccount");
    Route::any("/login", "Frontend\Login@index");
    Route::post("/login", "Frontend\Login@getAccount");
});
