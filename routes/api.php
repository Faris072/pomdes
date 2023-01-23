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
            Route::get('/','App\Http\Controllers\API\ProfileController@show');
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
            Route::get('/select-list','App\Http\Controllers\API\ProvinceController@select');
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
            Route::get('/select-list','App\Http\Controllers\API\CityController@select');
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
        Route::group(['prefix' => 'invoice-pomdes'],function(){
            Route::group(['prefix' => 'file'],function(){
                Route::post('/{id}','App\Http\Controllers\API\InvoicePomdesController@upload_files');
            });
            Route::post('update-file/{id}','App\Http\Controllers\API\InvoicePomdesController@update_upload');
            Route::post('store-file','App\Http\Controllers\API\InvoicePomdesController@store_upload');
            Route::put('restore-trash/{id}','App\Http\Controllers\API\InvoicePomdesController@restore_trash');
            Route::get('show-trash/{id}','App\Http\Controllers\API\InvoicePomdesController@show_trash');
            Route::get('get-trash','App\Http\Controllers\API\InvoicePomdesController@get_trash');
            Route::delete('delete/{id}','App\Http\Controllers\API\InvoicePomdesController@delete');
            Route::post('/','App\Http\Controllers\API\InvoicePomdesController@store');
            Route::put('/{id}','App\Http\Controllers\API\InvoicePomdesController@update');
            Route::get('/','App\Http\Controllers\API\InvoicePomdesController@get');
            Route::get('/{id}','App\Http\Controllers\API\InvoicePomdesController@show');
            Route::delete('/{id}','App\Http\Controllers\API\InvoicePomdesController@destroy');
        });
        Route::group(['prefix'=>'invoice-pusat'],function(){
            Route::post('/','App\Http\Controllers\API\InvoicePusatController@store');
        });
        Route::put('trash/{id}','App\Http\Controllers\API\TransactionController@restore_trash');
        Route::put('approve-submission/{id}','App\Http\Controllers\API\TransactionController@approve_submission');
        Route::get('trash','App\Http\Controllers\API\TransactionController@get_trash');
        Route::get('trash/{id}','App\Http\Controllers\API\TransactionController@show_trash');
        Route::delete('delete/{id}','App\Http\Controllers\API\TransactionController@delete');
        Route::post('/','App\Http\Controllers\API\TransactionController@store');
        Route::get('/','App\Http\Controllers\API\TransactionController@get');
        Route::get('/{id}','App\Http\Controllers\API\TransactionController@show');
        Route::put('/{id}','App\Http\Controllers\API\TransactionController@update');
        Route::delete('/{id}','App\Http\Controllers\API\TransactionController@destroy');
    });
});
