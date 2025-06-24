<?php

//redirect all 'none setup' url's to the [/setup] url
Route::any("/", "Install\Install@index");
Route::any("/app-admin", "Install\Install@index");
Route::any("/app-admin/{anything}", "Install\Install@index")->where('anything', '(.*)');

//all other setup routes
Route::group(['prefix' => 'install', 'as' => 'install'], function () {
    //requirements
    Route::post("/requirements", "Install\Install@checkRequirements")->middleware('memory');;
    //server phpinfo()
    Route::get("/serverinfo", "Install\Install@serverInfo");
    //database
    Route::get("/database", "Install\Install@showDatabase");
    Route::post("/database/mysql", "Install\Install@updateDatabaseMySQL");
    Route::post("/database/cpanel", "Install\Install@updateDatabaseCpanel");
    Route::post("/database/plesk", "Install\Install@updateDatabasePlesk");

    //settings
    Route::get("/settings", "Install\Install@showSettings");
    Route::post("/settings", "Install\Install@updateSettings");
    //admin user
    Route::get("/adminuser", "Install\Install@showUser");
    Route::post("/adminuser", "Install\Install@updateUser");
    //load first page -put this as last item
    Route::any("/", "Install\Install@index");
});