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
            Delete Note
          </div>

          <div class="ui segment">
            <div class="ui very relaxed divided list">
              <p><strong>Are you sure you want to permanently delete this note? This action cannot be undone.</strong></p>
              <p>{{ $note->note }}</p>
              <p><strong>{{ $note->book->title }}</strong></p>
              <a href="/permadeleteNote/{{ $note->id }}" class="btn btn-danger">Permanently Delete</a>
              <a href="/deleted-items" class="btn btn-default">Cancel</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</home>
@endsection
