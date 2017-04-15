<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('login', function() {return view('auth.login', [
	'info' => session('info'),
	'myerror' => session('myerror')
]);});
Route::post('login', 'Sentinel\SentinelController@login');
Route::get('logout', 'Sentinel\SentinelController@logout');

Route::get('register', function() {return view('auth.register');});
Route::post('register', 'Sentinel\SentinelController@register');
Route::get('activate/{email}/{code}', 'Sentinel\SentinelController@activate');
Route::get('register/{email}', 'Sentinel\SentinelController@resendActivationCode');

Route::get('password/reset/{email}/{code}/{password}', 'Sentinel\SentinelController@resetPassword');
Route::get('password/reset', function() {return view('auth.passwords.reset', ['token'=>'']);});
Route::post('password/reset', 'Sentinel\SentinelController@sendResetPassword');

Route::get('/home', 'HomeController@index');
