<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    // inside here called method

    public function index()
    {
        return 'TRIAL CONTROLLER';
    }

    // get view from pages folder
    public function welcome()
    {
        $connect = 'Or you can visit my LinedIn profile here :';
        $linkedinUrl = 'https://www.linkedin.com/in/wisnu-adi-wardhana-560473163/';
        $data = array(
            'subTitle' => 'Hi, here is my website for show up my portofolios',
            'listPortofolio' => ['E-Pipeline', 'Web Pareto AR', 'Web Todo List', 'Tokodistributor Desktop', 'Scrin', 'SCFS']
        );

        // return view('pages/index'); another options to write
        // return view('pages.index')->with('subTitle', $subTitle); or you can passing a variable like this
        return view('pages.index', compact('connect', 'data', 'linkedinUrl')); // you can add any other value like this
    }
}