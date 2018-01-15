<?php

Route::get('/', 'WelcomeController@show');

Route::get('/plain', 'WelcomeController@plain');

Route::get('/home', 'HomeController@show');

Route::get('/import', 'ImportController@show');
Route::post('/import', 'ImportController@importFile');

Route::get('/books', 'BooksController@index');
Route::post('/getBookDetails', 'BooksController@getBookDetails');
Route::post('/storeBookDetails', 'BooksController@storeBookDetails');
Route::get('/tag/{tag}', 'BooksController@showBooksByTag');

Route::get('/books/{book}/notes', 'NotesController@index');
Route::post('/notes/{note}/update', 'NotesController@update');
Route::delete('/notes/{note}', 'NotesController@delete');

Route::post('/deleteTagPivot', 'TagsController@deleteTagPivot');
Route::post('/addTagPivot', 'TagsController@addTagPivot');
Route::post('/getTagsForBook', 'TagsController@getTagsForBook');
Route::get('/tagAutoComplete', 'TagsController@tagAutoComplete');

Route::get('/csvExport/{book}', 'exportController@csvExport');

Route::get('/search', 'SearchController@index');

Route::get('/deleted-items', 'DeletedItemsController@index');
Route::get('/restoreNote/{note}', 'DeletedItemsController@restoreNote');