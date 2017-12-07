<?php

namespace App\Http\Controllers;

use App\Book;
use Illuminate\Http\Request;

class ExportController extends Controller
{

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

  public function csvExport(Book $book) 
  {
    $csv = \League\Csv\Writer::createFromFileObject(new \SplTempFileObject());

    $csv->setOutputBOM(\League\Csv\Reader::BOM_UTF8);

    $csv->insertOne([$this->getBookDetails($book)]);

    $csv->insertOne(['Note', 'Date', 'Type']);

    $notes = $book->notes()->get();
    
    foreach ($notes as $note) {
      if ($note->type == 1) {
        $noteType = 'Highlight';
      }
      
      if ($note->type == 2) {
        $noteType = 'Note';
      }

      $noteDate = date('d M Y, h:m:s',strtotime($note->date));

      $csv->insertOne([$note->note, $noteDate, $noteType]);
    }

    $filename = 'notes.csv';

    // https://github.com/barryvdh/laravel-debugbar/issues/621#issuecomment-288962915

    return response((string) $csv, 200, [
      'Content-Type' => 'text/csv',
      'Content-Transfer-Encoding' => 'binary',
      'Content-Disposition' => 'attachment; filename="' . $filename . '"'
    ]);
  }

  protected function getBookDetails($book)
  {
    if ($book->title == '') {
      return $book->title_string;
    }

    $details = $book->title;

    if ($book->author_first_name !== '' || $book->author_last_name !== '') {
      $details .= ' (';
    }

    if ($book->author_first_name !== '') {
      $details .= $book->author_first_name . ' ';
    }

    if ($book->author_last_name !== '') {
      $details .= $book->author_last_name;
    }

    if ($book->author_first_name !== '' || $book->author_last_name !== '') {
      $details .= ')';
    }
    
    return $details;
  }
}
