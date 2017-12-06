<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Book;
use App\User;
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
      auth()->user()->tags()->attach($tag->id);
    } catch (\Exception $e) {

    }

    return response(['id' => $tag->id]);
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
    $newTag->save();

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

    $this->authorize('update', $book);

    $book->tags()->detach($request->tag_id);

    $detached = $this->detachTagFromUserIfNecessary($request->tag_id);
    
    if ($detached) $this->deleteTagIfLastOfKind($request->tag_id);
  }

  public function getTagsForBook(Request $request)
  {
    $book = Book::findOrFail($request->book_id);

    return $book->tags()->get();
  }

  public function tagAutoComplete(Request $request)
  {
    $userId = auth()->id();
    $tag = $request->tag;
    $tags = Tag::where('tag', 'like', '%' . $tag . '%')
              ->whereHas('users', function($query) use ($userId) {
                $query->where('id', $userId);
              })
              ->get();

    return response()->json($tags);
  }

  /**
   * If a tag has been deleted, we need to see if it exists on other books,
   * and if it doesn't exist on other books, detach it from that user.
   */
  protected function detachTagFromUserIfNecessary($tagId)
  {
    $bookCount = Book::where('user_id', auth()->id())
                  ->whereHas('tags', function($query) use ($tagId) {
                    $query->where('id', $tagId);
                  })
                  ->count();
    
    if ($bookCount == 0) {
      auth()->user()->tags()->detach($tagId);
    }

    return $bookCount == 0;
  }

  /**
   * If a given tag that has been removed from one user does not exist for other users, 
   * delete it from the database.
   */
  protected function deleteTagIfLastOfKind($tagId)
  {
    $userCount = User::whereHas('tags', function($query) use ($tagId) {
                    $query->where('id', $tagId);
                  })
                  ->count();
    
    if ($userCount == 0) {
      Tag::findOrFail($tagId)->delete();
    }
  }
}
