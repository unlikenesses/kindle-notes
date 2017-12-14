<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Book;
use App\Note;
use App\Annotations;
use Illuminate\Http\Request;
use App\Exceptions\BookDetailsAreTooLong;

class ImportController extends Controller
{
  private $numBooks = 0;
  private $numNotes = 0;

  public function __construct()
  {
    $this->middleware('auth');

    // $this->middleware('subscribed');
  }

  public function show()
  {
    return view('show_import');
  }

  public function importFile(Request $request)
  {
    if (!($request->hasFile('clippings_file') && 
          $request->file('clippings_file')->isValid())) {
      return;
    }

    $parseAuthor = true;

    $this->validate($request, [
      'clippings_file' => 'required|mimes:txt|max:3000',
    ]);

    $file = $request->file('clippings_file');

    $annotations = new Annotations($file);

    try { 
      $result = $this->saveAllData($annotations->getData(), $parseAuthor);
    } catch (BookDetailsAreTooLong $e) {
      return redirect('/import')->with('status', 'Could not upload file: one or more book titles is too long');
    } catch (\Exception $e) {
      dd($e);
      return redirect('/import')->with('status', 'There was an error uploading the file');
    }

    $status = 'Imported ' . $this->numBooks . ' ' . str_plural('book', $this->numBooks);
    $status .= ' and ' . $this->numNotes . ' ' . str_plural('note', $this->numNotes);

    return redirect('/books')->with('status', $status);
  }

  private function saveAllData($books, $parseAuthor = false)
  {
    foreach ($books as $book) {
      $this->saveBookData($book, $parseAuthor);
    }
  }

  private function saveBookData($bookData, $parseAuthor)
  {
    $bookTitleString = $bookData['book']['titleString'];

    if (strlen($bookTitleString) > 255) {
      throw new BookDetailsAreTooLong;
    }
    
    $book = $this->getBookByTitleString($bookTitleString, auth()->id());

    if (!$book) {
      $this->numBooks++;
      $book = $this->addBook($bookData['book'], $parseAuthor);
    }

    foreach ($bookData['notes'] as $note) {
      $this->saveNote($note, $book);
    }
  }

  private function saveNote($note, $book)
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

  private function addBook($bookData, $parseAuthor)
  {
    $book = new Book;
    $book->title_string = $bookData['titleString'];

    if ($parseAuthor) {
      $book->title = $bookData['title'];
      $book->author_first_name = $bookData['firstName'];
      $book->author_last_name = $bookData['lastName'];
    }
    
    auth()->user()->books()->save($book);
  
    return $book;
  }

  private function getBookByTitleString($titleString, $userId)
  {
    $book = Book::where(['title_string' => $titleString, 'user_id' => $userId])->first();

    return $book;
  }

  private function noteExists($note, $userId)
  {
    $noteExists = false;

    if (isset($note['highlight'])) {
      $noteExists = Note::where([
        'note' => trim($note['highlight']), 
        'user_id' => $userId
        ])->exists();
    }

    return $noteExists;
  }

  
}
