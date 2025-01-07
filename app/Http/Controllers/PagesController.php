<?php

namespace App\Http\Controllers;

use App\Models\PsrNews;
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
            // 'subTitle' => 'Hi, here is my website to show up',
            'listPortofolio' => ['E-Pipeline', 'Web Pareto AR', 'Web Todo List', 'Tokodistributor Desktop', 'Scrin', 'SCFS']
        );

        // return view('pages/index'); another options to write
        // return view('pages.index')->with('subTitle', $subTitle); or you can passing a variable like this
        return view('pages.index', compact('connect', 'data', 'linkedinUrl')); // you can add any other value like this
    }

    public function postNews()
    {

    }

    public function getAllNews()
    {
        try {
            $news = PsrNews::select('id', 'title', 'body', 'author', 'created_at')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $news,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve news.',
            ], 500);
        }
    }

    public function getDetailNews($id)
    {
        try {
            $news = PsrNews::find($id);

            if (!$news) {
                return response()->json([
                    'success' => false,
                    'message' => 'News not found.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $news,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve news details.',
            ], 500);
        }
    }
}