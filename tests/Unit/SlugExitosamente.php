<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SlugExitosamente extends TestCase
{
    use RefreshDatabase;

    /**
     * Verificar que un slug se crea correctamente a partir del título del post.
     *
     * @return void
     */
    public function test_slug_is_created_successfully()
    {
        // Crear un usuario para asociarlo al post
        $user = User::factory()->create();

        // Crear un post con un título específico
        $post = Post::create([
            'title' => 'Este es un Título de Ejemplo',
            'excerpt' => 'Este es un extracto de ejemplo',
            'content' => 'Este es el contenido completo del post de ejemplo',
            'user_id' => $user->id,
        ]);

        // Verificar que el slug se creó correctamente
        $this->assertEquals('este-es-un-titulo-de-ejemplo', $post->slug);
    }

    /**
     * Verificar que los slugs son únicos cuando se crean posts con títulos idénticos.
     *
     * @return void
     */
    public function test_slugs_are_unique()
    {
        // Crear un usuario para asociarlo al post
        $user = User::factory()->create();

        // Crear el primer post
        $firstPost = Post::create([
            'title' => 'Título Duplicado',
            'excerpt' => 'Extracto del primer post',
            'content' => 'Contenido del primer post',
            'user_id' => $user->id,
        ]);

        // Crear un segundo post con el mismo título
        $secondPost = Post::create([
            'title' => 'Título Duplicado',
            'excerpt' => 'Extracto del segundo post',
            'content' => 'Contenido del segundo post',
            'user_id' => $user->id,
        ]);

        // Verificar que los slugs son diferentes
        $this->assertNotEquals($firstPost->slug, $secondPost->slug);
        
        // Verificar que el primer slug es el esperado
        $this->assertEquals('titulo-duplicado', $firstPost->slug);
        
        // Verificar que el segundo slug tiene algún tipo de sufijo (puede variar según tu implementación)
        $this->assertStringStartsWith('titulo-duplicado-', $secondPost->slug);
    }
}