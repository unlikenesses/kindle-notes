<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function it_belongs_to_a_user()
  {
    $book = factory('App\Book')->create();
    
    $this->assertInstanceOf('App\User', $book->user);
  }

  /** @test */
  public function it_has_notes()
  {
    $book = factory('App\Book')->create();

    $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $book->notes);
  }

  /** @test */
  public function it_has_tags()
  {
    $book = factory('App\Book')->create();

    $tag = factory('App\Tag')->create();

    $book->tags()->save($tag);

    $this->assertEquals($book->tags->first()->tag, $tag->tag);
  }
}