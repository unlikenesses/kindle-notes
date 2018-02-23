<?php

namespace App;

interface Parser
{
    public function parseFile($fileHandle);

    public function bookExistsInFile($bookToFind, $books);

    public function parseTitle($titleString);

    public function parseAuthor($author);

    public function parseMeta($str);
}