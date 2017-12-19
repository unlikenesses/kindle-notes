<?php

namespace App;

use App\Book;
use App\Parser;
use Illuminate\Http\UploadedFile;

class Annotations
{

  private $parser;
  private $annotations;
  private $file;
  public $numBooks;
  public $numNotes;

  public function __construct(UploadedFile $file)
  {
    $this->parser = new Parser();
    $this->file = $file;

    $this->numBooks = 0;
    $this->numNotes = 0;
  }

  public function processFile()
  {
    if (!$fileHandle = fopen($this->file, 'r')) {
      throw new Exception;
    }

    $this->annotations = $this->parser->parseFile($fileHandle);

    fclose($fileHandle);

    return $this;
  }

  public function save()
  {
    foreach ($this->annotations as $data) {
      $this->saveBookData($data);
    }
  }

  private function saveBookData($data)
  {
    $bookTitleString = $data['book']['titleString'];

    if (strlen($bookTitleString) > 255) {
      throw new BookDetailsAreTooLong;
    }
    
    $book = $this->getBookByTitleString($bookTitleString, auth()->id());

    if (!$book) {
      $this->numBooks++;
      $book = $this->addBook($data['book']);
    }

    foreach ($data['notes'] as $note) {
      $this->saveNote($note, $book);
    }
  }

  public function saveNote($note, $book)
  {
    $userId = auth()->id();
    
    if (! $this->noteExists($note, $userId)) {
      $this->numNotes++;
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
    } else if (isset($note['meta'])) {
      // echo 'Skipping note ' . $note['meta']['date'] . '<br>';
    }
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

  private function getBookByTitleString($titleString, $userId)
  {
    $book = Book::where(['title_string' => $titleString, 'user_id' => $userId])->first();

    return $book;
  }

  public function noteExists($note, $userId)
  {
    $noteExists = false;

    if (isset($note['highlight']) && isset($note['meta'])) {     
      
      $query = Note::where([
        'note' => trim($note['highlight']), 
        'date' => $note['meta']['date'],
        'user_id' => $userId
        ]);

      if (isset($note['meta']['page'])) {
        $query->where('page', $note['meta']['page']);
      }
      
      if (isset($note['meta']['location'])) {
        $query->where('location', $note['meta']['location']);
      }

      $noteExists = $query->exists();
      
    }

    return $noteExists;
  }
}
