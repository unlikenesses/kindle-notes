<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
  use SoftDeletes;

  protected $dates = ['deleted_at'];
  protected $fillable = ['title', 'author_first_name', 'author_last_name', 'user_id'];

  public function notes()
  {
    return $this->hasMany(Note::class);
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function tags() 
  {
    return $this->belongsToMany(Tag::class);
  }

  public static function saveImportedData($data)
  {
    $book = new static;

    $newBook = false;

    $numNotes = 0;

    $bookTitleString = $data['book']['titleString'];

    if (strlen($bookTitleString) > 255) {
      throw new BookDetailsAreTooLong;
    }
    
    $bookToAdd = $book->getBookByTitleString($bookTitleString, auth()->id());

    if (!$bookToAdd) {
      $newBook = true;
      $bookToAdd = $book->addBook($data['book']);
    }

    foreach ($data['notes'] as $note) {
      if (Note::saveImportedNote($note, $bookToAdd)) {
        $numNotes++;
      }
    }

    return ['newBook' => $newBook, 'numNotes' => $numNotes];
  }

  private function getBookByTitleString($titleString, $userId)
  {
    $book = Book::where(['title_string' => $titleString, 'user_id' => $userId])->first();

    return $book;
  }

  private function addBook($bookData)
  {
    $book = new Book;
    $book->title_string = $bookData['titleString'];
    $book->title = $bookData['title'];
    $book->author_first_name = $bookData['firstName'];
    $book->author_last_name = $bookData['lastName'];
    
    auth()->user()->books()->save($book);
  
    return $book;
  }
}
