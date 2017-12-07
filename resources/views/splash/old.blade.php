<html>
  <head>
    <title>
      @yield('title', config('app.name'))
    </title>
    <meta charSet="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" type="text/css" href="/css/splash/styles.css">
  </head>
  <body>
    <div class="cook template">
      <header class="header themecolor-purple themefont-neutral">
        <div class="container-lrg">
          <div class="col-12 spread">
            <div>
              <a class="logo">
                Scrippd
              </a>
            </div>
            <div>
              <a class="nav-link" href="#">
                Twitter
              </a>
              <a class="nav-link" href="#">
                Facebook
              </a>
            </div>
          </div>
        </div>
        <div class="container-lrg flex">
          <div class="col-5">
            <h1 class="heading">
              Manage your ebook notes and highlights.
            </h1>
          </div>
          <div class="col-7">
            <h2 class="paragraph">
              A tool to download, store and view your notes and highlights. Currently Kindle only.
            </h2>
            <div class="ctas">
              <!-- <input class="ctas-input" type="text" placeholder="Your Email Address">
              <button class="ctas-button">
                Sign Up
              </button> -->
              <a href="/login" style="margin-right: 15px;">
                <button class="ctas-button">
                    Login
                </button>
              </a>

              <a href="/register">
                  <button class="ctas-button">
                      Register
                  </button>
              </a>
            </div>
          </div>
        </div>
        <div class="container-lrg">
          <div class="centerdevices col-12">
            <div class="iphoneipad">
              <div class="iphone">
                <div class="mask">
                  <img class="mask-img" src="img/mobileapp.svg">
                </div>
              </div>
              <div class="ipad">
                <div class="mask">
                  <img class="mask-img" src="img/webapp.svg">
                </div>
              </div>
            </div>
          </div>
        </div>
      </header>
    </div>
    <div class="feature3">
      <div class="container-lrg flex">
        <div class="col-4">
          <b class="emoji">
            📜
          </b>
          <h3 class="subheading">
            View at a glance
          </h3>
          <p class="paragraph">
            Scrippd's dashboard lets you see at a single glance how many books, notes and highlights you've made .
          </p>
        </div>
        <div class="col-4">
          <b class="emoji">
            ✏️
          </b>
          <h3 class="subheading">
            Inline data edit
          </h3>
          <p class="paragraph">
            It's easy to edit the title and author details of your books without leaving the overview page.
          </p>
        </div>
        <div class="col-4">
          <b class="emoji">
            💼
          </b>
          <h3 class="subheading">
            Archive your notes
          </h3>
          <p class="paragraph">
            Scrippd allows you to maintain a easy to consult archive of your notes and highlights independent of your device.
          </p>
        </div>
      </div>
    </div>
    <div class="socialproof">
      <div class="container-lrg">
        <div class="flex text-center">
          <div class="col-4">
            <h6 class="heading">
              20,923
            </h6>
            <b class="paragraph">
              Users
            </b>
          </div>
          <div class="col-4">
            <h6 class="heading">
              52,548
            </h6>
            <b class="paragraph">
              Books
            </b>
          </div>
          <div class="col-4">
            <h6 class="heading">
              110,552
            </h6>
            <b class="paragraph">
              Notes
            </b>
          </div>
        </div>
      </div>
    </div>
    <div class="footer">
      <div class="container-sml text-center">
        <div class="col-12">
          <h5 class="heading">
            Start archiving your ebook notes now.
          </h5>
          <div class="ctas">
            <a class="ctas-button" href="/login">
              Login
            </a>
            <a class="ctas-button-2" href="/register">
              Register
            </a>
          </div>
        </div>
      </div>
      <div class="container-sml footer-nav">
        <div class="col-12 text-center">
          <div>
            <a class="nav-link">
              Twitter
            </a>
            <a class="nav-link">
              Facebook
            </a>
            <a class="nav-link">
              Contact
            </a>
            <a class="nav-link">
              TOS
            </a>
            <a class="nav-link">
              Privacy
            </a>
          </div>
          <br>
          <div>
            <span>
              © 2017 Scrippd
            </span>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>