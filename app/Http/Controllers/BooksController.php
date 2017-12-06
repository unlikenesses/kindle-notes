<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Book;
use App\Note;
use Illuminate\Http\Request;

class BooksController extends Controller
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

  public function index()
  {
    $books = auth()->user()->books()->with('tags')->paginate(10);

    return view('show_books', ['books' => $books]);
  }

  public function getBookDetails(Request $request) 
  {
    $book = Book::findOrFail($request->id);

    $data = [
      'title' => $book->title, 
      'authorFirstName' => $book->author_first_name, 
      'authorLastName' => $book->author_last_name
    ];
    
    return $data;
  }

  public function storeBookDetails(Request $request) 
  {
    $this->validate($request, [
      'title' => 'required|max:255'
    ]);
      
    $book = Book::findOrFail($request->id);

    if ($book->user_id != auth()->user()->id) {
      return;
    }

    $data = [
      'title' => $request->title,
      'author_first_name' => $request->authorFirstName,
      'author_last_name' => $request->authorLastName
    ];  

    $book->update($data);
  }

  public function showBooksByTag(Tag $tag) 
  {
    $books = $tag->books()->with('tags')->paginate(10);
    
    return view('show_books', ['books' => $books, 'tag' => $tag->tag]);
  }
  
  /**
   * Search: not used currently because I couldn't get Laravel Scout
   * to search for both notes and books
   */
  public function search()
  {
    $searchTerm = request('q');
    $books = Book::search($searchTerm)->paginate(15);
    if (request()->expectsJson()) {
      return $books;
    }

    return view('show_books', ['searchTerm' => $searchTerm, 'books' => $books]);
  }
}
