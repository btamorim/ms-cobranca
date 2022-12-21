<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'ticket'], function () {
    Route::post('confirmation', 'TicketController@paymentConfirmation');
});

Route::post('process', 'UploadController@UploadCharges');

Route::group(['prefix' => 'debt'], function () {
    Route::get('processJob', 'ProcessDebtController@ProcessJob');

});