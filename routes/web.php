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

Route::get('/', function () {
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