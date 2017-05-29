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

//get child thumbnail

Route::get('application/children/{childShortId}/avatar/{avatar_url_id}', 'ChildController@avatar');
Route::get('application/children/{childShortId}/posts/{quoteShortId}/img-original/{img_original_url_id}', 'Postcontroller@getQuoteOriginalImage');
Route::get('application/children/{childShortId}/posts/{quoteShortId}/img-baked/{img_baked_url_id}', 'Postcontroller@getQuoteBakedImage');

//get shared
Route::get('application/children/{childShortId}/posts/{postShortId}/shared/{img_baked_url_id}', 'ShareController@getSharedPost');


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
        Route::get('/{childShortId}/posts', 'ChildController@allPosts');
        Route::post('/new', 'ChildController@new');
        Route::post('/{childShortId}/upload', 'ChildController@uploadImage');
        Route::delete('/{childShortId}/delete', 'ChildController@delete');
        Route::put('/{childShortId}/edit', 'ChildController@update');

        /*
        * Api endpoints for quotes
        */
        Route::post('/{childShortId}/quotes/new', 'Postcontroller@newQuote');
        Route::post('/{childShortId}/memories/new', 'Postcontroller@newMemory');

        Route::put('{childShortId}/quotes/{quoteShortId}', 'Postcontroller@editQuote');
        Route::put('{childShortId}/memories/{memoryShortId}', 'Postcontroller@editMemory');

        Route::delete('{childShortId}/posts/{postShortId}/delete', 'Postcontroller@delete');

        //share
        Route::get('{childShortId}/posts/{postShortId}/share', 'ShareController@sharePost');

    });

    Route::group(['prefix' => 'posts'], function () {
        Route::get('/', 'Postcontroller@getAllPosts');
    });

    /*
    * Api endpoints for book data.
    */
    Route::group(['prefix' => 'books'], function () {
        Route::post('/new', 'BookController@newBook');
        Route::get('/generate', 'BookController@generateBook');
        // Route::put('');
        Route::get('/{shortId}', 'BookController@getBook');
        Route::put('/{shortId}', 'BookController@editBook');
        Route::get('/', 'BookController@getAllBooks');
        Route::delete('/{shortId}/delete', 'BookController@delete');

        //after seeing first book tutorial
        Route::get('/seen-tutorial', 'BookController@seenTutorial');

    });

    Route::group(['prefix' => 'achievements'], function () {
        Route::get('/', 'AchievementController@all');
    });

    Route::group(['prefix' => 'news'], function () {
        Route::get('/', 'NewsController@getAllNews');
        Route::get('/read/{news_id}', 'NewsController@markAsRead');
        Route::get('/unread', 'NewsController@getUnreadCount');
    });

    Route::group(['prefix' => 'fonts'], function () {
        Route::get('/', 'FontController@getAllFonts');
    });

    Route::group(['prefix' => 'colors'], function () {
        Route::get('/', 'ColorController@getAllColors');
    });

    Route::group(['prefix' => 'presets'], function () {
        Route::get('/', 'PresetController@getAllPresets');
    });
});
