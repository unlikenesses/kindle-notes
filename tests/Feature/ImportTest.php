<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ImportTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function an_authenticated_user_can_upload_a_file()
  {
    $this->withoutExceptionHandling();
    
    $this->signIn();

    Storage::fake('clippings');

    $this->post('/import', [
      'clippings_file' => UploadedFile::fake()->create('My Clippings.txt', 256)
    ]); 

    Storage::disk('clippings')->assertExists('My Clippings.txt');
  }
}
