<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Book;
use Illuminate\Http\Request;

class TagsController extends Controller
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

  public function addTagPivot(Request $request)
  {

    $this->validate($request, [
      'tag' => 'required|max:32'
    ]);
    $tag = $this->addNewTag($request->tag);
    $book = Book::findOrFail($request->book_id);
    try {
      $book->tags()->attach($tag->id);
    } catch (\Exception $e) {

    }
  }

  public function addNewTag($tag)
  {
    $tag = trim(strtolower($tag));

    $existingTag = $this->tagExists($tag);

    if ($existingTag) {
      return $existingTag;
    }

    $newTag = new Tag;
    $newTag->tag = $tag;
    $newTag->slug = str_slug($tag, '-');
    auth()->user()->tags()->save($newTag);

    return $newTag;
  }

  protected function tagExists($tag)
  {
    $tag = Tag::where('tag', $tag)->first();

    return $tag;
  }

  public function deleteTagPivot(Request $request)
  {
    $book = Book::findOrFail($request->book_id);

    $book->tags()->detach($request->tag_id);
  }

  public function getTagsForBook(Request $request)
  {
    $book = Book::findOrFail($request->book_id);

    return $book->tags()->get();
  }

  public function tagAutoComplete(Request $request)
  {
    $tag = $request->tag;
    $tags = Tag::where('tag', 'like', '%' . $tag . '%')->get();

    return response()->json($tags);
  }
}
