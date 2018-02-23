<?php

namespace Tests\Unit;

use App\PaperwhiteParser;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ParserTest extends TestCase
{
  use RefreshDatabase;

  public function setUp()
  {
    parent::setUp();

    $this->parser = new PaperwhiteParser();
  }

  /** @test */
  public function it_can_parse_a_file()
  {
    $pathToFile = __DIR__ . '/files/clippings.txt';

    $fileHandle = fopen($pathToFile, 'r');

    $response = $this->parser->parseFile($fileHandle);

    $this->assertCount(2, $response); 

    $this->assertCount(3, $response[0]['notes']); 
    $this->assertCount(9, $response[1]['notes']);

    $this->assertEquals("Hill", $response[0]['book']['lastName']);

    $this->assertEquals("From 1633 onwards, when the government was ruling without Parliament, depopulators were prosecuted", $response[1]['notes'][2]['highlight']);
  }

  /** @test */
  public function it_can_parse_a_book_title_string()
  {
    $titleString = 'Century of Revolution, 1603-1714 - Christopher Hill';
    $title = 'Century of Revolution, 1603-1714';
    $firstName = 'Christopher';
    $lastName = 'Hill';

    $response = $this->parser->parseTitle($titleString);

    $this->assertEquals($response['title'], $title);
    $this->assertEquals($response['firstName'], $firstName);
    $this->assertEquals($response['lastName'], $lastName);

    $titleString = 'Rights of War and Peace (2005 ed.) vol. 1 (Book I) (Hugo Grotius)';
    $title = 'Rights of War and Peace (2005 ed.) vol. 1 (Book I)';
    $firstName = 'Hugo';
    $lastName = 'Grotius';

    $response = $this->parser->parseTitle($titleString);

    $this->assertEquals($response['title'], $title);
    $this->assertEquals($response['firstName'], $firstName);
    $this->assertEquals($response['lastName'], $lastName);
  }
    
  /** @test */
  public function it_can_parse_an_author()
  {
    $authorString = 'Harvey, David';
    $firstName = 'David';
    $lastName = 'Harvey';

    $response = $this->parser->parseAuthor($authorString);

    $this->assertEquals($response['firstName'], $firstName);
    $this->assertEquals($response['lastName'], $lastName);

    $authorString = 'David Harvey';
    $firstName = 'David';
    $lastName = 'Harvey';

    $response = $this->parser->parseAuthor($authorString);

    $this->assertEquals($response['firstName'], $firstName);
    $this->assertEquals($response['lastName'], $lastName);
  }

  /** @test */
  public function it_can_parse_meta_data()
  {
    $meta = '- Your Highlight on page 126 | location 1928-1929 | Added on Wednesday, 29 April 2015 00:48:33';
    $page = '126';
    $location = '1928-1929';
    $date = 'Wednesday, 29 April 2015 00:48:33';

    $response = $this->parser->parseMeta($meta);

    $this->assertEquals($response['page'], $page);
    $this->assertEquals($response['location'], $location);
    $this->assertEquals($response['date'], $date);

    $meta = '- Your Note on page 116 | location 1776 | Added on Saturday, 15 October 2016 14:02:31';
    $page = '116';
    $location = '1776';
    $date = 'Saturday, 15 October 2016 14:02:31';
    
    $response = $this->parser->parseMeta($meta);
    
    $this->assertEquals($response['page'], $page);
    $this->assertEquals($response['location'], $location);
    $this->assertEquals($response['date'], $date);
  }
}
