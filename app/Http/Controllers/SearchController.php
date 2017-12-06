<?php

namespace App\Http\Controllers;

use App\Note;
use App\Book;

class SearchController extends Controller
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

  /**
   * Perform a search
   *
   * @return Response
   */
  public function index()
  {
    $userId = auth()->id();

    $searchTerm = request('q');

    $wildcardedTerm = '%' . $searchTerm . '%';

    $notes = Note::where('note', 'LIKE', $wildcardedTerm)
                  ->where('user_id', $userId)
                  ->get();
                  
    $books = Book::where('user_id', $userId)
                  ->where(function($query) use ($wildcardedTerm) {
                    $query->where('title_string', 'LIKE', $wildcardedTerm)
                        ->orWhere('title', 'LIKE', $wildcardedTerm)
                        ->orWhere('author_first_name', 'LIKE', $wildcardedTerm)
                        ->orWhere('author_last_name', 'LIKE', $wildcardedTerm);
                  })
                  ->get();

    if (request()->expectsJson()) {
      return [
        'books' => $books,
        'notes' => $notes
      ];
    }

    return view('search_results', [
      'searchTerm' => $searchTerm, 
      'books' => $books,
      'notes' => $notes
    ]);
  }
}
