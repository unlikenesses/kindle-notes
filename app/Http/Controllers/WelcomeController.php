<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WelcomeController extends Controller
{
  /**
   * Show the application splash screen.
   *
   * @return Response
   */
  public function show()
  {
    return view('splash.index');
  }

  public function plain()
  {
    return view('splash.plain');
  }
}
