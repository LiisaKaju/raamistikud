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
            'posts' => Post::withCount('comments')->latest()->paginate(30),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('posts/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $authorId = Author::query()->value('id');
        if ($authorId === null) {
            $authorId = Author::create([
                'first_name' => 'Vaikimisi',
                'last_name' => 'Autor',
                'date_of_birth' => '2000-01-01',
            ])->id;
        }

        Post::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'author_id' => $authorId,
            'published' => false,
        ]);

        return redirect()->route('posts.index');
    }

    public function show(Post $post)
    {
        return Inertia::render('posts/Show', [
            'post' => $post->loadMissing(['comments.user']),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        return Inertia::render('posts/Edit', [
            'post' => $post,
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
