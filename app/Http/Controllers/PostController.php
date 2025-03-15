<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\SlugGenerator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the posts with optional search functionality.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Post::with(['categories', 'user']);
        
        // Aplicar filtro de búsqueda si se proporciona
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('excerpt', 'like', "%{$searchTerm}%")
                  ->orWhere('content', 'like', "%{$searchTerm}%");
            });
        }
        
        // Filtrar por usuario autenticado si es necesario
        // Si quieres filtrar por el usuario actual, descomenta la siguiente línea
        // $query->where('user_id', Auth::id());
        
        $posts = $query->latest()->get();
        
        // Transformar la respuesta para que coincida con el formato esperado
        $formattedPosts = $posts->map(function ($post) {
            return [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'excerpt' => $post->excerpt,
                'categories' => $post->categories->pluck('name'),
                'user' => $post->user->name,
                'created_at' => $post->created_at
            ];
        });
        
        return response()->json($formattedPosts);
    }

    /**
     * Store a newly created post in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'required|string',
            'content' => 'required|string',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id'
        ]);
        
        // Crear el post con slug único
        $post = new Post();
        $post->title = $validated['title'];
        $post->slug = SlugGenerator::generateUniqueSlug($validated['title']);
        $post->excerpt = $validated['excerpt'];
        $post->content = $validated['content'];
        $post->user_id = Auth::id() ?? 1; // Fallback a user_id 1 si no hay usuario autenticado (para pruebas)
        $post->save();
        
        // Asociar categorías
        $post->categories()->attach($validated['categories']);
        
        // Recargar el post con las relaciones para la respuesta
        $post->load(['categories', 'user']);
        
        // Formatear la respuesta para que coincida con el formato esperado
        $formattedPost = [
            'id' => $post->id,
            'title' => $post->title,
            'slug' => $post->slug,
            'excerpt' => $post->excerpt,
            'content' => $post->content,
            'categories' => $post->categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name
                ];
            }),
            'user' => [
                'id' => $post->user->id,
                'name' => $post->user->name,
                'email' => $post->user->email
            ],
            'created_at' => $post->created_at,
            'updated_at' => $post->updated_at
        ];
        
        return response()->json($formattedPost, 201);
    }
}