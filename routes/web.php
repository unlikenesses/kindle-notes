<?php

Route::get('/', 'WelcomeController@show');

Route::get('/home', 'HomeController@show');

Route::get('/import', 'HomeController@show_import');

Route::post('/import', 'HomeController@import_file');

Route::get('/books', 'HomeController@show_books');

Route::get('/books/{book}/notes', 'HomeController@show_notes');

Route::post('/getBookDetails', 'HomeController@getBookDetails');

Route::post('/storeBookDetails', 'HomeController@storeBookDetails');

Route::get('/tag/{tag}', 'HomeController@showBooksByTag');

Route::post('/deleteTagPivot', 'TagsController@deleteTagPivot');

Route::post('/addTagPivot', 'TagsController@addTagPivot');

Route::post('/getTagsForBook', 'TagsController@getTagsForBook');

Route::get('/tagAutoComplete', 'TagsController@tagAutoComplete');

Route::get('/csvExport/{book}', 'exportController@csvExport');