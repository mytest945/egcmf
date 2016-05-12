<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('home');
});

// Cms pages
Route::get('/cms', function () {
    return redirect('/cms');
});

Route::get('cms', 'CmsController@index');
Route::get('cms/{slug}', 'CmsController@showPost');

/*
Route::auth();
Route::get('/home', 'HomeController@index');
*/
Route::group(['middleware'], function () {

    Route::auth();
    Route::get('home', 'HomeController@index');

    Route::get('admin/login', 'Admin\AuthController@getLogin');
    Route::post('admin/login', 'Admin\AuthController@postLogin');
    //Route::get('admin/register', 'Admin\AuthController@getRegister');
    //Route::post('admin/register', 'Admin\AuthController@postRegister');
    Route::get('admin/logout', 'Admin\AuthController@getLogout');

    Route::get('admin', 'AdminController@index');

});
