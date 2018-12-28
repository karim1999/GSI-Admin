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

//Payment Methods
Route::any('payment-methods', 'PaymentMethodsController@anyIndex');
Route::post('payment-methods/paid/{id}', 'PaymentMethodsController@anyPaid');
//Online Payment
Route::any('online-payment', 'OnlinePaymentController@anyIndex');
Route::any('online-payment/success', 'OnlinePaymentController@getSuccess');
Route::any('online-payment/error', 'OnlinePaymentController@getError');
// Route::get('/buy', 'BuyController@buy');
// Route::get('/response', 'BuyController@response');
// Route::get('/error', 'BuyController@error');
// Route::get('/result', 'BuyController@result');
