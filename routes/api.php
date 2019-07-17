<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login');
    Route::post('signup', 'AuthController@signup');

    Route::group([
        'middleware' => 'auth:api'
    ], function() {
        Route::get('logout', 'AuthController@logout');
        Route::get('user', 'AuthController@user');
    });
});



Route::post('searchCategoriesByName', 'CategoryController@searchByName');
Route::post('getAllCategories', 'CategoryController@getAllCategories');

Route::post('addNewProject', 'ProjectController@addNewProject');
Route::post('getAllProjectsByUser', 'ProjectController@getAllProjectsByUser');
Route::post('addOrderToProject', 'ProjectController@addOrderToProject');
Route::post('openProject', 'ProjectController@openProject');
Route::post('closeProject', 'ProjectController@closeProject');

Route::post('getAllWorkersPaginated', 'UserController@getAllWorkersPaginated');
Route::post('changePersonalInfo', 'UserController@changePersonalInfo');
Route::post('changePassword', 'UserController@changePassword');

Route::post('architectSendRequest', 'ArchitectRequestController@architectSendRequest');
