@extends('spark::layouts.app')

@section('content')
<home :user="user" inline-template>
  <div class="container">
    <!-- Application Dashboard -->
    <div class="row">
      <div class="col-md-8 col-md-offset-2">
        @if (count($errors) > 0)
          <div class="alert alert-danger">
            <ul>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif
        <div class="ui segments">
          <div class="ui segment secondary">Import File</div>

          <div class="ui segment">

            <form action="/import" method="post" enctype="multipart/form-data" class="ui form">
              {{ csrf_field() }}
              <div class="field">
                <input type="file" name="clippings_file">
              </div>
              <div class="field">
                <input type="checkbox" name="parse_author" checked>
                <label>
                  Attempt to extract author and title <kbd data-toggle="popover" title="Popover title" data-content="And here's some amazing content. It's very engaging. Right?">?</kbd>
                </label>
              </div>
              <button class="ui button" type="submit">Import</button>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
</home>
@endsection
