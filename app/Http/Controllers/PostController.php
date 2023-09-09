<?php

namespace App\Http\Controllers;

use App\Models\Post;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]); // block if its not authenticated
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $data = Post::all(); // pick all data on the model
        // $data = Post::where('folio_name', 'E-Pipeline')->get(); // pick by certain key
        // $data = Post::orderBy('folio_name', 'asc')->get(); // pick all data on the model with order
        // $data = Post::orderBy('folio_name', 'asc')->take(1)->get(); // pick 1 data on the model with order
        // $data = DB::select('SELECT * FROM posts'); // pick all data on the model by query
        $data = Post::orderBy('folio_name', 'asc')->paginate(2); // pick with perpage 1 data from the model with order
        return view('posts.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'folio_name' => 'required',
            'description' => 'required',
            'url' => 'required',
            'image_url' => 'image|nullable|max:1999',
            'created_at' => 'required',
        ]);

        // Handle file uploader

        if ($request->hasFile('image_url')) {
            $fileNameWithExt = $request->file('image_url')->getClientOriginalName(); // get filename with extension
            $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME); // get just filename
            $extension = $request->file('image_url')->getClientOriginalExtension(); // get just extension
            $fileNameToStore = $fileName . '_' . time() . '.' . $extension; // file name to store avoid same name
            $path = $request->file('image_url')->storeAs('public/cover_images', $fileNameToStore); // save image with directory
        } else {
            $fileNameToStore = 'noimage.jpg';
        }

        $user_id = auth()->user()->id;

        $post = new Post;
        $post->folio_name = $request->input('folio_name');
        $post->description = $request->input('description');
        $post->url = $request->input('url');
        $post->image_url = $fileNameToStore;
        $post->created_at = $request->input('created_at');
        $post->user_id = $user_id;
        $post->save();

        return redirect('/post')->with('success', 'Portofolio Created');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Post::find($id);
        return view('posts.detail', compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = Post::find($id);
        $user_id = auth()->user()->id;

        if (Auth::user()->id !== $data->user_id) {
            return redirect('/post')->with('error', 'Unauthorized Page');
        } else {
            return view('posts.edit', compact('data'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'folio_name' => 'required',
            'description' => 'required',
            'url' => 'required',
            'image_url' => 'image|nullable|max:1999',
            'created_at' => 'required'
        ]);

        // Handle file uploader

        if ($request->hasFile('image_url')) {
            $fileNameWithExt = $request->file('image_url')->getClientOriginalName(); // get filename with extension
            $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME); // get just filename
            $extension = $request->file('image_url')->getClientOriginalExtension(); // get just extension
            $fileNameToStore = $fileName . '_' . time() . '.' . $extension; // file name to store avoid same name
            $path = $request->file('image_url')->storeAs('public/cover_images', $fileNameToStore); // save image with directory
        }

        $post = Post::find($id);
        $post->folio_name = $request->input('folio_name');
        $post->description = $request->input('description');
        $post->url = $request->input('url');
        if ($request->hasFile('image_url')) {
            $post->image_url = $fileNameToStore;
        }
        $post->created_at = $request->input('created_at');
        $post->save();

        return redirect('/post')->with('success', 'Portofolio Edited');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = POST::find($id);

        if (Auth::user()->id !== $data->user_id) {
            return redirect('/post')->with('error', 'Unauthorized Page');
        } else {
            $data->delete();

            if ($data->image_url != 'noimage.jpg' && !str_contains($data->image_url, 'https')) {
                Storage::delete('public/cover_images/' . $data->image_url); // use . to template literals like in js
            }
            return redirect('/post')->with('success', 'Portofolio Removed');
        }
    }
}