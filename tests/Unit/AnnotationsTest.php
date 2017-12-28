<?php

namespace Tests\Unit;

use App\Book;
use App\Note;
use App\Annotations;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AnnotationsTest extends TestCase
{
  use RefreshDatabase;

  public function setUp()
  {
    parent::setUp();

    $path = __DIR__ . '/files/clippings.txt';

    $file = new UploadedFile($path, 'clippings.txt', filesize($path), null, null, true);

    $this->annotations = new Annotations($file);
  }  

  /** @test */
  public function a_file_can_be_processed()
  {
    // The processFile method is basically tested in ParserTest's it_can_parse_a_file

    $this->assertTrue(true); 
  }
  
  /** @test */
  public function annotations_can_be_saved()
  {
    $user = factory('App\User')->create();
    
    $this->signIn($user);

    $this->annotations->processFile();

    $this->annotations->save();

    $this->assertDatabaseHas('books', [
      'title_string' => 'Puritanism and Revolution (Christopher Hill)',
      'title' => 'Puritanism and Revolution',
      'author_last_name' => 'Hill',
      'author_first_name' => 'Christopher'
    ]);

    $bookId = Book::first()->id;
    
    $this->assertDatabaseHas('notes', [
      'book_id' => $bookId,
      'page' => '126',
      'location' => '1928-1929',
      'date' => '2015-04-29 00:48:33',
      'note' => 'The common law was identical with the laws of nature.'
    ]);
  }
}
