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
    $user = factory('App\User')->create();
    $this->be($user);
  }

  /** @test */
  public function a_user_can_search_notes()
  {
    config(['scout.driver' => 'mysql']);
    
    $searchTerm = 'inaugenscheinnahme';
    factory('App\Note', 2)->create();
    factory('App\Note', 2)->create(['note' => "This text contains the {$searchTerm} in it."]);

    do {
      $results = $this->getJson("/notes/search?q={$searchTerm}")->json()['data'];
    } while (empty($results));
    // Re. do-while loop:
    // See https://laracasts.com/series/lets-build-a-forum-with-laravel/episodes/95
    // Only for when we're using a 3rd-party search service, to handle network latency
    
    $this->assertCount(2, $results);

    // Again from Laracasts, if we're using a 3rd-party search we'll need to remove 
    // these test items.
    \App\Note::latest()->take(4)->unsearchable();
  }

  /** @test */
  public function a_user_can_search_books()
  {
    config(['scout.driver' => 'mysql']);
    
    $searchTerm = 'loeschwassereinspeisung';
    factory('App\Book', 2)->create();
    factory('App\Book', 2)->create(['title' => "This title contains the {$searchTerm} in it."]);

    do {
      $results = $this->getJson("/books/search?q={$searchTerm}")->json()['data'];
    } while (empty($results));
    
    $this->assertCount(2, $results);

    \App\Book::latest()->take(4)->unsearchable();
  }
}
