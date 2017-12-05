<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TagTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function it_belongs_to_many_books()
  {
    $testBooks = [
      $book1 = factory('App\Book')->create(), 
      $book2 = factory('App\Book')->create()
    ];

    $tag = factory('App\Tag')->create();

    $testBooks[0]->tags()->attach($tag->id);
    $testBooks[1]->tags()->attach($tag->id);

    $books = $tag->books()->get();

    $i=0;
    foreach ($books as $book) {
      $this->assertEquals($book->title, $testBooks[$i]->title);
      $i++;
    }
  }
}
