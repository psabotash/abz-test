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

Route::any('/', 'EmployeeController@index');
Route::any('/tree', 'EmployeeController@tree');
Route::any('/change_parent_id', 'EmployeeController@change_parent_id');
Route::match(['get', 'post'], '/list', ['uses' => 'EmployeeController@list'])->name('hidden')->middleware('auth');
Route::match(['get', 'post'], '/ajax_search_employee', 'EmployeeController@ajax_search_employee');
Route::match(['get', 'post'], '/ajax_create_employee', 'EmployeeController@ajax_CE_employee');
Route::match(['get', 'post'], '/ajax_edit_employee', 'EmployeeController@ajax_CE_employee');
Route::match(['get', 'post'], '/ajax_delete_employee', 'EmployeeController@ajax_D_employee');
Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');
