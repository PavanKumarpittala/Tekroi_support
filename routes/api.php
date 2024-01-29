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


Route::any('support_email', array(
    'uses' => 'App\Http\Controllers\ApiEmailController@supportEmail',
    'as'   => 'support_email'
));

// Route::any('support_email', array(
//     'uses' => 'App\Http\Controllers\ApiReportController@supportEmail',
//     'as'   => 'support_email'
// ));

Route::any('support_tickets', array(
    'uses' => 'App\Http\Controllers\ApiReportController@supportTickets',
    'as'   => 'support_tickets'
));


