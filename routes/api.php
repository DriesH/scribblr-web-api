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

Route::group(['prefix' => 'application'], function () {
    Route::group(['prefix' => 'children'], function () {
        Route::get('/', 'ChildController@index');
        Route::get('/{shortId}', 'ChildController@getChild');
        Route::get('/{shortId}/quotes', 'ChildController@allQuotes');
        Route::post('/new', 'ChildController@new');
        Route::post('/upload', 'ChildController@uploadImage');
        Route::delete('/{shortId}/delete', 'ChildController@delete');
        Route::put('/{shortId}/edit', 'ChildController@update');
    });
});
