<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Book;
use App\Note;
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

    $data = array('books' => $this->parseFile($file));

    try { 
      $result = $this->saveAllData($data, $parseAuthor);
    } catch (BookDetailsAreTooLong $e) {
      return redirect('/import')->with('status', 'Could not upload file: one or more book titles is too long');
    } catch (\Exception $e) {
      return redirect('/import')->with('status', 'There was an error uploading the file');
    }

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

    if (strlen($bookTitle) > 255) {
      throw new BookDetailsAreTooLong;
    }
    
    $book = $this->getBookByTitleString($bookTitle, auth()->id());

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
    
    if ($newBook || ! $this->noteExists($note, $userId)) {
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

  private function addBook($bookTitle, $parseAuthor)
  {
    $book = new Book;
    $book->title_string = $bookTitle;

    if ($parseAuthor) {
      $parsed = $this->parseTitle($bookTitle);
      $book->title = $parsed['title'];
      $book->author_first_name = $parsed['firstName'];
      $book->author_last_name = $parsed['lastName'];
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

  private function parseFile($file)
  {
    $book = $books = $clipping = array();
    $inBook = FALSE;
    $fh = fopen($file, 'r');

    if ($fh) {
      while ($line = fgets($fh)) {
        if (!$inBook) {
          $existingBookPos = -1;
          // This is a book title. Check if it's already in the `books` array:
          $c = 0;
          foreach ($books as $tempBook) {
            if ($tempBook['book'] == trim($line)) {
              $book = $tempBook;
              $existingBookPos = $c;
            }
            $c++;
          }
          if ($existingBookPos < 0) {
            $book['book'] = trim($line);
            $book['notes'] = array();
          }
          $inBook = TRUE;
        } else if (trim($line) == '==========') {
          // End of a clipping.
          $inBook = FALSE;
          if ($existingBookPos < 0) {
            $books[] = $book;
          } else {
            $books[$existingBookPos] = $book;
          }
          $book = array();
        } else if (stristr($line, 'your highlight')) {
          //  Location:
          $clipping['meta'] = $this->parseMeta($line);
          $clipping['type'] = 1;
        } else if (stristr($line, 'your note')) {
          $clipping['meta'] = $this->parseMeta($line);
          $clipping['type'] = 2;
        } else if (strlen(trim($line)) > 0) {
          // Highlight:
          $clipping['highlight'] = $line;
          $book['notes'][] = $clipping;
          $clipping = array();
        }
      }
    } else {
      throw new Exception;
    }

    fclose($fh);
    
    return $books;
  }

  private function parseMeta($str)
  {
    $return = [];

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

  private function parseTitle($titleString)
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
    $lastName = '';
    $firstName = '';
    // Check if the title ends with a closing bracket:
    if (substr($titleString, -1) === ')') {
      preg_match('/\(([^)]*)\)[^(]*$/', $titleString, $out);
      $author = $out[sizeof($out) - 1];
      $title = trim(str_replace('(' . $author . ')', '', $titleString));
    } else {
      // Check if there's a hyphen separated by spaces:
      // Don't bother if there's more than one instance, this is too hard to parse.
      if (substr_count($titleString, ' - ') === 1) {
        list($partOne, $partTwo) = explode(' - ', $titleString);
        // Now the problem here is that either part could be the author's name.
        // For now we have to assume it's part two, and leave it to the user to correct if not.
        // I think Calibre does that too.
        // Maybe later check against a list of common names, e.g. https://github.com/hadley/data-baby-names
        $author = $partTwo;
        $title = trim($partOne);
      }
    }
    if ($author !== '') {
      $author = trim($author);
      // Do we have a [last name, first name] format?
      if (strpos($author, ',') !== false) {
        list($lastName, $firstName) = explode(',', $author);
      } else {
        // Use a space:
        $nameArray = explode(' ', $author);
        $lastName = $nameArray[sizeof($nameArray) - 1];
        array_pop($nameArray);
        $firstName = implode(' ', $nameArray);
      }
      $lastName = trim($lastName);
      $firstName = trim($firstName);
    }
    
    return [
      'title' => $title,
      'lastName' => $lastName,
      'firstName' => $firstName
    ];
  }
}
