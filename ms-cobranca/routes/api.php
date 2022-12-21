<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('upload', 'UploadController@UploadCharges');
Route::post('confirmation', 'TicketController@paymentConfirmation');
Route::get('processCsvList', 'ProcessDebtController@ProcessJob');


