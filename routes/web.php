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

Route::get('/', 'WelcomeController@show');

Route::get('/home', 'HomeController@show');

Route::get('/import', 'HomeController@show_import');

Route::post('/import', 'HomeController@import_file');

Route::get('/books', 'HomeController@show_books');

Route::get('/books/{book}/notes', 'HomeController@show_notes');

Route::post('/getBookDetails', 'HomeController@getBookDetails');

Route::post('/storeBookDetails', 'HomeController@storeBookDetails');

Route::get('/tag/{tag}', 'HomeController@showBooksByTag');