<?php

use Illuminate\Http\Request;
use stdClass;

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
 * Login routes
 */
Route::post('login', 'Auth\LoginController@login');
Route::post('register', 'Auth\RegisterController@register');

Route::group(['middleware' => 'auth:api'], function () {
    // Authentication Routes...
    Route::get('logout', 'Auth\LoginController@logout');

    // TESTING
    Route::get('test', function () {
        return 'authenticated';
    });

    Route::get('/user', function (Request $request) {
        $user_resp = new stdClass();
        $user_resp->id = $user->id;
        $user_resp->short_id = $user->short_id;
        $user_resp->first_name = $user->first_name;
        $user_resp->last_name = $user->last_name;
        $user_resp->email = $user->email;
        $user_resp->street_name = $user->street_name;
        $user_resp->house_number = $user->house_number;
        $user_resp->city = $user->city;
        $user_resp->postal_code = $user->postal_code;
        $user_resp->country = $user->country;
        $user_resp->JWTToken = $token;

        return response()->json([
            'user' => $user_resp
        ]);
    });
});

/*
 * Api endpoints consumed by the client application written in Angular 2.
 */
Route::group(['prefix' => 'application'], function () {

    /*
     * Api endpoints for all the child data.
     */
    Route::group(['prefix' => 'children'], function () {
        Route::get('/', 'ChildController@index');
        Route::get('/{shortId}', 'ChildController@getChild');
        Route::get('/{shortId}/quotes', 'ChildController@allQuotes');
        Route::post('/new', 'ChildController@new');
        Route::post('/{shortId}/upload', 'ChildController@uploadImage');
        Route::delete('/{shortId}/delete', 'ChildController@delete');
        Route::put('/{shortId}/edit', 'ChildController@update');
    });

    /*
     * Api endpoints for quote data.
     */
    Route::group(['prefix' => 'quotes'], function () {
        Route::post('/new', 'QuoteController@new');
        Route::post('/upload', 'QuoteController@uploadImage');
        Route::delete('/{shortId}/delete', 'QuoteController@delete');
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
});
