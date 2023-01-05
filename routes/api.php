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

        Route::group(['prefix' => 'profile'], function(){
            Route::put('/','App\Http\Controllers\API\ProfileController@update');
            Route::post('/photo','App\Http\Controllers\API\ProfileController@photo');
        });

        Route::delete('delete/{id}','App\Http\Controllers\API\AuthController@delete');
        Route::get('get-trashed','App\Http\Controllers\API\AuthController@get_trashed');
        Route::get('show-trashed/{id}','App\Http\Controllers\API\AuthController@show_trashed');
        Route::put('restore/{id}','App\Http\Controllers\API\AuthController@restore');
        Route::post('/','App\Http\Controllers\API\AuthController@register');
        Route::get('/','App\Http\Controllers\API\AuthController@get_users');
        Route::get('/{id}','App\Http\Controllers\API\AuthController@show_user');
        Route::put('/{id}','App\Http\Controllers\API\AuthController@update');
        Route::delete('/{id}','App\Http\Controllers\API\AuthController@kill');

    });

    Route::group(['prefix' => 'location'],function(){

        Route::group(['prefix' => 'province'],function(){
            Route::get('/get-trash','App\Http\Controllers\API\ProvinceController@get_trash');
            Route::get('show-trash/{id}','App\Http\Controllers\API\ProvinceController@show_trash');
            Route::put('restore/{id}','App\Http\Controllers\API\ProvinceController@restore');
            Route::delete('delete/{id}','App\Http\Controllers\API\ProvinceController@delete');
            Route::post('/','App\Http\Controllers\API\ProvinceController@store');
            Route::get('/','App\Http\Controllers\API\ProvinceController@get');
            Route::get('/{id}','App\Http\Controllers\API\ProvinceController@show');
            Route::put('/{id}','App\Http\Controllers\API\ProvinceController@update');
            Route::delete('/{id}','App\Http\Controllers\API\ProvinceController@destroy');
        });

        Route::group(['prefix' => 'city'],function(){
            Route::get('/get-trash','App\Http\Controllers\API\CityController@get_trash');
            Route::get('show-trash/{id}','App\Http\Controllers\API\CityController@show_trash');
            Route::delete('delete/{id}','App\Http\Controllers\API\CityController@delete');
            Route::put('restore/{id}','App\Http\Controllers\API\CityController@restore');
            Route::post('/','App\Http\Controllers\API\CityController@store');
            Route::get('/','App\Http\Controllers\API\CityController@get');
            Route::get('/{id}','App\Http\Controllers\API\CityController@show');
            Route::put('/{id}','App\Http\Controllers\API\CityController@update');
            Route::delete('{id}','App\Http\Controllers\API\CityController@destroy');
        });

    });

    Route::group(['prefix' => 'transaction'], function(){
        Route::get('trash','App\Http\Controllers\API\TransactionController@get_trash');
        Route::get('trash/{id}','App\Http\Controllers\API\TransactionController@show_trash');
        Route::post('/','App\Http\Controllers\API\TransactionController@store');
        Route::get('/','App\Http\Controllers\API\TransactionController@get');
        Route::get('/{id}','App\Http\Controllers\API\TransactionController@show');
        Route::put('/{id}','App\Http\Controllers\API\TransactionController@update');
        Route::delete('/{id}','App\Http\Controllers\API\TransactionController@delete');
    });
});
