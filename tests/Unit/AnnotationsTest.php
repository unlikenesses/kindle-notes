<?php

namespace Tests\Unit;

use App\Annotations;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AnnotationsTest extends TestCase
{
  use RefreshDatabase;

  public function setUp()
  {
    parent::setUp();

    $path = __DIR__ . '/files/clippings.txt';

    $file = new UploadedFile($path, 'clippings.txt', filesize($path), null, null, true);

    $this->annotations = new Annotations($file);
  }

  /** @test */
  public function a_notes_existence_is_based_on_type_date_page_and_location_but_not_the_note()
  {
    $user = factory('App\User')->create();

    $this->signIn($user);

    $note = factory('App\Note')->create([
      'user_id' => $user->id,
      'page' => '32',
      'location' => '1883',
      'date' => '2017-03-01 10:00:03',
      'note' => 'This is a test',
      'type' => 1
    ]);    

    $newNote = [
      'type' => 1,
      'highlight' => 'This is a test',
      'meta' => [
        'page' => '35',
        'location' => '1883',
        'date' => '2017-03-01 10:00:03',
      ]
    ];

    $noteExists = $this->annotations->noteExists($newNote, auth()->id());

    $this->assertFalse($noteExists);

  }
}
