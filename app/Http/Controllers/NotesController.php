<?php

namespace App\Http\Controllers;

use DB;
use App\Tag;
use App\Book;
use App\Note;
use Illuminate\Http\Request;

class NotesController extends Controller
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

  public function index(Book $book)
  { 
    $this->authorize('view', $book);

    $notes = $book->notes()->get();

    return view('show_notes', ['book' => $book, 'notes' => $notes]);
  }

  /**
   * Search notes: not used currently because I couldn't get Laravel Scout
   * to search for both notes and books
   */
  public function search()
  {
    $searchTerm = request('q');
    // dd(Note::all());
    // dd($searchTerm);
    // print_r(Note::all()->last());
    // DB::enableQueryLog();
    // $results = Note::search($searchTerm)->get();
    // $query = DB::getQueryLog(); $query = end($query);
    // dd($query);
    $notes = Note::search($searchTerm)->paginate(15);
    if (request()->expectsJson()) {
      return $notes;
    }

    return view('notes_search_results', ['searchTerm' => $searchTerm, 'notes' => $notes]);
  }

}
