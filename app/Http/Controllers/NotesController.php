<?php

namespace App\Http\Controllers;

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
    if ($book->user_id != auth()->user()->id) {
      return redirect('/home');
    }
    
    $notes = $book->notes()->get();

    return view('show_notes', ['book' => $book, 'notes' => $notes]);
  }

}
