@extends('spark::layouts.app')

@section('content')
<home :user="user" inline-template>
  <div class="container">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="ui segments">
          <div class="ui segment secondary">
            Your Notes for 
            <strong>
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
            </strong>
          </div>
          <div class="ui segment">
            <p>
              <a href="{{ url('csvExport/' . $book->id ) }}">
                <span class="glyphicon glyphicon-export" aria-hidden="true"></span> Export to CSV
              </a>
            </p>
            <div class="ui large feed">
              @forelse ($notes as $note)
                <note :note="{{ json_encode($note) }}"></note>
              @empty
                <p>There are no notes for this book.</p>
              @endforelse
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</home>
@endsection
