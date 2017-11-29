<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function it_shows_the_total_number_of_books_belonging_to_a_user()
  {
    $user = factory('App\User')->create();
    
    $this->signIn($user);

    $numBooks = 13;

    factory('App\Book', $numBooks)->create(['user_id' => $user->id]);

    $this->get('/home')
        ->assertSee("You have {$numBooks} books.");
  }

  /** @test */
  public function it_shows_the_total_number_of_notes_belonging_to_a_user()
  {
    $user = factory('App\User')->create();
    
    $this->signIn($user);

    $numNotes = 13;

    factory('App\Note', $numNotes)->create(['user_id' => $user->id]);

    $this->get('/home')
        ->assertSee("You have {$numNotes} notes.");
  }

  /** @test */
  public function it_shows_the_date_of_the_last_upload()
  {
    $user = factory('App\User')->create();
    
    $this->signIn($user);

    // First check for no upload:
    $this->get('/home')
        ->assertSee("You have not yet uploaded a file.");

    // // Now check for uploaded file:
    // Storage::fake('clippings');


  }
}
