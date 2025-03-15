<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Models\Post;

class SlugGenerator
{
    /**
     * Generate a unique slug based on a title
     *
     * @param string $title
     * @return string
     */
    public static function generateUniqueSlug(string $title): string
    {
        // Convertir el título a slug base
        $baseSlug = Str::slug($title);
        
        // Si no existe un post con ese slug, lo devolvemos
        if (!Post::where('slug', $baseSlug)->exists()) {
            return $baseSlug;
        }
        
        // Si ya existe, generamos un slug único añadiendo un número incremental
        $counter = 1;
        $newSlug = $baseSlug;
        
        while (Post::where('slug', $newSlug)->exists()) {
            $newSlug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        return $newSlug;
    }
}