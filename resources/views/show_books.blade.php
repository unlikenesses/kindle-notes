@extends('spark::layouts.app')

@section('content')
<home :user="user" inline-template>
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Your Books
                        @if (isset($tag))
                            tagged &ldquo;{{ $tag }}&rdquo;
                        @endif
                    </div>

                    <div class="panel-body">
                        <div class="ui relaxed divided list">
                        @foreach ($books as $book)
                            <book :book="{{ json_encode($book) }}"></book>
                        @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</home>
@endsection
