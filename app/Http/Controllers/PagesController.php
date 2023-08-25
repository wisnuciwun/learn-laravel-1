<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    // inside here called method

    public function index() {
        return 'TRIAL CONTROLLER';
    }

    public function welcome() {
        // get view from pages folder
        // return view('pages/index'); another options to write
        return view('pages.index');
    }
}
