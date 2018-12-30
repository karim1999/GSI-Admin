<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([

    'prefix' => 'auth'

], function () {

    Route::post('login', 'AuthController@login');
    Route::post('register', 'AuthController@register');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');

});

Route::group([
    'middleware' => 'jwt.auth',

], function () {
    //Lectures
    Route::get('lectures', 'LecturesController@showLectures');
    Route::post('jointLecture/{lecture}', 'LecturesController@jointLecture');
    Route::post('addLecture', 'LecturesController@addLecture');
    Route::post('editLecture/{lecture}', 'LecturesController@editLecture');
    Route::delete('deleteLecture/{lecture}', 'LecturesController@deleteLecture');

    Route::get('getusers', 'LecturesController@getUsers');

    Route::post('updatetoken', 'AuthController@userToken');

    Route::post('notification', 'LecturesController@notification');

});

Route::get('lecturesDate', 'LecturesController@showLecturesByDate');
Route::get('addlectureusers', 'LecturesController@addLectureUsers');
Route::post('jointlectureusers/{lecture}/{user}', 'LecturesController@jointLectureUsers');
Route::post('/password/reset', 'AuthController@recover')->name('password.reset');

