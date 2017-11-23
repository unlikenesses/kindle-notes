<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NoteTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function it_belongs_to_a_book()
  {
    $note = factory('App\Note')->create();

    $this->assertInstanceOf('App\Book', $note->book);
  }
}
