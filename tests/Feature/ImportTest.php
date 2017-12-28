<?php

namespace Tests\Feature;

use App\Note;
use App\Annotations;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ImportTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function modified_notes_are_not_overwritten_when_their_containing_file_is_reimported()
  {
    $this->importData();

    $note = Note::first();

    $this->assertEquals($note->note, 'The common law was identical with the laws of nature.');

    $newText = 'Here is an updated note';

    $this->post('/notes/' . $note->id . '/update', ['note' => $newText]);

    $note = Note::first();
    
    $this->assertEquals($note->note, 'Here is an updated note');

    $this->importData();

    $note = Note::first();
    
    $this->assertEquals($note->note, 'Here is an updated note');
  }

  private function importData()
  {
    $user = factory('App\User')->create();
    
    $this->signIn($user);

    $path = __DIR__ . '/files/clippings.txt';
    
    $file = new UploadedFile($path, 'clippings.txt', filesize($path), null, null, true);

    $annotations = (new Annotations($file))->processFile();

    $annotations->save();
  }
}
