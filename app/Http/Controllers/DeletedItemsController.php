<?php

namespace App\Http\Controllers;

use App\Note;
use App\Book;

class DeletedItemsController extends Controller
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
   * Show deleted items.
   *
   * @return Response
   */
  public function index()
  {
    return view('deletedItems', [
      'notes' => Note::onlyTrashed()->where('user_id', auth()->id())->get(), 
      'books' => Book::onlyTrashed()->where('user_id', auth()->id())->get()
    ]);
  }

  public function restoreNote($deletedNote)
  {
    $note = Note::withTrashed()->findOrFail($deletedNote);
    
    $note->restore();

    return redirect('/deleted-items')->with('status', 'Your note has been restored.');
  }

}
