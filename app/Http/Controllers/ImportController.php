<?php

namespace App\Http\Controllers;

use App\Annotations;
use App\PaperwhiteParser;
use Illuminate\Http\Request;
use App\Exceptions\BookDetailsAreTooLong;

class ImportController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');

    // $this->middleware('subscribed');
  }

  public function show()
  {
    return view('show_import');
  }

  public function importFile(Request $request)
  {
    if (!($request->hasFile('clippings_file') &&
          $request->file('clippings_file')->isValid())) {
      return null;
    }

    $this->validate($request, [
      'clippings_file' => 'required|mimes:txt|max:3000',
    ]);

    $parser = new PaperwhiteParser();

    try {
      $annotations = (new Annotations($request->file('clippings_file'), $parser))->processFile();
    } catch (\Exception $e) {
      return null;
    }

    try { 
      $annotations->save();
    } catch (BookDetailsAreTooLong $e) {
      return redirect('/import')->with('status', 'Could not upload file: one or more book titles is too long');
    } catch (\Exception $e) {
      return redirect('/import')->with('status', 'There was an error uploading the file');
    }

    $status = 'Imported ' . $annotations->numBooks . ' ' . str_plural('book', $annotations->numBooks);
    $status .= ' and ' . $annotations->numNotes . ' ' . str_plural('note', $annotations->numNotes);

    return redirect('/books')->with('status', $status);
  }  
}
