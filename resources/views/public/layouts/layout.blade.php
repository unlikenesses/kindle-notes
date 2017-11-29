<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TrelloTime</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.2.3/css/bulma.min.css">
    <link title="timeline-styles" rel="stylesheet" href="https://cdn.knightlab.com/libs/timeline3/latest/css/timeline.css">
    <script src="https://cdn.knightlab.com/libs/timeline3/latest/js/timeline.js"></script>
    <script src="https://code.jquery.com/jquery.js"></script>
    <script src="{{ url('js/main.js') }}"></script>
</head>
<body>
    <nav class="nav">
      <div class="nav-left">
        <a href="#" class="nav-item is-brand">
          TrelloTime
        </a>
        <a class="nav-item is-tab is-active" href="#">
          Home
        </a>
        <a class="nav-item is-tab" href="#">
          Upload JSON
        </a>
      </div>

      <div class="nav-center">
        <a class="nav-item" href="#">
          <span class="icon">
            <i class="fa fa-github"></i>
          </span>
        </a>
        <a class="nav-item" href="#">
          <span class="icon">
            <i class="fa fa-twitter"></i>
          </span>
        </a>
      </div>

      <span class="nav-toggle">
        <span></span>
        <span></span>
        <span></span>
      </span>

      <div class="nav-right nav-menu">


        @if (Auth::guest())
                  <li><a href="{{ url('/login') }}">Login</a></li>
                  <li><a href="{{ url('/register') }}">Register</a></li>
              @else
                  <li class="dropdown">
                      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                          {{ Auth::user()->name }} <span class="caret"></span>
                      </a>

                      <ul class="dropdown-menu" role="menu">
                          <li>
                              <a href="{{ url('/logout') }}"
                                  onclick="event.preventDefault();
                                           document.getElementById('logout-form').submit();">
                                  Logout
                              </a>

                              <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                  {{ csrf_field() }}
                              </form>
                          </li>
                      </ul>
                  </li>
              @endif
      </div>


    </nav>
    <div class="container">

        <p><strong>Current JSON file: example.json</strong></p>

        @yield('content')


    </div>
</body>
</html>
