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
                <div class="panel panel-default">
                    <div class="panel-heading">Import File</div>

                    <div class="panel-body">

                        <form action="/import" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <input type="file" name="clippings_file">
                            </div>
                            <div class="form-group">
                                <input type="checkbox" name="parse_author"> Attempt to extract author and title <kbd data-toggle="popover" title="Popover title" data-content="And here's some amazing content. It's very engaging. Right?">?</kbd>
                            </div>
                            <div class="form-group">
                                <input type="submit" class="btn btn-info" value="Import">
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</home>
@endsection
