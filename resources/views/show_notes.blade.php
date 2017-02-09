@extends('spark::layouts.app')

@section('content')
<home :user="user" inline-template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
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
                    <div class="panel-body">
                        @foreach ($notes as $note)
                            <p>
                                <strong>
                                    {{ date('d M Y, h:m:s',strtotime($note->date)) }}
                                    @if ($note->type == 1) (Highlight) @endif
                                    @if ($note->type == 2) (Note) @endif
                                </strong><br>
                                @if ($note->page != '') <small>Page: {{ $note->page}}</small><br> @endif
                                @if ($note->location != '') <small>Location: {{ $note->location}}</small><br> @endif
                                {{ $note->note }}
                            </p>
                            <hr>
                        @endforeach
                        <?php //dd($books)?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</home>
@endsection
