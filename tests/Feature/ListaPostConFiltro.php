<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserPostController extends Controller
{
    /**
     * Listar todos los posts de un usuario con filtros opcionales
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, User $user)
    {
        // Iniciar la consulta con los posts del usuario
        $query = $user->posts();

        // Filtrar por título si se proporciona
        if ($request->has('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        // Filtrar por categoría si se proporciona
        if ($request->has('category')) {
            $categoryId = $request->category;
            $query->whereHas('categories', function ($q) use ($categoryId) {
                $q->where('categories.id', $categoryId);
            });
        }

        // Filtrar por rango de fechas
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Obtener los resultados
        $posts = $query->get();

        // Devolver respuesta JSON
        return response()->json([
            'data' => $posts
        ]);
    }
}