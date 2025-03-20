<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class ListUserPostsWithFilterTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear un usuario para las pruebas
        $this->user = User::factory()->create();

        // Crear posts normales para el usuario
        Post::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'title' => 'Post normal',
            'content' => 'Contenido normal'
        ]);

        // Crear posts con contenido específico para pruebas de búsqueda
        Post::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Post especial',
            'content' => 'Contenido normal'
        ]);

        Post::factory()->create([
            'user_id' => $this->user->id,
            'title' => 'Post normal',
            'content' => 'Contenido con palabra clave'
        ]);
    }

    /** @test */
    public function user_can_list_their_posts_with_search_filter()
    {
        // Autenticar al usuario
        Sanctum::actingAs($this->user);

        // Test de búsqueda por título
        $response = $this->getJson('/api/v1/posts?search=especial');

        // Verificar que la respuesta sea exitosa
        $response->assertStatus(200);
        
        // Verificar que la estructura de la respuesta es correcta
        $response->assertJsonStructure([
            'status',
            'data' => [
                '*' => [
                    'id', 'title', 'slug', 'excerpt', 'content', 'user_id', 'created_at', 'updated_at'
                ]
            ]
        ]);

        // Verificar que se devuelve exactamente 1 post (el que contiene "especial" en el título)
        $response->assertJsonCount(1, 'data');
        $responseData = $response->json('data');
        $this->assertEquals('Post especial', $responseData[0]['title']);

        // Test de búsqueda por contenido
        $response = $this->getJson('/api/v1/posts?search=palabra clave');
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $responseData = $response->json('data');
        $this->assertEquals('Contenido con palabra clave', $responseData[0]['content']);
    }
}