<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>@yield('title', config('app.name'))</title>
        <meta name="description" content="Download free Bootstrap 4 multipurpose template Comply." />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://fonts.googleapis.com/css?family=Fredericka+the+Great" rel="stylesheet">
        <!--Bootstrap 4-->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">
        <!--icons-->
        <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" />
        <link rel="stylesheet" href="{{ asset('css/splash/style.css') }}" />
    </head>
    <body>
        <!--header-->
        <nav class="navbar navbar-toggleable-md navbar-inverse fixed-top sticky-navigation">
            <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="ion-grid icon-sm"></span>
            </button>
            <a class="navbar-brand hero-heading" href="#">Scrippd</a>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item mr-3">
                        <a class="nav-link page-scroll" href="#main">Product <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item mr-3">
                        <a class="nav-link page-scroll" href="#features">Features</a>
                    </li>
                    <li class="nav-item mr-3">
                        <a class="nav-link" href="/login">Login</a>
                    </li>
                    <li class="nav-item mr-3">
                        <a class="nav-link" href="/register">Register</a>
                    </li>
                </ul>
            </div>
        </nav>

        <!--main section-->
        <section class="bg-texture hero" id="main">
            <div class="container">
                <div class="row d-md-flex brand">
                    <div class="col-md-8 hidden-sm-down wow fadeIn">
                        <img class="img-fluid mx-auto d-block" src="{{ asset('img/splash/laptop.png') }}"/>
                    </div>
                    <div class="col-md-4 col-sm-12 text-white wow fadeIn">
                        <h2 class="pt-4">Manage your Kindle <b class="text-primary-light">notes</b> and <b class="text-primary-light">highlights</b>.</h2>
                        <p class="mt-5">
                        A free tool to manage your notes and highlights. Store, tag, search and export.
                        </p>
                        <p class="mt-5">
                            <a href="/login" class="btn btn-primary mr-2 mb-2 page-scroll">Login</a>
                            <a href="/register" class="btn btn-white mb-2 page-scroll">Register</a>
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!--features-->
        <section class="bg-faded" id="features">
            <div class="container">
                <div class="row mb-3">
                    <div class="col-md-6 offset-md-3 col-sm-8 offset-sm-2 col-xs-12 text-center wow fadeIn">
                        <h2 class="text-primary">Features</h2>
                    </div>
                </div>
                <div class="row mt-5 text-center">
                    <div class="col-md-4 wow fadeIn">
                        <div class="card">
                            <div class="card-block">
                                <div class="icon-box">
                                    <em class="ion-grid icon-md"></em>
                                </div>
                                <h6>View at a glance</h6>
                                <p>
                                Scrippd's dashboard lets you see at a single glance how many books, notes and highlights you've made. 
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 wow fadeIn">
                        <div class="card">
                            <div class="card-block">
                                <div class="icon-box">
                                    <em class="ion-ios-compose-outline icon-md"></em>
                                </div>
                                <h6>Inline data edit</h6>
                                <p>
                                    It's easy to edit the title and author details of your books without leaving the overview page.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 wow fadeIn">
                        <div class="card">
                            <div class="card-block">
                                <div class="icon-box">
                                    <em class="ion-ios-bookmarks-outline icon-md"></em>
                                </div>
                                <h6>Archive your notes</h6>
                                <p>
                                    Scrippd allows you to maintain a easy to consult archive of your notes and highlights independent of your device. 
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 wow fadeIn">
                        <div class="card">
                            <div class="card-block">
                                <div class="icon-box">
                                    <em class="ion-ios-pricetags-outline icon-md"></em>
                                </div>
                                <h6>Tag and organise</h6>
                                <p>
                                    Add, edit and delete tags for your books, and view books by tag.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 wow fadeIn">
                        <div class="card">
                            <div class="card-block">
                                <div class="icon-box">
                                    <em class="ion-ios-search-strong icon-md"></em>
                                </div>
                                <h6>Search</h6>
                                <p>
                                    Quickly find notes and books with Scrippd's search facility.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 wow fadeIn">
                        <div class="card">
                            <div class="card-block">
                                <div class="icon-box">
                                    <em class="ion-ios-cloud-download-outline icon-md"></em>
                                </div>
                                <h6>Export</h6>
                                <p>
                                    Export your notes to Excel with one click.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!--download-->
        <section class="bg-orange pt-0" id="download">
            <div class="container">
                <div class="row d-md-flex text-center wow fadeIn">
                    <div class="col-md-6 offset-md-3 col-sm-10 offset-sm-1 col-xs-12">
                        <h5 class="text-primary">Start archiving your ebook notes now.</h5>
                        <p class="mt-4">
                            <a href="/register" class="btn btn-primary mr-2 page-scroll">Get Started with Scrippd</a>
                        </p>
                        <p class="mt-5">
                            <a href="#" class="mr-2"><img src="" class="store-img"/></a>
                            <a href="#"><img src="" class="store-img"/> </a>
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!--footer-->
        <section class="bg-footer" id="connect">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 offset-md-3 col-sm-8 offset-sm-2 col-xs-12 text-center wow fadeIn">
                        <h1>Scrippd</h1>
                        <p class="mt-4">
                            <a href="https://twitter.com/scrippdApp" target="_blank"><em class="ion-social-twitter text-twitter-alt icon-sm mr-3"></em></a>
                        </p>
                        <p class="pt-2 text-muted">
                            &copy; 2017 Scrippd.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.js"></script>
        <script src="{{ asset('js/splash/scripts.js') }}"></script>
    </body>
</html>
