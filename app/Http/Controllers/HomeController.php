<?php

namespace App\Http\Controllers;

class HomeController extends Controller
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
   * Show the application dashboard.
   *
   * @return Response
   */
  public function show()
  {
    $book_count = auth()->user()->books()->count();
    $note_count = auth()->user()->notes()->count();
    $first_book = auth()->user()->books()->orderBy('created_at', 'desc')->first();
    
    $last_upload = '';
    
    if ($first_book) {
      $last_upload = $first_book->created_at->format('l jS \\of F Y, \\a\\t h:i:s A');
    }

    return view('home', [
      'book_count' => $book_count, 
      'note_count' => $note_count, 
      'last_upload' => $last_upload
    ]);
  }

}
