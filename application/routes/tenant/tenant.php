<?php

/** -------------------------------------------------------------------------
 * TENANT ACCOUNT LIMITATIONS
 * -------------------------------------------------------------------------*/

//suspended account
Route::any('home', 'Home@index')->name('home')->middleware(['accountStatus']);
Route::get('clients', 'Clients@index')->middleware(['accountStatus']);
Route::get('projects', 'Projects@index')->middleware(['accountStatus']);
Route::get('tasks', 'Tasks@index')->middleware(['accountStatus']);
Route::get('team', 'Team@index')->middleware(['accountStatus']);
Route::get('invoices', 'Invoices@index')->middleware(['accountStatus']);
Route::get('estimates', 'Estimates@index')->middleware(['accountStatus']);
Route::get('payments', 'Payments@index')->middleware(['accountStatus']);
Route::get('expenses', 'Expenses@index')->middleware(['accountStatus']);
Route::get('subscriptions', 'Subscriptions@index')->middleware(['accountStatus']);
Route::get('tickets', 'Tickets@index')->middleware(['accountStatus']);
Route::get('leads', 'Leads@index')->middleware(['accountStatus']);



//Limits
Route::post("/clients", "Clients@store")->middleware(['accountLimitsClients']);
Route::post("/projects", "Projects@store")->middleware(['accountLimitsProjects']);
Route::post("/team", "Team@store")->middleware(['accountLimitsTeam']);