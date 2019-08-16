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

Route::post('sendContactForm', 'MailController@sendContactForm');

Route::post('searchCategoriesByName', 'CategoryController@searchByName');
Route::post('getAllCategories', 'CategoryController@getAllCategories');

Route::post('addNewProject', 'ProjectController@addNewProject');
Route::post('getAllProjectsByUser', 'ProjectController@getAllProjectsByUser');
Route::post('addOrderToProject', 'ProjectController@addOrderToProject');
Route::post('openProject', 'ProjectController@openProject');
Route::post('closeProject', 'ProjectController@closeProject');
Route::post('getAllUsersOnProject', 'ProjectController@getAllUsersOnProject');
Route::post('getProjectOrderForSpecificUser', 'ProjectController@getProjectOrderForSpecificUser');

Route::post('getAllWorkersPaginated', 'UserController@getAllWorkersPaginated');
Route::post('changePersonalInfo', 'UserController@changePersonalInfo');
Route::post('changePassword', 'UserController@changePassword');
Route::post('getAllUserFiles', 'UserController@getAllUserFiles');
Route::post('getAllWorkerProjects', 'UserController@getAllWorkerProjects');
Route::post('addRatingOnUser', 'UserController@addRatingOnUser');

Route::post('architectSendRequest', 'ArchitectRequestController@architectSendRequest');
Route::post('answerRequest', 'ArchitectRequestController@answerRequest');

// ---------------------------------

Route::group([
    'prefix' => 'admin'
], function () {
    Route::post('getAllEditableContent', 'EditableController@getAllEditableContent');
    Route::post('changeMainPage', 'EditableController@changeMainPage');
    Route::post('changeTerms', 'EditableController@changeTerms');
    Route::post('changeAboutUs', 'EditableController@changeAboutUs');
    Route::post('changePrivacyPolicy', 'EditableController@changePrivacyPolicy');
    Route::post('changeHowItWorksArchitect', 'EditableController@changeHowItWorksArchitect');
    Route::post('changeHowItWorksWorker', 'EditableController@changeHowItWorksWorker');

    Route::post('getAllUsers', 'AdminController@getAllUsers');
    Route::post('getUserCounter', 'AdminController@getUserCounter');
    Route::post('editUser', 'AdminController@editUser');
    Route::post('deleteUser', 'AdminController@deleteUser');

    Route::post('getAllCategories', 'admin\CategoryController@getAllCategories');
    Route::post('deleteCategory', 'admin\CategoryController@deleteCategory');
    Route::post('addNewCategory', 'admin\CategoryController@addNewCategory');
    Route::post('deleteSubcategory', 'admin\CategoryController@deleteSubcategory');
    Route::post('editCategory', 'admin\CategoryController@editCategory');
    Route::post('getSingleCategory', 'admin\CategoryController@getSingleCategory');

    Route::post('getAllProjects', 'admin\ProjectController@getAllProjects');
    Route::post('getSingleProject', 'admin\ProjectController@getSingleProject');
    Route::post('deleteProject', 'admin\ProjectController@deleteProject');
    Route::post('editProject', 'admin\ProjectController@editProject');

    Route::post('getAllOrders', 'admin\OrderController@getAllOrders');
    Route::post('deleteOrder', 'admin\OrderController@deleteOrder');
    Route::post('changeOrder', 'admin\OrderController@changeOrder');
    Route::post('editOrder', 'admin\OrderController@editOrder');

    Route::post('getAllUsers', 'admin\UserController@getAllUsers');
    Route::post('getUserCounter', 'admin\UserController@getUserCounter');
    Route::post('editUser', 'admin\UserController@editUser');
    Route::post('deleteUser', 'admin\UserController@deleteUser');
    Route::post('getAllComments', 'admin\UserController@getAllComments');
});

