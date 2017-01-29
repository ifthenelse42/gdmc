<?php
/** Routes de développement */
Route::get('/', function() {
	return view('index');
});

Route::get("test", function() {
    return view('test');
});

Route::get("login", function() {
    return view('login');
});

Route::get("election","ElectionController@index");
Route::get("election/printable","ElectionController@printable");
Route::post("election","ElectionController@insert");


Route::get("election/brut","ElectionController@indexBrut");
Route::post("election/brut","ElectionController@rechercheBrut");
Route::get("election/brut/supprimer/{id}","ElectionController@supprimer");

/**
 * ----> Module Réunion <----
 * -------------------------
 * Get requests
 * Post requests
 */

Route::get("reunion", "ReunionController@index");
Route::get("reunion/add", "ReunionController@add");
Route::get("reunion/delete/{id}", "ReunionController@delete");

/**
 * ----> Module Action <----
 * -------------------------
 * Get requests
 * Post requests
 */
Route::get("action", "ActionController@index");
Route::get("action/{id}", "ActionController@getAction");
Route::get("action/stats", "ActionController@stats");
Route::get("action/get/jour-restant", "ActionController@getJourRestant");
Route::get("action/alerte", "ActionController@getAlert");
Route::get("action/alerte/{id}", "ActionController@actionAlert");
Route::get("action/delete/{id}", "ActionController@delete");

Route::post("action/edit/nom", "ActionController@editActionTitre");
Route::post("action/edit/description", "ActionController@editActionDescription");
Route::post("action/edit/date-creation", "ActionController@editActionDateCreation");
Route::post("action/edit/date-realisation", "ActionController@editActionDateRealisation");
Route::post("action/edit/date-butoir", "ActionController@editActionDateButoir");
Route::post("action/ajout", "ActionController@ajoutAction");

/**
 * ----> Module Budget <----
 * -------------------------
 * Get requests
 * Post requests
 */
Route::get("budget", "BudgetController@index");
Route::get("budget/add/{id}/{year}", "BudgetController@add");
Route::get("budget/add/year/{year}", "BudgetController@addYear");
Route::get("budget/delete/{id}", "BudgetController@delete");
Route::get("budget/delete/year/{year}", "BudgetController@deleteYear");
Route::get("budget/total/{id}", "BudgetController@getTotal");
Route::get("budget/depense/{id}", "BudgetController@getDepense");
Route::get("budget/ajout/depense/{id}", "BudgetController@addDepense");
Route::get("budget/delete/depense/{id}", "BudgetController@deleteDepense");
Route::get("budget/board", "BudgetController@board");

Route::post("budget/edit", "BudgetController@edit");
Route::post("budget/edit/depense", "BudgetController@editDepense");

/**
 * ----> Module Dashboard <----
 * ----------------------------
 * Get requests
 * Post requests
 */
Route::get("dashboard", "DashboardController@index");
Route::get("dashboard/stats/year/{year}", "DashboardController@statsYear");
Route::get("dashboard/categories", "DashboardController@indexCategories");
Route::get("dashboard/add/categories", "DashboardController@addCategories");
Route::get("dashboard/services", "DashboardController@indexServices");
Route::get("dashboard/add/service", "DashboardController@addService");
Route::get("dashboard/agents", "DashboardController@indexAgents");
Route::get("dashboard/delete/agent/{id}", "DashboardController@deleteAgent");
Route::get("dashboard/holidays", "DashboardController@indexHolidays");
Route::get("dashboard/delete/holiday/{id}", "DashboardController@deleteHoliday");
Route::get("dashboard/delete/service/{id}", "DashboardController@deleteService");
Route::get("dashboard/add/category", "DashboardController@addCategory");
Route::get("dashboard/delete/category/{id}", "DashboardController@deleteCategory");
Route::get("dashboard/{year}/{month}", "DashboardController@access");
Route::get("dashboard/{year}", "DashboardController@accessYear");
Route::get("dashboard/add/{year}", "DashboardController@add");
Route::get("dashboard/add/{year}/month", "DashboardController@addMonth");

Route::post("dashboard/add/agent", "DashboardController@addAgent");
Route::post("dashboard/add/holiday", "DashboardController@addHoliday");
Route::post("dashboard/edit/service", "DashboardController@editService");
Route::post("dashboard/edit/category", "DashboardController@editCategory");
Route::post("dashboard/edit/amount", "DashboardController@editAmount");
Route::post("dashboard/edit/amount/service", "DashboardController@editAmountService");

/** XHR CSRF */
Route::get('refresh-csrf', function(){
    return csrf_token();
});

/** Hack désactivant le formattage de certains nombres */
Route::get('unformat/{number}', function($number) {
    $resultat = [
        'number' => number_format($number)
    ];

    return json_encode($resultat);
});