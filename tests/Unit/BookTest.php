<?php

namespace Tests\Unit;

use App\Book; 
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function it_belongs_to_a_user()
  {
    $book = factory('App\Book')->create();
    
    $this->assertInstanceOf('App\User', $book->user);
  }

  /** @test */
  public function it_has_notes()
  {
    $book = factory('App\Book')->create();

    $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $book->notes);
  }

  /** @test */
  public function it_has_tags()
  {
    $book = factory('App\Book')->create();

    $tag = factory('App\Tag')->create();

    $book->tags()->save($tag);

    $this->assertEquals($book->tags->first()->tag, $tag->tag);
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
  public function it_soft_deletes_books()
  {
    $this->assertSoftDeletes(Book::class);
  }
}
