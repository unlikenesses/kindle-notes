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
    $user = factory('App\User')->create();
    $this->be($user);
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
}
