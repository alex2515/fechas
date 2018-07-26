<?php

use Carbon\Carbon;

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
Route::get('fecha', function(){
	setlocale(LC_TIME, 'Spanish');
	$dt = Carbon::create(2016, 01, 06, 00, 00, 00);
	Carbon::setUtf8(false);
	echo $dt->formatLocalized('%A %d %B %Y');          // mi�rcoles 06 enero 2016
	Carbon::setUtf8(true);
	echo $dt->formatLocalized('%A %d %B %Y');          // miércoles 06 enero 2016
	Carbon::setUtf8(false);
	setlocale(LC_TIME, '');
});

Route::get('/', function () {
	setlocale(LC_TIME, 'Spanish');
	Carbon::setUtf8(true);
	$user = App\User::first();
    return view('welcome',compact('user'));
});

Route::get('/usuarios', 'UserController@index')->name('user.index');
Route::get('/usuarios/nuevo', 'UserController@create');
Route::post('/usuarios', 'UserController@store');
Route::get('/usuarios/{user}', 'UserController@show')->where('user', '[0-9]+')->name('user.show'); 
Route::get('/usuarios/{user}/edit', 'UserController@edit')->name('user.edit');
Route::put('/usuarios/{user}', 'UserController@update');
Route::delete('/usuarios/{user}', 'UserController@destroy')->name('user.destroy');