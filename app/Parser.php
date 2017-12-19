<?php

namespace App;

class Parser
{

  public function parseFile($fileHandle)
  {
    $annotations = $book = $clipping = [];
    
    $inBook = false;

    while ($line = fgets($fileHandle)) {
      if (!$inBook) {
        $bookIndex = $this->bookExistsInFile($line, $annotations);
        
        if ($bookIndex < 0) {
          $parsedTitle = $this->parseTitle(trim($line));
          $book['book'] = [
            'titleString' => trim($line),
            'title' => $parsedTitle['title'],
            'lastName' => $parsedTitle['lastName'],
            'firstName' => $parsedTitle['firstName'],
          ];
          $book['notes'] = [];
        } else {
          $book = $annotations[$bookIndex];
        }
        $inBook = true;
      } else if (trim($line) == '==========') {
        // End of a clipping.
        $inBook = false;
        if ($bookIndex < 0) {
          $annotations[] = $book;
        } else {
          $annotations[$bookIndex] = $book;
        }
        $book = [];
      } else if (stristr($line, 'your highlight')) {
        $clipping['meta'] = $this->parseMeta($line);
        $clipping['type'] = 1;
      } else if (stristr($line, 'your note')) {
        $clipping['meta'] = $this->parseMeta($line);
        $clipping['type'] = 2;
      } else if (strlen(trim($line)) > 0) {
        $clipping['highlight'] = $line;
        $book['notes'][] = $clipping;
        $clipping = [];
      }
    }

    return $annotations;
  }

  private function bookExistsInFile($bookToFind, $books)
  {
    $bookIndex = -1;
    $i = 0;
    foreach ($books as $tempBook) {
      if ($tempBook['book'] == trim($bookToFind)) {
        $bookIndex = $i;
      }
      $i++;
    }

    return $bookIndex;
  }

  public function parseTitle($titleString)
  {
    // The idea is to split the title field into title string + author string.
    // Based on my sample size of 27, authors are typically separated by a hyphen or brackets.
    // Brackets are more common.
    // Title strings can contain hyphens AND brackets. E.g. a hyphen for a date range, then author in brackets.
    // Title strings can also contain more than 1 instance of the separator used to designate the author:
    // e.g. if the author separator is a hyphen, there may be more than 1 hyphen ("Century of Revolution, 1603-1714 - Christopher Hill").
    // e.g. same for brackets ("Rights of War and Peace (2005 ed.) vol. 1 (Book I) (Hugo Grotius)").
    // So we take the last instance of the separator as the author.
    // This will fail in some instances: e.g. "Harvey, David - A brief history of neoliberalism", where the author comes before the title. 
    // But this seems to be an exception.

    $author = '';
    $title = '';
    $lastName = '';
    $firstName = '';
    // Check if the title ends with a closing bracket:
    if (substr($titleString, -1) === ')') {
      preg_match('/\(([^)]*)\)[^(]*$/', $titleString, $out);
      $author = $out[sizeof($out) - 1];
      $title = trim(str_replace('(' . $author . ')', '', $titleString));
    } else {
      // Check if there's a hyphen separated by spaces:
      // Don't bother if there's more than one instance, this is too hard to parse.
      if (substr_count($titleString, ' - ') === 1) {
        list($partOne, $partTwo) = explode(' - ', $titleString);
        // Now the problem here is that either part could be the author's name.
        // For now we have to assume it's part two, and leave it to the user to correct if not.
        // I think Calibre does that too.
        // Maybe later check against a list of common names, e.g. https://github.com/hadley/data-baby-names
        $author = $partTwo;
        $title = trim($partOne);
      }
    }
    if ($author !== '') {
      $parsedAuthor = $this->parseAuthor($author);
    }
    
    return [
      'title' => $title,
      'lastName' => $parsedAuthor['lastName'],
      'firstName' => $parsedAuthor['firstName']
    ];
  }

  public function parseAuthor($author)
  {
    $author = trim($author);

    // Do we have a [last name, first name] format?
    if (strpos($author, ',') !== false) {
      list($lastName, $firstName) = explode(',', $author);
    } else {
      // Use a space:
      $nameArray = explode(' ', $author);
      $lastName = $nameArray[sizeof($nameArray) - 1];
      array_pop($nameArray);
      $firstName = implode(' ', $nameArray);
    }
    return [
      'lastName' => trim($lastName),
      'firstName' => trim($firstName)
    ];
  }

  public function parseMeta($str)
  {
    $return = [];

    if (stristr($str, 'page')) {
      preg_match("/page (\d*-?\d*)/", $str, $output);
      $return['page'] = $output[1];
    }

    if (stristr($str, 'location')) {
      preg_match("/location (\d*-?\d*)/", $str, $output);
      $return['location'] = $output[1];
    }

    if (stristr($str, 'added')) {
      preg_match("/Added on (.*)/", $str, $output);
      $return['date'] = $output[1];
    }
    
    return $return;
  }

}