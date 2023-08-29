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
        $data = Post::orderBy('folio_name', 'asc')->paginate(1); // pick with perpage 1 data from the model with order
        return view('posts.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}