<?php

namespace App\Http\Controllers;

use App\Book;
use Illuminate\Http\Request;

class ExportController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

        // $this->middleware('subscribed');
    }

    public function csvExport(Book $book) 
    {
        $csv = \League\Csv\Writer::createFromFileObject(new \SplTempFileObject());
        $csv->setOutputBOM(\League\Csv\Reader::BOM_UTF8);
        // Book details:
        $details = $book->title_string;
        if ($book->title !== '') {
            $details = $book->title;
            if ($book->author_first_name !== '' || $book->author_last_name !== '') {
                $details .= ' (';
            }
            if ($book->author_first_name !== '') {
                $details .= $book->author_first_name . ' ';
            }
            if ($book->author_last_name !== '') {
                $details .= $book->author_last_name;
            }
            if ($book->author_first_name !== '' || $book->author_last_name !== '') {
                $details .= ')';
            }
        }

        $csv->insertOne([$details]);

        // Notes:
        $notes = $book->notes()->get();
        $csv->insertOne(['Type', 'Date', 'Note']);
        foreach ($notes as $note) {
            if ($note->type == 1) {
                $noteType = 'Highlight';
            }
            if ($note->type == 2) {
                $noteType = 'Note';
            }
            $noteDate = date('d M Y, h:m:s',strtotime($note->date));
            $csv->insertOne([$noteType, $noteDate, $note->note]);
        }

        $csv->output('notes.csv');
    }
}
