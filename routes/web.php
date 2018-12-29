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

Route::get('payment/buy', 'OnlinePaymentController@buy')->name('payment.buy');
Route::any('payment/response', 'OnlinePaymentController@response')->name('payment.response');
Route::any('payment/success', 'OnlinePaymentController@success')->name('payment.success');
Route::any('payment/error', 'OnlinePaymentController@error')->name('payment.error');
