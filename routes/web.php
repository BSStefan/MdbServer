<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/get-images/{page}', 'StartController@getTopImage');

Route::group(['prefix' => 'auth'], function (){
    Route::get('{provider}/login', 'User\AuthController@redirectToProvider');
    Route::get('{provider}/callback', 'User\AuthController@handleProviderCallback');
    Route::group(['prefix' => 'mdb'], function (){
    });
});