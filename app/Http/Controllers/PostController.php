<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'required|string',
            'content' => 'required|string',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id'
        ]);
        
        // Crear el post
        $post = new Post();
        $post->title = $validated['title'];
        $post->slug = Str::slug($validated['title']);
        $post->excerpt = $validated['excerpt'];
        $post->content = $validated['content'];
        $post->user_id = auth()->id();
        $post->save();
        
        // Asociar categorías
        $post->categories()->attach($validated['categories']);
        
        // Recargar el post con las relaciones para la respuesta
        $post->load(['categories', 'user']);
        
        return response()->json($post, 201);
    }
}