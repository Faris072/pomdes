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
            Route::get('/render-photo/{id}','App\Http\Controllers\API\ProfileController@render_gambar')->name('render-gambar-profile');
            Route::post('/photo','App\Http\Controllers\API\ProfileController@photo');
            Route::put('/','App\Http\Controllers\API\ProfileController@update');
            Route::get('/','App\Http\Controllers\API\ProfileController@show');
        });

        Route::get('/supplier/{id}','App\Http\Controllers\API\AuthController@show_supplier');
        Route::delete('delete/{id}','App\Http\Controllers\API\AuthController@delete');
        Route::get('get-trashed','App\Http\Controllers\API\AuthController@get_trashed');
        Route::put('/switch-status/{id}','App\Http\Controllers\API\AuthController@switch_status');
        Route::get('show-trashed/{id}','App\Http\Controllers\API\AuthController@show_trashed');
        Route::get('select-pomdes','App\Http\Controllers\API\AuthController@select_pomdes');
        Route::get('select-pusat','App\Http\Controllers\API\AuthController@select_pusat');
        Route::put('restore/{id}','App\Http\Controllers\API\AuthController@restore');
        Route::put('reset-password/{id}','App\Http\Controllers\API\AuthController@reset_password');
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
                Route::delete('/{id}','App\Http\Controllers\API\InvoicePomdesController@delete_file');
            });
            Route::get('render-file/{id}','App\Http\Controllers\API\InvoicePomdesController@render_file')->name('render-additional-cost-files');
            Route::put('restore-trash/{id}','App\Http\Controllers\API\InvoicePomdesController@restore_trash');
            Route::get('show-trash/{id}','App\Http\Controllers\API\InvoicePomdesController@show_trash');
            Route::get('get-trash','App\Http\Controllers\API\InvoicePomdesController@get_trash');
            Route::delete('delete/{id}','App\Http\Controllers\API\InvoicePomdesController@delete');
            Route::post('/','App\Http\Controllers\API\InvoicePomdesController@store');
            Route::get('/','App\Http\Controllers\API\InvoicePomdesController@get');
            Route::get('/{id}','App\Http\Controllers\API\InvoicePomdesController@show');
            Route::delete('/{id}','App\Http\Controllers\API\InvoicePomdesController@destroy');
        });
        Route::group(['prefix' => 'delivery'], function(){
            Route::put('set-arrived/{id}', 'App\Http\Controllers\API\TransactionController@set_arrived');
            Route::put('send-delivery/{id}', 'App\Http\Controllers\API\TransactionController@send_delivery');
            Route::get('render-file/{id}', 'App\Http\Controllers\API\DeliveryController@render_file')->name('render-delivery-files');
            Route::post('/{id}', 'App\Http\Controllers\API\DeliveryController@save');
        });
        Route::group(['prefix' => 'hindrance'], function(){
            Route::put('send-hindrance/{id}', 'App\Http\Controllers\API\TransactionController@send_hindrance');
            Route::get('render-file/{id}', 'App\Http\Controllers\API\HindranceController@render_file')->name('render-hindrance-files');
            Route::post('/{id}', 'App\Http\Controllers\API\HindranceController@save');
        });
        Route::group(['prefix' => 'discrepancy'], function(){
            Route::get('check-discrepancy', 'App\Http\Controllers\API\DiscrepancyController@check_discrepancy');
            Route::get('select-discrepancy-type', 'App\Http\Controllers\API\DiscrepancyController@select_discrepancy_type');
            Route::post('/upload-file/{id}', 'App\Http\Controllers\API\DiscrepancyController@upload_file');
            Route::get('/render-file/{id}', 'App\Http\Controllers\API\DiscrepancyController@render_file')->name('render-discrepancy-files');
            Route::post('/', 'App\Http\Controllers\API\DiscrepancyController@create');
        });
        Route::put('finish/{id}', 'App\Http\Controllers\Api\TransactionController@finish');
        Route::put('confirm-discrepancy/{id}', 'App\Http\Controllers\Api\TransactionController@confirm_discrepancy');
        Route::put('publish-billing/{id}', 'App\Http\Controllers\Api\TransactionController@publish_billing');
        Route::put('approve-payment/{id}', 'App\Http\Controllers\Api\TransactionController@approve_payment');
        Route::get('data-table/{steps}','App\Http\Controllers\API\TransactionController@get');
        Route::post('reason-reject/{id}','App\Http\Controllers\API\TransactionController@reason_reject');
        Route::post('repair/{id}','App\Http\Controllers\API\TransactionController@repair');
        Route::post('reject/{id}','App\Http\Controllers\API\TransactionController@reject');
        Route::delete('delete-file/{id}','App\Http\Controllers\API\SubmissionFilesController@delete');
        Route::get('render-file/{id}','App\Http\Controllers\API\SubmissionFilesController@render_file')->name('render-submission-files');
        Route::post('upload/{id}','App\Http\Controllers\API\SubmissionFilesController@upload');
        Route::put('trash/{id}','App\Http\Controllers\API\TransactionController@restore_trash');
        Route::put('approve-submission/{id}','App\Http\Controllers\API\TransactionController@approve_submission');
        Route::get('trash','App\Http\Controllers\API\TransactionController@get_trash');
        Route::get('trash/{id}','App\Http\Controllers\API\TransactionController@show_trash');
        Route::delete('delete/{id}','App\Http\Controllers\API\TransactionController@delete');
        Route::post('/','App\Http\Controllers\API\TransactionController@store');
        Route::get('/{id}','App\Http\Controllers\API\TransactionController@show');
        Route::put('/{id}','App\Http\Controllers\API\TransactionController@update');
        Route::delete('/{id}','App\Http\Controllers\API\TransactionController@destroy');
    });

    Route::group(['prefix' => 'role'], function(){
        Route::get('/','App\Http\Controllers\API\RoleController@get');
        Route::get('/select-list','App\Http\Controllers\API\RoleController@select');
    });

    Route::group(['prefix' => 'fuel'], function(){
        Route::get('select-list','App\Http\Controllers\API\FuelController@select_list');
        Route::get('select-supplier','App\Http\Controllers\API\FuelController@select_supplier');
        Route::delete('/delete/{id}','App\Http\Controllers\API\FuelController@delete');
        Route::get('/','App\Http\Controllers\API\FuelController@get');
        Route::post('/','App\Http\Controllers\API\FuelController@create');
        Route::put('/{id}','App\Http\Controllers\API\FuelController@update');
    });
});
