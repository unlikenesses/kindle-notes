<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GuestTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function a_guest_cannot_view_books()
  { 
    $this->get('/books')
        ->assertRedirect('/login');
  }
  
  /** @test */
  public function a_guest_cannot_view_a_book()
  {    
    $book = factory('App\Book')->create();
    $this->get('/books/' . $book->id . '/notes')
        ->assertRedirect('/login');
  }
}