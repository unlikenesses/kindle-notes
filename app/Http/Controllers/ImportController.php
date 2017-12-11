<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Book;
use App\Note;
use Illuminate\Http\Request;

class ImportController extends Controller
{
  private $numBooks = 0;
  private $numNotes = 0;

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('auth');

    // $this->middleware('subscribed');
  }

  public function show()
  {
    return view('show_import');
  }

  public function import_file(Request $request)
  {
    if (!($request->hasFile('clippings_file') && 
          $request->file('clippings_file')->isValid())) {
      return;
    }

    $parseAuthor = true;

    $this->validate($request, [
      'clippings_file' => 'required|mimes:txt',
    ]);

    $file = $request->file('clippings_file');

    $data = array('books' => $this->parse_file($file));

    $result = $this->saveAllData($data, $parseAuthor);

    $status = 'Imported ' . $this->numBooks . ' ' . str_plural('book', $this->numBooks);
    $status .= ' and ' . $this->numNotes . ' ' . str_plural('note', $this->numNotes);
    $status .= '.';

    return redirect('/books')->with('status', $status);
  }

  private function saveAllData($data, $parseAuthor = false)
  {
    foreach ($data['books'] as $row) {
      $this->saveBookData($row, $parseAuthor);
    }
  }

  private function saveBookData($row, $parseAuthor)
  {
    $newBook = false;
    
    $bookTitle = $row['book'];
    
    $book = $this->get_book_by_title_string($bookTitle, auth()->id());

    if (!$book) {
      $newBook = true;
      $this->numBooks++;
      $book = $this->addBook($bookTitle, $parseAuthor);
    }

    foreach ($row['notes'] as $note) {
      $this->saveNote($note, $book, $newBook);
    }
  }

  private function saveNote($note, $book, $newBook)
  {
    $userId = auth()->id();
    
    if ($newBook || ! $this->note_exists($note, $userId)) {
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
    } else {
      if (isset($note['meta'])) {
        // echo 'Skipping note ' . $note['meta']['date'] . '<br>';
      }
    }
  }

  private function addBook($bookTitle, $parseAuthor)
  {
    $book = new Book;
    $book->title_string = $bookTitle;
    if ($parseAuthor) {
      $parsed = $this->parse_title($bookTitle);
      $book->title = $parsed['title'];
      $book->author_first_name = $parsed['first_name'];
      $book->author_last_name = $parsed['last_name'];
    }
    auth()->user()->books()->save($book);

    return $book;
  }

  private function get_book_by_title_string($title_string, $userId)
  {
    $book = Book::where(['title_string' => $title_string, 'user_id' => $userId])->first();

    return $book;
  }

  private function note_exists($note, $userId)
  {
    if (isset($note['highlight'])) {
      $test_note = Note::where(['note' => trim($note['highlight']), 'user_id' => $userId])->first();
    }

    if (! $test_note) {
      return false;
    }

    return true;
  }

  private function parse_file($file)
  {
    $book = $books = $clipping = array();
    $in_book = FALSE;
    $fh = fopen($file, 'r');

    if ($fh) {
      while ($line = fgets($fh)) {
        if (!$in_book) {
          $existing_book_pos = -1;
          // This is a book title. Check if it's already in the `books` array:
          $c = 0;
          foreach ($books as $temp_book) {
            if ($temp_book['book'] == trim($line)) {
              $book = $temp_book;
              $existing_book_pos = $c;
            }
            $c++;
          }
          if ($existing_book_pos < 0) {
            $book['book'] = trim($line);
            $book['notes'] = array();
          }
          $in_book = TRUE;
        } else if (trim($line) == '==========') {
          // End of a clipping.
          $in_book = FALSE;
          if ($existing_book_pos < 0) {
            $books[] = $book;
          } else {
            $books[$existing_book_pos] = $book;
          }
          $book = array();
        } else if (stristr($line, 'your highlight')) {
          //  Location:
          $clipping['meta'] = $this->parse_meta($line);
          $clipping['type'] = 1;
        } else if (stristr($line, 'your note')) {
          $clipping['meta'] = $this->parse_meta($line);
          $clipping['type'] = 2;
        } else if (strlen(trim($line)) > 0) {
          // Highlight:
          $clipping['highlight'] = $line;
          $book['notes'][] = $clipping;
          $clipping = array();
        }
      }
    } else {
      echo 'Error opening file';
    }

    fclose($fh);
    
    return $books;
  }

  private function parse_meta($str)
  {
    $return = array();

    if (stristr($str, 'page')) {
      preg_match("/page (\d*-?\d*)/", $str, $output);
      $return['page'] = $output[1];
    }
    if (stristr($str, 'location')) {
      preg_match("/location (\d*-?\d*)/", $str, $output);
      $return['location'] = $output[1];
    }
    if (stristr($str, 'added')) {
      preg_match("/Added on (.*)/", $str, $output);
      $return['date'] = $output[1];
    }
    
    return $return;
  }

  private function parse_title($original_title)
  {
    // The idea is to split the title field into title string + author string.
    // Based on my sample size of 27, authors are typically separated by a hyphen or brackets.
    // Brackets are more common.
    // Title strings can contain hyphens AND brackets. E.g. a hyphen for a date range, then author in brackets.
    // Title strings can also contain more than 1 instance of the separator used to designate the author:
    // e.g. if the author separator is a hyphen, there may be more than 1 hyphen ("Century of Revolution, 1603-1714 - Christopher Hill").
    // e.g. same for brackets ("Rights of War and Peace (2005 ed.) vol. 1 (Book I) (Hugo Grotius)").
    // So we take the last instance of the separator as the author.
    // This will fail in some instances: e.g. "Harvey, David - A brief history of neoliberalism". But this seems an exception.

    $author = '';
    $title = '';
    $last_name = '';
    $first_name = '';
    // Check if the title ends with a closing bracket:
    if (substr($original_title, -1) === ')') {
      preg_match('/\(([^)]*)\)[^(]*$/', $original_title, $out);
      $author = $out[sizeof($out) - 1];
      $title = trim(str_replace('(' . $author . ')', '', $original_title));
    } else {
      // Check if there's a hyphen separated by spaces:
      // Don't bother if there's more than one instance, this is too hard to parse.
      if (substr_count($original_title, ' - ') === 1) {
        list($part_one, $part_two) = explode(' - ', $original_title);
        // Now the problem here is that either part could be the author's name.
        // For now we have to assume it's part two, and leave it to the user to correct if not.
        // I think Calibre does that too.
        // Maybe later check against a list of common names, e.g. https://github.com/hadley/data-baby-names
        $author = $part_two;
        $title = trim($part_one);
      }
    }
    if ($author !== '') {
      $author = trim($author);
      // Do we have a [last name, first name] format?
      if (strpos($author, ',') !== false) {
        list($last_name, $first_name) = explode(',', $author);
      } else {
        // Use a space:
        $name_array = explode(' ', $author);
        $last_name = $name_array[sizeof($name_array) - 1];
        array_pop($name_array);
        $first_name = implode(' ', $name_array);
      }
      $last_name = trim($last_name);
      $first_name = trim($first_name);
    }
    
    return [
      'title' => $title,
      'last_name' => $last_name,
      'first_name' => $first_name
    ];
  }
}
