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
  public function it_can_save_a_book()
  {
    $user = factory('App\User')->create();
    
    $this->signIn($user);

    $title = 'A test of a title string';
    $firstName = 'Jehoover';
    $lastName = 'MacDoozlebink';
    $titleString = $title . ' - ' . $firstName . ' ' . $lastName;

    $book = [
      'titleString' => $titleString,
      'title' => $title,
      'lastName' => $lastName,
      'firstName' => $firstName
    ];

    $page = '35';
    $location = '1883';
    $date = '2017-03-01 10:00:03';
    $highlight = 'This is a test note';

    $note = [
      'type' => 1,
      'highlight' => $highlight,
      'meta' => [
        'page' => $page,
        'location' => $location,
        'date' => $date,
      ]
    ];

    $data = [
      'book' => $book,
      'notes' => [$note]
    ];

    Book::saveImportedData($data);

    $this->assertDatabaseHas('books', [
      'title_string' => $titleString,
      'title' => $title,
      'author_last_name' => $lastName,
      'author_first_name' => $firstName
    ]);

    $bookId = Book::first()->id;

    $this->assertDatabaseHas('notes', [
      'book_id' => $bookId,
      'page' => $page,
      'location' => $location,
      'date' => $date,
      'note' => $highlight
    ]);
  }

  /** @test */
  public function it_can_save_a_note()
  {
    $user = factory('App\User')->create();
    
    $this->signIn($user);

    $book = factory('App\Book')->create();

    $note = [
      'type' => 1,
      'highlight' => 'This is a test',
      'meta' => [
        'page' => '35',
        'location' => '1883',
        'date' => '2017-03-01 10:00:03',
      ]
    ];

    $this->assertCount(0, $book->notes);

    Note::saveImportedNote($note, $book);

    $this->assertCount(1, $book->fresh()->notes);
  }

  
}
