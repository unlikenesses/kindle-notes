<?php

namespace Tests\Unit;

use App\Annotations;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ImportTest extends TestCase
{
  use RefreshDatabase;

  public function setUp()
  {
    parent::setUp();

    $path = __DIR__ . '/files/clippings.txt';

    $file = new UploadedFile($path, 'clippings.txt', filesize($path), null, null, true);

    $this->annotations = new Annotations($file);
  }
  
}
