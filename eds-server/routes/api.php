<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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

Route::middleware('auth:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::middleware('admin')->group(function () {
        Route::get('/employees', 'EmployeeController@get');
        Route::post('/employees', 'EmployeeController@create');
        Route::put('/employees/{id}', 'EmployeeController@update');
    });

    Route::get('/employee-duties', 'EmployeeDutyController@get');
    Route::get('/employee-duties/{id}', 'EmployeeDutyController@getById');
    Route::middleware('admin')->group(function () {
        Route::post('/employee-duties', 'EmployeeDutyController@create');
        Route::put('/employee-duties/{id}', 'EmployeeDutyController@update');
        Route::delete('/employee-duties/{id}', 'EmployeeDutyController@delete');
    });
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
