<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
Route::post('/login', 'AuthController@login');

/**
 * @todo:: Should protect the endpoint either by limiting server who can send request
 * or some authentication process, also should encrypt payload
 */
Route::post('/notify', function (Request $request) {
    Log::debug($request->all());

    return ['success' => true];
});
