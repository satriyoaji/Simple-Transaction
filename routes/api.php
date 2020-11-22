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
Route::group(['middleware' => ['auth:api']], function () {
    Route::group(['prefix'=>'auth','as'=>'auth.'], function (){
        Route::post('logout', 'AuthController@logout')->name('logout');
        Route::post('refresh', 'AuthController@refresh')->name('refresh');
    });

    Route::group(['prefix'=>'transaction'], function (){
        Route::get('/{user}/get-transaction', 'TransactionController@getOwnTransaction')
            ->name('get-own-transaction');
    });

    Route::group(['prefix'=>'product'], function (){
        Route::get('/{user}/get-product', 'ProductController@getOwnProduct')
            ->name('get-own-product');
    });

    Route::resource('product', 'ProductController');
    Route::resource('transaction', 'TransactionController');
    Route::resource('user', 'UserController');
});

Route::group(['prefix'=>'auth','as'=>'auth.'], function (){
    Route::post('register', 'AuthController@register')->name('register');
    Route::post('login', 'AuthController@login')->name('login');
});
