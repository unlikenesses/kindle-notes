<?php

namespace App;

use App\Parser;

class Annotations
{

  private $parser;
  private $parsedData;

  public function __construct($file)
  {
    $this->parser = new Parser();
    $this->parsedData = $this->parseFile($file);
  }

  public function getData()
  {
    return $this->parsedData;
  }

  private function parseFile($file)
  {
    $book = $books = $clipping = [];
    $inBook = false;

    if (!$fh = fopen($file, 'r')) {
      throw new Exception;
    }

    while ($line = fgets($fh)) {
      if (!$inBook) {
        $bookIndex = $this->bookExists($line, $books);
        
        if ($bookIndex < 0) {
          $parsedTitle = $this->parser->parseTitle(trim($line));
          $book['book'] = [
            'titleString' => trim($line),
            'title' => $parsedTitle['title'],
            'lastName' => $parsedTitle['lastName'],
            'firstName' => $parsedTitle['firstName'],
          ];
          $book['notes'] = [];
        } else {
          $book = $books[$bookIndex];
        }
        $inBook = true;
      } else if (trim($line) == '==========') {
        // End of a clipping.
        $inBook = false;
        if ($bookIndex < 0) {
          $books[] = $book;
        } else {
          $books[$bookIndex] = $book;
        }
        $book = [];
      } else if (stristr($line, 'your highlight')) {
        $clipping['meta'] = $this->parser->parseMeta($line);
        $clipping['type'] = 1;
      } else if (stristr($line, 'your note')) {
        $clipping['meta'] = $this->parser->parseMeta($line);
        $clipping['type'] = 2;
      } else if (strlen(trim($line)) > 0) {
        $clipping['highlight'] = $line;
        $book['notes'][] = $clipping;
        $clipping = [];
      }
    }

    fclose($fh);
    
    return $books;
  }

  private function bookExists($bookToFind, $books)
  {
    $bookIndex = -1;
    $i = 0;
    foreach ($books as $tempBook) {
      if ($tempBook['book'] == trim($bookToFind)) {
        $bookIndex = $i;
      }
      $i++;
    }

    return $bookIndex;
  }
}
