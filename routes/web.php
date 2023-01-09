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
    return view('welcome');
});

Route::get('/usuarios', 'UserController@index')->name('users.index');

Route::get('/usuarios/nuevo', 'UserController@create')->name('users.create');

Route::post('/usuarios', 'UserController@store')->name('users.store');

Route::get('/usuarios/papelera', 'UserController@trashed')->name('users.trashed');

Route::get('/usuarios/{user}/editar', 'UserController@edit')->where('user','[0-9]+')->name('users.edit');

Route::patch('/usuarios/{user}/papelera', 'UserController@trash')->where('user', '[0-9]+')->name('users.trash');

Route::patch('/usuarios/{id}/restaurar', 'UserController@restore')->where('id', '[0-9]+')->name('users.restore');

Route::get('/usuarios/{user}', 'UserController@show')->where('user', '[0-9]+')->name('users.show');

Route::put('/usuarios/{user}', 'UserController@update')->where('user', '[0-9]+')->name('users.update');

Route::delete('/usuarios/{id}', 'UserController@destroy')->where('id', '[0-9]+')->name('users.destroy');

// Editar perfil propio
Route::get('/editar-perfil', 'ProfileController@edit')->name('profile.edit');

Route::put('/editar-perfil', 'ProfileController@update')->name('profile.update');

// Profesiones
Route::get('/profesiones', 'ProfessionController@index')->name('profession.index');
Route::delete('/profesiones/{profession}', 'ProfessionController@destroy')->name('profession.destroy');

// Habilidades
Route::get('/habilidades', 'SkillController@index')->name('skill.index');