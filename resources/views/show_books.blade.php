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
        <div class="ui segments">
          <div class="ui segment secondary">
            Your Books
            @if (isset($tag))
              tagged &ldquo;{{ $tag }}&rdquo;
            @endif
            <form method="GET" action="/books/search">
                <div class="form-group">
                    <input type="text" name="q" placeholder="Search books..." required>
                </div>
                <button type="submit" class="">Submit</button>
                @if (isset($searchTerm))
                  <a href="/books">Clear search</a>
                @endif
            </form> 
          </div>

          <div class="ui segment">
            <div class="ui very relaxed divided list">
            @forelse ($books as $book)
              <book :book="{{ json_encode($book) }}"></book>
            @empty
              <p>You have no books in the system.</p>
            @endforelse
            </div>
            {{ $books->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
</home>
@endsection
