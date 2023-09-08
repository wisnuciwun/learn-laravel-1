<?php

namespace App\Http\Controllers;

use App\Models\Post;
use DB;
use Illuminate\Http\Request;

class PostController extends Controller
{
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
            'image_url' => 'required',
            'created_at' => 'required'
        ]);

        $post = new Post;
        $post->folio_name = $request->input('folio_name');
        $post->description = $request->input('description');
        $post->url = $request->input('url');
        $post->image_url = $request->input('image_url');
        $post->created_at = $request->input('created_at');
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
        return view('posts.edit', compact('data'));
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
            'image_url' => 'required',
            'created_at' => 'required'
        ]);

        $post = Post::find($id);
        $post->folio_name = $request->input('folio_name');
        $post->description = $request->input('description');
        $post->url = $request->input('url');
        $post->image_url = $request->input('image_url');
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
        $data->delete();
        return redirect('/post')->with('success', 'Portofolio Removed');
    }
}