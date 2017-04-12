@extends('spark::layouts.app')

@section('content')
<home :user="user" inline-template>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif
                <div class="panel panel-default">
                    <div class="panel-heading">Your Books</div>

                    <div class="panel-body">
                        <ul>
                        @foreach ($books as $book)
                            <book id="{{ $book->id }}"></book>
                            <!--<li>
                                 <a href="/books/{{ $book->id }}/notes">
                                    @if ($book->title !== '') 
                                        {{ $book->title }}<br>
                                        @if ($book->author_first_name !== '')
                                            {{ $book->author_first_name . ' ' }}
                                        @endif
                                        @if ($book->author_last_name !== '')
                                            {{ $book->author_last_name }}
                                        @endif
                                    @else
                                        {{ $book->title_string }}
                                    @endif
                                </a> 
                            </li>-->
                        @endforeach
                        </ul>

                        <?php //dd($books)?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</home>
@endsection
