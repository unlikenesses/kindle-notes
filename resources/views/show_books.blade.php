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
                            <book :book="{{ json_encode($book) }}"></book>
                        @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</home>
@endsection
