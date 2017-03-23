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

// TODO: Check this route! Do we need it? I don't think we do. DUBBLE CHECK!

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Login
Route::post('login', 'Auth\LoginController@login');

Route::group([
    'middleware' => 'auth:api',
], function () {

    // Authentication Routes...
    Route::get('logout', 'Auth\LoginController@logout');

    Route::get('test', function () {
        return 'authenticated';
    });

});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');


/*
| Api endpoints consumed by the client application written in Angular 2.
*/
Route::group(['prefix' => 'application'], function () {

    /*
    | Api endpoints for all the child data.
    */
    Route::group(['prefix' => 'children'], function () {
        Route::get('/', 'ChildController@index');
        Route::get('/{shortId}', 'ChildController@getChild');
        Route::get('/{shortId}/quotes', 'ChildController@allQuotes');
        Route::post('/new', 'ChildController@new');
        Route::post('/upload', 'ChildController@uploadImage');
        Route::delete('/{shortId}/delete', 'ChildController@delete');
        Route::put('/{shortId}/edit', 'ChildController@update');
    });

    /*
    | Api endpoints for quote data.
    */
    Route::group(['prefix' => 'quotes'], function () {
        Route::post('/new', 'QuoteController@new');
        Route::post('/upload', 'QuoteController@uploadImage');
        Route::delete('/{shortId}/delete', 'QuoteController@delete');
    });

    /*
    | Api endpoints for book data.
    */
    Route::group(['prefix' => 'books'], function () {
        Route::post('/new', 'BookController@new');
        // Route::put('');
        Route::get('/{shortId}', 'BookController@getBook');
        Route::get('/all', 'BookController@index');
        Route::delete('/{shortId}/delete', 'BookController@delete');
    });
});
