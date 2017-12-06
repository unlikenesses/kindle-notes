<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TagsTest extends TestCase
{
  use RefreshDatabase;

  public function setUp()
  {
    parent::setUp();
    $this->user = factory('App\User')->create();
    $this->be($this->user);
  }

  /** @test */
  public function a_tag_can_be_added_to_a_book()
  {
    $tagName = "lepidoptera";

    $book = factory('App\Book')->create();

    $response = $this->post('/addTagPivot', [
      'tag' => $tagName,
      'book_id' => $book->id
    ])->getContent();

    $tagId = json_decode($response)->id;

    $this->assertDatabaseHas('tags', [
      'id' => $tagId,
      'tag' => $tagName
    ]);

    $this->assertDatabaseHas('book_tag', [
      'book_id' => $book->id,
      'tag_id' => $tagId
    ]);
    
    $this->assertDatabaseHas('tag_user', [
      'user_id' => $this->user->id,
      'tag_id' => $tagId
    ]);    
  }

  /** @test */
  public function a_deleted_tag_will_delete_its_book_associations()
  {
    $tagName = "lepidoptera";
    
    $book = factory('App\Book')->create();

    $response = $this->post('/addTagPivot', [
      'tag' => $tagName,
      'book_id' => $book->id
    ])->getContent();

    $tagId = json_decode($response)->id;

    $this->post('/deleteTagPivot', [
      'book_id' => $book->id,
      'tag_id' => $tagId
    ]);

    $this->assertDatabaseMissing('book_tag', [
      'book_id' => $book->id,
      'tag_id' => $tagId
    ]);
  }

  /** @test */
  public function if_a_tag_is_added_twice_it_should_only_occur_once_in_the_database()
  {
    $tagName = "lepidoptera";
    
    // First user (logged in in the setUp above)
    $book = factory('App\Book')->create();

    $response = $this->post('/addTagPivot', [
      'tag' => $tagName,
      'book_id' => $book->id
    ])->getContent();

    $tagId = json_decode($response)->id;

    // Second user:
    $newUser = factory('App\User')->create();
    $this->be($newUser);

    $book2 = factory('App\Book')->create();

    $response = $this->post('/addTagPivot', [
      'tag' => $tagName,
      'book_id' => $book2->id
    ])->getContent();

    $tag2Id = json_decode($response)->id;

    $this->assertEquals($tagId, $tag2Id);
  }

  /** @test */
  public function a_deleted_tag_will_delete_its_user_association_if_none_of_that_users_books_have_that_tag()
  {
    $tagName = "lepidoptera";

    $book = factory('App\Book')->create(['user_id' => $this->user->id]);
    $book2 = factory('App\Book')->create(['user_id' => $this->user->id]);

    $this->post('/addTagPivot', [
      'tag' => $tagName,
      'book_id' => $book->id
    ]);

    $response = $this->post('/addTagPivot', [
      'tag' => $tagName,
      'book_id' => $book2->id
    ])->getContent();

    $tagId = json_decode($response)->id;

    $this->post('/deleteTagPivot', [
      'book_id' => $book->id,
      'tag_id' => $tagId
    ]);

    // The tag should still have an association with this user because it's still associated with $book2
    $this->assertDatabaseHas('tag_user', [
      'user_id' => $this->user->id,
      'tag_id' => $tagId
    ]);

    $this->post('/deleteTagPivot', [
      'book_id' => $book2->id,
      'tag_id' => $tagId
    ]);

    // Now the tag should have been detached from the user
    $this->assertDatabaseMissing('tag_user', [
      'user_id' => $this->user->id,
      'tag_id' => $tagId
    ]);
  }

  /** @test */
  public function a_tag_can_be_autocompleted()
  {
    $tagName = "Lepidoptera";
    $firstLetters = substr($tagName, 0, 4);

    // If we don't create the tag, we should expect an empty array:
    $this->get('/tagAutoComplete?tag=' . $firstLetters)
        ->assertExactJson([]);

    factory('App\Tag')->create(['tag' => $tagName]);
    
    // Now we should expect an array with the tag in it:
    $this->get('/tagAutoComplete?tag=' . $firstLetters)
        ->assertJsonFragment([
          'tag' => $tagName
        ]);
  }

  /** @test */
  public function autocomplete_only_returns_tags_for_the_authenticated_user()
  {
    $tagName = "Lepidoptera";
    $firstLetters = substr($tagName, 0, 4);

    $newUser = factory('App\User')->create();

    factory('App\Tag')->create(['tag' => $tagName, 'user_id' => $newUser->id]);
    
    // The first tag belongs to a different user so we should get an empty array:
    $this->get('/tagAutoComplete?tag=' . $firstLetters)
        ->assertExactJson([]);

    factory('App\Tag')->create(['tag' => $tagName, 'user_id' => $this->user->id]);

    // Now we should expect an array with the tag in it:
    $this->get('/tagAutoComplete?tag=' . $firstLetters)
        ->assertJsonFragment([
          'tag' => $tagName
        ]);
  }
}
