<?php

namespace App;

use App\Book;
use App\PaperwhiteParser;
use Illuminate\Http\UploadedFile;

class Annotations
{

  private $file;
  private $parser;
  private $annotations;

  public $numBooks = 0;
  public $numNotes = 0;

  public function __construct(UploadedFile $file, Parser $parser)
  {
    $this->file = $file;
    $this->parser = $parser;
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
      $results = Book::saveImportedData($data);
      if ($results['newBook']) {
        $this->numBooks++;
      }
      $this->numNotes += $results['numNotes'];
    }
  }
  
}
