<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BooksTest extends TestCase
{
  use DatabaseMigrations;
  /** @test */
  public function a_user_can_view_their_books()
  {
    $book = factory('App\Book')->create();

    $response = $this->get('/books');
    $response->assertSee($book->title);
  }
}
