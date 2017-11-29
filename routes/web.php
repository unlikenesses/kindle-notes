<?php

Route::get('/', 'WelcomeController@show');

Route::get('/home', 'HomeController@show');

Route::get('/import', 'HomeController@show_import');

Route::post('/import', 'HomeController@import_file');

Route::get('/books', 'BooksController@index');

Route::get('/books/{book}/notes', 'NotesController@index');

Route::get('/books/search', 'BooksController@search');

Route::get('/notes/search', 'NotesController@search');

Route::post('/getBookDetails', 'BooksController@getBookDetails');

Route::post('/storeBookDetails', 'BooksController@storeBookDetails');

Route::get('/tag/{tag}', 'BooksController@showBooksByTag');

Route::post('/deleteTagPivot', 'TagsController@deleteTagPivot');

Route::post('/addTagPivot', 'TagsController@addTagPivot');

Route::post('/getTagsForBook', 'TagsController@getTagsForBook');

Route::get('/tagAutoComplete', 'TagsController@tagAutoComplete');

Route::get('/csvExport/{book}', 'exportController@csvExport');