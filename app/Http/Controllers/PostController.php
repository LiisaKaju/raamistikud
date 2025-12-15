<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('posts/Index', [
            'posts'=>Post::with('author:id,first_name,last_name')->paginate(30),
        ]);
    }
    

    /**
     * Show the form for creating a new resource.
     */
    public function create()

    {
        return Inertia::render ('posts/Create', [
            'authors' => Author::all()->mapWithKeys(fn ($author)=> [$author->id => $author->first_name.' '.$author->last_name])
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Post::create($request->validate([
            'title'=>'required|string|max:255',
            'content' =>'required|string',
            'author_id' =>'required|integer|exists:authors,id',
            'published'=>'boolean',
        ]));

        return redirect()->route('posts.index');
    }


  

    public function show(Post $post)
    {
        return Inertia::render('posts/Show', [
            'post'=> $post->loadMissing('author', 'comments.user')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        
        return Inertia::render('posts/Edit', [
        'post' => $post, // <-- saadab Vue komponendile post objekti
          'authors' => Author::all()->mapWithKeys(fn ($author)=> [$author->id => $author->first_name.' '.$author->last_name])
    ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'author_id' => 'required|string|max:255',
        'published' => 'boolean',
    ]);

    $post->update($validated);

    return redirect()->route('posts.index')->with('success', 'Post updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
         $post->delete();

    return redirect()->route('posts.index')->with('success', 'Post deleted successfully.');
    }
}
