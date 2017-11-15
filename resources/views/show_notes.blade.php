@extends('spark::layouts.app')

@section('content')
<home :user="user" inline-template>
  <div class="container">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="ui segments">
          <div class="ui segment secondary">
            Your Notes for 
            @if ($book->title !== '') 
              {{ $book->title }}
              @if ($book->author_first_name !== '' || $book->author_last_name !== '')(@endif
              @if ($book->author_first_name !== '')
                {{ $book->author_first_name . ' ' }}
              @endif
              @if ($book->author_last_name !== '')
                {{ $book->author_last_name }}
              @endif
              @if ($book->author_first_name !== '' || $book->author_last_name !== ''))@endif
            @else
              {{ $book->title_string }}
            @endif
          </div>
          <div class="ui segment">
            <p>
              <a href="{{ url('csvExport/' . $book->id ) }}">
                <span class="glyphicon glyphicon-export" aria-hidden="true"></span> Export to CSV
              </a>
            </p>
            <div class="ui large feed">
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
                      @if ($note->page != '') <small>Page: {{ $note->page}}</small> @endif
                      @if ($note->location != '') <small>Location: {{ $note->location}}</small> @endif
                      @if ($note->type == 1) (Highlight) @endif
                      @if ($note->type == 2) (Note) @endif
                    </div>
                  </div>
                </div>
                <hr>
              @endforeach
            </div>
            <?php //dd($books)?>
          </div>
        </div>
      </div>
    </div>
  </div>
</home>
@endsection
