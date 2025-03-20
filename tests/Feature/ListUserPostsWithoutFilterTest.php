<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class ListUserPostsWithoutFilterTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $otherUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear un usuario para las pruebas
        $this->user = User::factory()->create();
        
        // Crear otro usuario para verificar que no se muestren sus posts
        $this->otherUser = User::factory()->create();

        // Crear posts para el usuario principal
        Post::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'title' => 'Post normal',
            'content' => 'Contenido normal'
        ]);

        // Crear posts para el otro usuario (no deberían aparecer en los resultados)
        Post::factory()->count(2)->create([
            'user_id' => $this->otherUser->id
        ]);
    }

    /** @test */
    public function user_can_list_all_their_posts_without_filter()
    {
        // Autenticar al usuario
        Sanctum::actingAs($this->user);

        // Hacer la petición al endpoint
        $response = $this->getJson('/api/v1/posts');

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

        // Verificar que se devuelven exactamente 3 posts (los del usuario autenticado)
        $response->assertJsonCount(3, 'data');

        // Verificar que todos los posts pertenecen al usuario autenticado
        $responseData = $response->json('data');
        foreach ($responseData as $post) {
            $this->assertEquals($this->user->id, $post['user_id']);
        }
    }
}