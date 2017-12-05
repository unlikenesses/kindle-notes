<?php
namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BooksTest extends TestCase
{
  use RefreshDatabase;

  protected $newTitle = 'A pretty unique title if you ask me';
  protected $newFirstName = 'Federosque';
  protected $newLastName = 'Smithsantifax';

  public function setUp()
  {
    parent::setUp();
    $user = factory('App\User')->create();
    $this->be($user);
    $this->book = factory('App\Book')->create(['user_id' => $user->id]);
  }

  /** @test */
  public function a_user_can_view_their_books()
  {
    $this->get('/books')
        ->assertSee($this->book->title);
  }

  /** @test */
  public function a_user_can_view_their_books_by_tag()
  {
    $tag = factory('App\Tag')->create();

    $this->book->tags()->save($tag);

    $this->get('/tag/' . $tag->slug)
        ->assertSee($this->book->title);
  }

  /** @test */
  public function a_user_cannot_view_another_users_books()
  {
    $anotherUser = factory('App\User')->create();
    $anotherUsersBook = factory('App\Book')->create(['user_id' => $anotherUser->id]);
    $this->get('/books')
      ->assertDontSee($anotherUsersBook->title);
  }

  /** @test */
  public function a_user_can_view_a_book()
  {
    $this->get('/books/' . $this->book->id . '/notes')
        ->assertSee($this->book->title);
  }

  /** @test */
  public function a_user_cannot_view_another_users_book()
  {
    $anotherUser = factory('App\User')->create();
    $anotherUsersBook = factory('App\Book')->create(['user_id' => $anotherUser->id]);

    $this->get('/books/' . $anotherUsersBook->id . '/notes')
        ->assertDontSee($anotherUsersBook->title);
  }

  /** @test */
  public function a_user_can_update_the_details_of_a_book()
  {
    $this->updateBook([
      'id' => $this->book->id,
      'title' => $this->newTitle, 
      'authorFirstName' => $this->newFirstName, 
      'authorLastName' => $this->newLastName
    ]);

    $this->get('/books/' . $this->book->id . '/notes')
        ->assertSee($this->newTitle)
        ->assertSee($this->newFirstName)
        ->assertSee($this->newLastName);
  }

  /** @test */
  public function a_user_cannot_update_the_details_of_another_users_book()
  {
    $anotherUser = factory('App\User')->create();
    $anotherUsersBook = factory('App\Book')->create(['user_id' => $anotherUser->id]);
    
    $this->updateBook([
      'id' => $anotherUsersBook->id,
      'title' => $this->newTitle, 
      'authorFirstName' => $this->newFirstName, 
      'authorLastName' => $this->newLastName
    ]);

    $this->be($anotherUser);
    
    $this->get('/books/' . $anotherUsersBook->id . '/notes')
        ->assertDontSee($this->newTitle)
        ->assertDontSee($this->newFirstName)
        ->assertDontSee($this->newLastName);
  }
  
  /** @test */
  public function a_user_can_view_notes_associated_with_a_book()
  {
    $note = factory('App\Note')
        ->create(['book_id' => $this->book->id]);

    $this->get('/books/' . $this->book->id . '/notes')
        ->assertSee($note->note);
  }

  /** @test */
  public function a_book_requires_a_title()
  {
    $this->updateBook([
      'id' => $this->book->id,
      'title' => null
    ])->assertSessionHasErrors('title');
  }

  /** @test */
  public function a_books_title_must_not_be_more_than_255_characters_long()
  {
    $this->updateBook([
      'id' => $this->book->id,
      'title' => 'abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz'
    ])->assertSessionHasErrors('title');
  }

  protected function updateBook($attributes)
  {
    return $this->post('/storeBookDetails', $attributes);
  }
}
