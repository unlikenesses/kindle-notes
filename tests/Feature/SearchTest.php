<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchTest extends TestCase
{
  use RefreshDatabase;

  public function setUp()
  {
    parent::setUp();
    $this->user = factory('App\User')->create();
    $this->be($this->user);
  }

  /** @test */
  public function a_user_can_search_their_books_and_notes()
  {
    $searchTerm = 'inaugenscheinnahme';

    factory('App\Book', 2)->create(['user_id' => $this->user->id]);
    $targetBook1 = factory('App\Book')->create([
      'user_id' => $this->user->id, 
      'title' => "This title contains the {$searchTerm} in it."
    ]);
    $targetBook2 = factory('App\Book')->create([
      'user_id' => $this->user->id, 
      'title' => "This also contains {$searchTerm} in it."
    ]);

    factory('App\Note', 2)->create(['user_id' => $this->user->id]);
    $targetNote = factory('App\Note')->create([
      'user_id' => $this->user->id, 
      'note' => "This text contains the {$searchTerm} in it."
    ]);

    $results = $this->getJson("/search?q={$searchTerm}")->json();

    $bookResults = $results['books'];
    $noteResults = $results['notes'];

    $this->assertCount(2, $bookResults);
    $this->assertCount(1, $noteResults);

    $this->assertEquals($bookResults[0]['id'], $targetBook1->id);
    $this->assertEquals($bookResults[1]['id'], $targetBook2->id);
    $this->assertEquals($noteResults[0]['id'], $targetNote->id);
  }
  
  /** @test */
  public function a_user_cannot_search_another_users_books_and_notes()
  {
    $searchTerm = 'inaugenscheinnahme';

    factory('App\Book')->create(['user_id' => $this->user->id]);
    factory('App\Note')->create(['user_id' => $this->user->id]);

    $newUser = factory('App\User')->create();
    $this->be($newUser);

    factory('App\Book')->create([
      'user_id' => $newUser->id, 
      'title' => "This title contains the {$searchTerm} in it."
    ]);
    factory('App\Note')->create([
      'user_id' => $newUser->id, 
      'note' => "This text contains the {$searchTerm} in it."
    ]);

    $this->be($this->user);

    $results = $this->getJson("/search?q={$searchTerm}")->json();

    $bookResults = $results['books'];
    $noteResults = $results['notes'];

    $this->assertCount(0, $bookResults);
    $this->assertCount(0, $noteResults);
  }

  /** @test */
  // public function a_user_can_search_notes()
  // {
  //   config(['scout.driver' => 'mysql']);
    
  //   $searchTerm = 'inaugenscheinnahme';
  //   factory('App\Note', 2)->create();
  //   factory('App\Note', 2)->create(['note' => "This text contains the {$searchTerm} in it."]);

  //   do {
  //     $results = $this->getJson("/notes/search?q={$searchTerm}")->json()['data'];
  //   } while (empty($results));
  //   // Re. do-while loop:
  //   // See https://laracasts.com/series/lets-build-a-forum-with-laravel/episodes/95
  //   // Only for when we're using a 3rd-party search service, to handle network latency
    
  //   $this->assertCount(2, $results);

  //   // Again from Laracasts, if we're using a 3rd-party search we'll need to remove 
  //   // these test items.
  //   \App\Note::latest()->take(4)->unsearchable();
  // }

  // /** @test */
  // public function a_user_can_search_books()
  // {
  //   config(['scout.driver' => 'mysql']);
    
  //   $searchTerm = 'loeschwassereinspeisung';
  //   factory('App\Book', 2)->create();
  //   factory('App\Book', 2)->create(['title' => "This title contains the {$searchTerm} in it."]);

  //   do {
  //     $results = $this->getJson("/books/search?q={$searchTerm}")->json()['data'];
  //   } while (empty($results));
    
  //   $this->assertCount(2, $results);

  //   \App\Book::latest()->take(4)->unsearchable();
  // }
}
