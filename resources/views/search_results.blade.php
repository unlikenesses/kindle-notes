@extends('spark::layouts.app')

@section('content')
<home :user="user" inline-template>
  <div class="container">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="ui segments">
          <div class="ui segment secondary">
            Search results for <strong>{{ $searchTerm }}</strong>:
          </div>
          <div class="ui segment">
            <div class="ui large feed">
              @if (sizeof($books) > 0)
                <h4>Books</h4>
                @foreach ($books as $book)
                  <div class="event">
                    <div class="label">
                      <i class="book icon"></i>
                    </div>
                    <div class="content">
                      <div class="summary">
                        <a href="/books/{{ $book->id }}/notes">
                          {{ $book->title }}
                        </a>
                      </div>
                      <div class="meta">
                        {{ $book->author_first_name }} {{ $book->author_last_name }}
                      </div>
                    </div>
                  </div>
                  <hr>
                @endforeach
              @endif
              @if (sizeof($notes) > 0)
                <h4>Notes</h4>
                @foreach ($notes as $note)
                  <div class="event">
                    <div class="label">
                      <i class="pencil icon"></i>
                    </div>
                    <div class="content">
                      <div class="date">
                        {{ date('d F   Y, h:m:s', strtotime($note->date)) }}
                      </div>
                      <div class="summary">
                        {{ $note->note }}
                      </div>
                      <div class="meta">
                        <a href="/books/{{ $note->book->id }}/notes">
                          <strong>{{ $note->book->title }}</strong>
                        </a>
                        @if ($note->page != '') <small>Page: {{ $note->page}}</small> @endif
                        @if ($note->location != '') <small>Location: {{ $note->location}}</small> @endif
                        @if ($note->type == 1) (Highlight) @endif
                        @if ($note->type == 2) (Note) @endif
                      </div>
                    </div>
                  </div>
                  <hr>
                @endforeach
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</home>
@endsection
