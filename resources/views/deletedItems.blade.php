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
            Deleted Items
          </div>

          <div class="ui segment">
            <div class="ui very relaxed divided list">
              @if (sizeof($books) < 1 && sizeof($notes) < 1) 
                <h4>There are no deleted items at present.</h4>
              @else
                @if ($notes)
                  <h4>Notes</h4>
                  @foreach ($notes as $note)
                    <p>{{ $note->note }}</p>
                    <p><strong>{{ $note->book->title }}</strong></p>
                    <a href="/restoreNote/{{ $note->id }}" class="btn btn-default">Restore</a>
                    <hr>
                  @endforeach
                @endif
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</home>
@endsection
