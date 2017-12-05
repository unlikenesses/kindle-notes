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
