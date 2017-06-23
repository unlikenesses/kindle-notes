<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Book;
use App\Note;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public $upload_dir = 'uploads';

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
        $last_upload = $first_book->created_at->format('l jS \\of F Y, \\a\\t h:i:s A');

        return view('home', ['book_count' => $book_count, 'note_count' => $note_count, 'last_upload' => $last_upload]);
    }

    public function show_import()
    {
        return view('show_import');
    }

    public function show_books()
    {
        $books = auth()->user()->books()->with('tags')->get();

        return view('show_books', ['books' => $books]);
    }

    public function show_notes(Book $book)
    {
        $notes = $book->notes()->get();

        return view('show_notes', ['book' => $book, 'notes' => $notes]);
    }

    public function import_file(Request $request)
    {
        if ($request->hasFile('clippings_file') && $request->file('clippings_file')->isValid()) {

            $parse_author = false;

            $this->validate($request, [
                'clippings_file' => 'required|mimes:txt',
            ]);

            if ($request->parse_author) {
                $parse_author = true;
            }

            $file = $request->file('clippings_file');

            $name = time() . $file->getClientOriginalName();

            $file->move($this->upload_dir, $name);

            $data = array('books' => $this->parse_file($name));

            $result = $this->save_book_data($data, $parse_author);

            $status = 'Imported ' . $result['books'] . ' book';
            if ($result['books'] < 1 || $result['books'] > 1) $status .= 's';
            $status .= ' and ' . $result['notes'] . ' note';
            if ($result['notes'] < 1 || $result['notes'] > 1) $status .= 's';
            $status .= '.';

            return redirect('/books')->with('status', $status);
        }
    }

    private function save_book_data($data, $parse_author = false)
    {
        $num_notes = $num_books = 0;
        $user_id = auth()->user()->id;
        foreach ($data['books'] as $row) {
            $new_book = false;
            $book_title = $row['book'];
            $book = $this->get_book_by_title_string($book_title, $user_id);
            if (!$book) {
                $num_books++;
                $new_book = true;
                $book = new Book;
                $book->title_string = $book_title;
                if ($parse_author) {
                    $parsed = $this->parse_title($book_title);
                    $book->title = $parsed['title'];
                    $book->author_first_name = $parsed['first_name'];
                    $book->author_last_name = $parsed['last_name'];
                }
                auth()->user()->books()->save($book);
            } 
            foreach ($row['notes'] as $note) {
                if ($new_book || ! $this->note_exists($note, $user_id)) {
                    $num_notes++;
                    $date = '';
                    $page = '';
                    $location = '';
                    $note_text = '';
                    $note_type = 0;
                    if (isset($note['meta']['date'])) {
                        $timestamp = strtotime($note['meta']['date']);
                        $date = date('Y-m-d H:i:s', $timestamp);
                    }
                    if (isset($note['meta']['page'])) {
                        $page = $note['meta']['page'];
                    }
                    if (isset($note['meta']['location'])) {
                        $location = $note['meta']['location'];
                    }
                    if (isset($note['highlight'])) {
                        $note_text = trim($note['highlight']);
                    }
                    if (isset($note['type'])) {
                        $note_type = trim($note['type']);
                    }
                    $new_note = new Note;
                    $new_note->date = $date;
                    $new_note->page = $page;
                    $new_note->location = $location;
                    $new_note->note = $note_text;
                    $new_note->type = $note_type;
                    $new_note->user_id = $user_id;
                    $book->notes()->save($new_note);
                } else {
                    if (isset($note['meta'])) {
                        // echo 'Skipping note ' . $note['meta']['date'] . '<br>';
                    }
                }
            }
        }
        // die();
        return array('books' => $num_books, 'notes' => $num_notes);
    }

    private function get_book_by_title_string($title_string, $user_id)
    {
        $book = Book::where(['title_string' => $title_string, 'user_id' => $user_id])->first();

        return $book;
    }

    private function note_exists($note, $user_id)
    {
        if (isset($note['highlight'])) {
            $test_note = Note::where(['note' => trim($note['highlight']), 'user_id' => $user_id])->first();
        }

        if (! $test_note) {
            return false;
        }

        return true;
    }

    private function parse_file($filename)
    {
        $book = $books = $clipping = array();
        $in_book = FALSE;
        $fh = fopen($this->upload_dir . '/' . $filename, 'r');
        if ($fh)
        {
            while ($line = fgets($fh))
            {
                if ( ! $in_book)
                {
                    $existing_book_pos = -1;
                    // This is a book title. Check if it's already in the `books` array:
                    $c = 0;
                    foreach ($books as $temp_book)
                    {
                        if ($temp_book['book'] == trim($line))
                        {
                            $book = $temp_book;
                            $existing_book_pos = $c;
                        }
                        $c++;
                    }
                    if ($existing_book_pos < 0)
                    {
                        $book['book'] = trim($line);
                        $book['notes'] = array();
                    }
                    $in_book = TRUE;
                }
                else if (trim($line) == '==========')
                {
                    // End of a clipping.
                    $in_book = FALSE;
                    if ($existing_book_pos < 0)
                    {
                        $books[] = $book;
                    }
                    else
                    {
                        $books[$existing_book_pos] = $book;
                    }
                    $book = array();
                }
                else if (stristr($line, 'your highlight'))
                {
                    //  Location:
                    $clipping['meta'] = $this->parse_meta($line);
                    $clipping['type'] = 1;
                }
                else if (stristr($line, 'your note'))
                {
                    $clipping['meta'] = $this->parse_meta($line);
                    $clipping['type'] = 2;
                }
                else if (strlen(trim($line)) > 0)
                {
                    // Highlight:
                    $clipping['highlight'] = $line;
                    $book['notes'][] = $clipping;
                    $clipping = array();
                }
            }
        }
        else
        {
            echo 'Error opening file';
        }
        fclose($fh);
        
        return $books;
    }

    private function parse_meta($str)
    {
        $return = array();
        if (stristr($str, 'page'))
        {
            preg_match("/page (\d*-?\d*)/", $str, $output);
            $return['page'] = $output[1];
        }
        if (stristr($str, 'location'))
        {
            preg_match("/location (\d*-?\d*)/", $str, $output);
            $return['location'] = $output[1];
        }
        if (stristr($str, 'added'))
        {
            preg_match("/Added on (.*)/", $str, $output);
            $return['date'] = $output[1];
        }
        
        return $return;
    }

    private function parse_title($original_title)
    {
        // The idea is to split the title field into title string + author string.
        // Based on my sample size of 27, authors are typically separated by a hyphen or brackets.
        // Brackets are more common.
        // Title strings can contain hyphens AND brackets. E.g. a hyphen for a date range, then author in brackets.
        // Title strings can also contain more than 1 instance of the separator used to designate the author:
        // e.g. if the author separator is a hyphen, there may be more than 1 hyphen ("Century of Revolution, 1603-1714 - Christopher Hill").
        // e.g. same for brackets ("Rights of War and Peace (2005 ed.) vol. 1 (Book I) (Hugo Grotius)").
        // So we take the last instance of the separator as the author.
        // This will fail in some instances: e.g. "Harvey, David - A brief history of neoliberalism". But this seems an exception.

        $author = '';
        $title = '';
        $last_name = '';
        $first_name = '';
        // Check if the title ends with a closing bracket:
        if (substr($original_title, -1) === ')') {
            preg_match('/\(([^)]*)\)[^(]*$/', $original_title, $out);
            $author = $out[sizeof($out) - 1];
            $title = trim(str_replace('(' . $author . ')', '', $original_title));
        } else {
            // Check if there's a hyphen separated by spaces:
            // Don't bother if there's more than one instance, this is too hard to parse.
            if (substr_count($original_title, ' - ') === 1) {
                list($part_one, $part_two) = explode(' - ', $original_title);
                // Now the problem here is that either part could be the author's name.
                // For now we have to assume it's part two, and leave it to the user to correct if not.
                // I think Calibre does that too.
                // Maybe later check against a list of common names, e.g. https://github.com/hadley/data-baby-names
                $author = $part_two;
                $title = trim($part_one);
            }
        }
        if ($author !== '') {
            $author = trim($author);
            // Do we have a [last name, first name] format?
            if (strpos($author, ',') !== false) {
                list($last_name, $first_name) = explode(',', $author);
            } else {
                // Use a space:
                $name_array = explode(' ', $author);
                $last_name = $name_array[sizeof($name_array) - 1];
                array_pop($name_array);
                $first_name = implode(' ', $name_array);
            }
            $last_name = trim($last_name);
            $first_name = trim($first_name);
        }
        
        return [
            'title' => $title,
            'last_name' => $last_name,
            'first_name' => $first_name
        ];
    }

    public function getBookDetails(Request $request) 
    {
        $book = Book::findOrFail($request->id);
        $data = ['title' => $book->title, 'authorFirstName' => $book->author_first_name, 'authorLastName' => $book->author_last_name];
        
        return $data;
    }

    public function storeBookDetails(Request $request) 
    {
        $book = Book::findOrFail($request->id);
        $data = [
            'title' => $request->title,
            'author_first_name' => $request->authorFirstName,
            'author_last_name' => $request->authorLastName
        ];  
        $book->update($data);
    }

    public function showBooksByTag(Tag $tag) 
    {
        $books = $tag->books()->with('tags')->get();
        
        return view('show_books', ['books' => $books, 'tag' => $tag->tag]);
    }
}
