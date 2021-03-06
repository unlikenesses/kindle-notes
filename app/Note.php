<?php

namespace App;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model
{
  use Searchable; 
  use SoftDeletes;

  protected $dates = ['deleted_at'];
  protected $fillable = ['book_id', 'page', 'location', 'date', 'note', 'type'];

  public function book()
  {
    return $this->belongsTo(Book::class);
  }

  public static function saveImportedNote($note, $book)
  {
    $userId = auth()->id();
    
    if (isset($note['highlight']) && isset($note['meta']) && ! static::noteExists($note, $book->id, $userId)) {
      $date = '';
      $page = '';
      $location = '';
      $noteText = '';
      $noteType = 0;
      
      if (isset($note['meta']['date'])) {
        $timestamp = strtotime($note['meta']['date']);
        $date = date('Y-m-d H:i:s', $timestamp);
      }
      
      if (isset($note['meta']['page'])) {
        $page = $note['meta']['page'];
      }
      
      if (isset($note['meta']['location'])) {
        $location = $note['meta']['location'];
      }
      
      if (isset($note['highlight'])) {
        $noteText = trim($note['highlight']);
      }
      
      if (isset($note['type'])) {
        $noteType = trim($note['type']);
      }

      $newNote = new Note;
      $newNote->date = $date;
      $newNote->page = $page;
      $newNote->location = $location;
      $newNote->note = $noteText;
      $newNote->type = $noteType;
      $newNote->user_id = $userId;

      $book->notes()->save($newNote);

      return true;
    } 
    
    return false;
  }

  public static function noteExists($note, $book_id, $userId)
  {
    $date = '';

    if (isset($note['meta']['date'])) {
      $timestamp = strtotime($note['meta']['date']);
      $date = date('Y-m-d H:i:s', $timestamp);
    }

    $query = Note::withTrashed()->where([
      'book_id' => $book_id, 
      'date' => $date,
      'user_id' => $userId
      ]);

    if (isset($note['meta']['page'])) {
      $query->where('page', $note['meta']['page']);
    }
    
    if (isset($note['meta']['location'])) {
      $query->where('location', $note['meta']['location']);
    }

    return $query->exists();
  }
}
