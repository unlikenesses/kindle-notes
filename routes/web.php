<?php

Route::get('/', 'WelcomeController@show');

Route::get('/home', 'HomeController@show');

Route::get('/import', 'ImportController@show');

Route::post('/import', 'ImportController@importFile');

Route::get('/books', 'BooksController@index');

Route::get('/books/{book}/notes', 'NotesController@index');

Route::get('/search', 'SearchController@index');

Route::post('/getBookDetails', 'BooksController@getBookDetails');

Route::post('/storeBookDetails', 'BooksController@storeBookDetails');

Route::get('/tag/{tag}', 'BooksController@showBooksByTag');

Route::post('/deleteTagPivot', 'TagsController@deleteTagPivot');

Route::post('/addTagPivot', 'TagsController@addTagPivot');

Route::post('/getTagsForBook', 'TagsController@getTagsForBook');

Route::get('/tagAutoComplete', 'TagsController@tagAutoComplete');

Route::get('/csvExport/{book}', 'exportController@csvExport');