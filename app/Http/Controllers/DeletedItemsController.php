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

  /**
   * Restore a deleted note
   *
   * @param int $deletedNote
   * @return Response
   */
  public function restoreNote($deletedNote)
  {
    $note = Note::withTrashed()->findOrFail($deletedNote);
    
    if (auth()->id() != $note->user_id) {
      return response('Unauthorized', 403);
    }

    $note->restore();

    return redirect('/deleted-items')->with('status', 'Your note has been restored.');
  }

  /**
   * Show a confirmation page before permanently deleting note
   *
   * @param int $deletedNote
   * @return Response
   */
  public function confirmPermadeleteNote($deletedNote)
  {
    $note = Note::withTrashed()->findOrFail($deletedNote);
    
    if (auth()->id() != $note->user_id) {
      return response('Unauthorized', 403);
    }

    return view('confirmPermaDeleteNote', ['note' => $note]);
  }
  
  /**
   * Permanently delete a note
   *
   * @param int $deletedNote
   * @return Response
   */
  public function permadeleteNote($deletedNote)
  {
    $note = Note::withTrashed()->findOrFail($deletedNote);

    if (auth()->id() != $note->user_id) {
      return response('Unauthorized', 403);
    }

    $note->forceDelete();

    return redirect('/deleted-items')->with('status', 'Your note has been permanently deleted.');
  }

}
