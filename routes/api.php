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
        Route::delete('/{id}','App\Http\Controllers\API\AuthController@kill');
        Route::delete('delete/{id}','App\Http\Controllers\API\AuthController@delete');
        Route::get('get-trashed','App\Http\Controllers\API\AuthController@get_trashed');
        Route::get('show-trashed/{id}','App\Http\Controllers\API\AuthController@show_trashed');
        Route::put('restore/{id}','App\Http\Controllers\API\AuthController@restore');

    });

    Route::group(['prefix' => 'profile'], function(){
        Route::put('/','App\Http\Controllers\API\ProfileController@update');
    });

    Route::group(['prefix' => 'location'],function(){

        Route::group(['prefix' => 'city'],function(){
            Route::post('/','App\Http\Controllers\API\CityController@store');
        });

        Route::group(['prefix' => 'province'],function(){
            Route::post('/','App\Http\Controllers\API\ProvinceController@store');
            Route::get('/','App\Http\Controllers\API\ProvinceController@get');
            Route::get('/{id}','App\Http\Controllers\API\ProvinceController@show');
            Route::put('/{id}','App\Http\Controllers\API\ProvinceController@update');
            Route::delete('delete/{id}','App\Http\Controllers\API\ProvinceController@delete');
            Route::delete('/{id}','App\Http\Controllers\API\ProvinceController@destroy');
        });

    });
});
