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
Route::get('application/children/{childShortId}/posts/{quoteShortId}/img-original/{img_original_url_id}', 'PostController@getPostOriginalImage');
Route::get('application/children/{childShortId}/posts/{quoteShortId}/img-baked/{img_baked_url_id}', 'PostController@getPostBakedImage');

//get shared
Route::get('application/children/{childShortId}/posts/{postShortId}/shared/{img_baked_url_id}', 'ShareController@getSharedPost');


Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', 'Auth\LoginController@login');
    Route::post('/register', 'Auth\RegisterController@register');
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'auth'], function () {
    // Authentication Routes...
    Route::get('/check', 'UserController@checkAuth');
    Route::get('/logout', 'Auth\LoginController@logout');
    Route::get('/user', 'UserController@getUser');
    Route::post('/user', 'UserController@editUser');
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
        Route::post('/{childShortId}/edit', 'ChildController@update');

        /*
        * Api endpoints for quotes
        */
        Route::post('/{childShortId}/quotes/new', 'PostController@newQuote');
        Route::post('/{childShortId}/story/new', 'PostController@newStory');

        Route::post('{childShortId}/quotes/{quoteShortId}', 'PostController@editQuote');
        Route::post('{childShortId}/story/{memoryShortId}', 'PostController@editStory');

        Route::delete('{childShortId}/posts/{postShortId}/delete', 'PostController@delete');

        //share
        Route::get('{childShortId}/posts/{postShortId}/share', 'ShareController@sharePost');

    });

    Route::group(['prefix' => 'posts'], function () {
        Route::get('/', 'PostController@getAllPosts');
    });

    /*
    * Api endpoints for book data.
    */
    Route::group(['prefix' => 'books'], function () {
        //check for 0 memories
        Route::get('/check', 'BookController@check');

        Route::post('/new', 'BookController@newBook');
        Route::get('/generate', 'BookController@generateBook');

        Route::get('/seen-tutorial', 'BookController@seenTutorial');

        Route::get('/{shortId}', 'BookController@getBook');
        Route::post('/{shortId}', 'BookController@editBook');
        Route::get('/', 'BookController@getAllBooks');
        Route::delete('/{shortId}/delete', 'BookController@delete');

        //after seeing first book tutorial

    });

    Route::group(['prefix' => 'orders'], function () {
        Route::get('/prices', 'OrderController@getPrices');
        Route::post('/checkout', 'OrderController@checkout');
        // Route::post('/pay', 'OrderController@pay');
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

    Route::group(['prefix' => 'countries'], function () {
        Route::get('/', 'CountryController@getAllCountries');
    });

});
