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

/**
* Login/register routes
*/

Route::get('/color', 'QuoteController@getMainColor');

//get child thumbnail
Route::get('application/children/{childShortId}/avatar/{avatar_url_id}', 'ChildController@avatar');
Route::get('application/children/{childShortId}/quotes/{quoteShortId}/img-original/{img_original_url_id}', 'QuoteController@getQuoteOriginalImage');
Route::get('application/children/{childShortId}/quotes/{quoteShortId}/img-baked/{img_baked_url_id}', 'QuoteController@getQuoteBakedImage');

Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', 'Auth\LoginController@login');
    Route::post('/register', 'Auth\RegisterController@register');
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'auth'], function () {
    // Authentication Routes...
    Route::get('/check', 'UserContorller@checkAuth');
    Route::get('/logout', 'Auth\LoginController@logout');
    Route::get('/user', 'UserController@getUser');
});

/*
* Api endpoints consumed by the client application written in Angular 2.
*/
Route::group(['prefix' => 'application', 'middleware' => 'jwt.auth'], function () {

    /*
    * Api endpoints for all the child data.
    */
    Route::group(['prefix' => 'children'], function () {
        Route::get('/', 'ChildController@getAllChildren');
        Route::get('/{childShortId}', 'ChildController@getChild');
        Route::get('/{childShortId}/quotes', 'ChildController@allQuotes');
        Route::post('/new', 'ChildController@new');
        Route::post('/{childShortId}/upload', 'ChildController@uploadImage');
        Route::delete('/{childShortId}/delete', 'ChildController@delete');
        Route::put('/{childShortId}/edit', 'ChildController@update');

        /*
        * Api endpoints for quotes
        */
        Route::post('/{childShortId}/quotes/new', 'QuoteController@newQuote');
        Route::delete('{childShortId}/quotes/{quoteShortId}/delete', 'QuoteController@delete');

    });

    Route::group(['prefix' => 'quotes'], function () {
        Route::get('/', 'QuoteController@getAllQuotes');
    });

    /*
    * Api endpoints for book data.
    */
    Route::group(['prefix' => 'books'], function () {
        Route::post('/new', 'BookController@new');
        // Route::put('');
        Route::get('/{shortId}', 'BookController@getBook');
        Route::get('/all', 'BookController@index');
        Route::delete('/{shortId}/delete', 'BookController@delete');
    });

    Route::group(['prefix' => 'achievements'], function () {
        Route::get('/', 'AchievementController@all');
    });
});
