<?php

namespace Tests\Unit;

use App\Note;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NoteTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function it_belongs_to_a_book()
  {
    $note = factory('App\Note')->create();

    $this->assertInstanceOf('App\Book', $note->book);
  }

  /** @test */
  public function a_notes_existence_is_not_based_on_the_note_text()
  {
    $book_id = 14;

    $user = factory('App\User')->create();

    $this->signIn($user);

    $note = factory('App\Note')->create([
      'user_id' => $user->id,
      'book_id' => $book_id,
      'page' => '32',
      'location' => '1883',
      'date' => '2017-03-01 10:00:03',
      'note' => 'This is a test',
      'type' => 1
    ]);    

    $newNote = [
      'type' => 1,
      'highlight' => 'This is a test',
      'meta' => [
        'page' => '35',
        'location' => '1883',
        'date' => '2017-03-01 10:00:03',
      ]
    ];

    $noteExists = Note::noteExists($newNote, $book_id, auth()->id());

    $this->assertFalse($noteExists);

  }

  /** @test */
  public function a_note_already_exists_if_its_book_id_page_location_and_date_are_the_same_even_if_its_highlight_is_different()
  {
    $book_id = 14;
    
    $user = factory('App\User')->create();
    
    $this->signIn($user);

    $note = factory('App\Note')->create([
      'user_id' => $user->id,
      'book_id' => $book_id,
      'page' => '32',
      'location' => '1883',
      'date' => '2017-03-01 10:00:03',
      'note' => 'This is a test',
      'type' => 1
    ]);    

    $newNote = [
      'type' => 1,
      'highlight' => 'Now the text is totally different',
      'meta' => [
        'page' => '32',
        'location' => '1883',
        'date' => '2017-03-01 10:00:03',
      ]
    ];

    $noteExists = Note::noteExists($newNote, $book_id, auth()->id());

    $this->assertTrue($noteExists);
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

  /** @test */
  public function it_soft_deletes_notes()
  {
    $this->assertSoftDeletes(Note::class);
  }
}
