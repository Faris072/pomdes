<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('login','App\Http\Controllers\API\AuthController@login');

Route::group(['middleware' => 'auth:api'],function(){

    Route::post('me', 'App\Http\Controllers\API\AuthController@me');
    Route::post('refresh', 'App\Http\Controllers\API\AuthController@refresh');
    Route::post('logout', 'App\Http\Controllers\API\AuthController@logout');

    Route::group(['prefix' => 'users'], function(){
        Route::post('/','App\Http\Controllers\API\AuthController@register');
        Route::get('/','App\Http\Controllers\API\AuthController@get_users');
        Route::get('/{id}','App\Http\Controllers\API\AuthController@show_user');
        Route::put('/{id}','App\Http\Controllers\API\AuthController@update');
        Route::delete('/{id}','App\Http\Controllers\API\AuthController@destroy');
        Route::delete('delete/{id}','App\Http\Controllers\API\AuthController@delete');
        Route::get('get-trashed','App\Http\Controllers\API\AuthController@get_trashed');
        Route::get('show-trashed/{id}','App\Http\Controllers\API\AuthController@show_trashed');
        Route::put('restore/{id}','App\Http\Controllers\API\AuthController@restore');
    });
});
