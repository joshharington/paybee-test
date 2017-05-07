<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/** TELEGRAM  **/
Route::post('/', ['as' => 'get-btc-equivalent', 'uses' => 'Telegram\\TelegramController@index']);

/** FRONTEND  **/
Auth::routes();

Route::get('/', function() {
    return view('welcome');
});