@extends('public.layouts.layout')

@section('content')
<div class="container">
    <div class="box">
        <div class="card is-fullwidth">
            <header class="card-header">
                <p class="card-header-title">Login</p>
            </header>
            <div class="card-content">
                <form role="form" method="POST" action="{{ url('/login') }}">
                    {{ csrf_field() }}

                    <div class="{{ $errors->has('email') ? 'has-error' : '' }}">
                        <label for="email" class="label">E-Mail Address</label>

                        <p class="control">
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>

                            @if ($errors->has('email'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </p>
                    </div>

                    <div class="{{ $errors->has('password') ? 'has-error' : '' }}">
                        <label for="password" class="label">Password</label>

                        <p class="control">
                            <input id="password" type="password" name="password" required>

                            @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </p>
                    </div>

                    <p class="control">
                        <label class="checkbox">
                            <input type="checkbox" name="remember"> Remember Me
                        </div>
                    </p>

                    <p class="control">
                        <button type="submit" class="button is-primary">
                            Login
                        </button>

                        <a class="btn btn-link" href="{{ url('/password/reset') }}">
                            Forgot Your Password?
                        </a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
