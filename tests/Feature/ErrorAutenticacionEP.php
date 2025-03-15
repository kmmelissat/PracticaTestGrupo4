<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ErrorAutenticacionCrearPostTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Configuración inicial para las pruebas
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear algunas categorías para usar en las pruebas
        Category::factory()->create(['name' => 'noticias']);
        Category::factory()->create(['name' => 'tecnología']);
    }

    /**
     * Prueba que verifica que un usuario no autenticado 
     * recibe un error 401 al intentar crear un post.
     *
     * @return void
     */
    public function test_unauthenticated_user_cannot_create_post()
    {
        // Preparar datos para la creación del post
        $postData = [
            'title' => 'Mi nueva publicación',
            'excerpt' => 'Lorem ipsum sit amet',
            'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'categories' => [1, 2]
        ];

        // Intentar crear un post sin autenticación
        $response = $this->postJson('/api/v1/posts', $postData);

        // Verificar que recibe un código de estado 401 (Unauthorized)
        $this->assertEquals(401, $response->status());
        // O alternativamente usar la aserción de Laravel
        $response->assertUnauthorized();
    }

    /**
     * Prueba que verifica que un usuario autenticado puede
     * crear un post exitosamente.
     *
     * @return void
     */
    public function test_authenticated_user_can_create_post()
    {
        // Crear un usuario para la prueba
        $user = User::factory()->create();

        // Autenticar al usuario (método para API)
        $this->actingAs($user, 'sanctum');

        // Preparar datos para la creación del post
        $postData = [
            'title' => 'Mi nueva publicación',
            'excerpt' => 'Lorem ipsum sit amet',
            'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'categories' => [1, 2]
        ];

        // Intentar crear un post con autenticación
        $response = $this->postJson('/api/v1/posts', $postData);

        // Verificar que la solicitud es exitosa (código 201 Created)
        $response->assertStatus(201);

        // Verificar que el post se creó correctamente
        $response->assertJsonStructure([
            'id',
            'title',
            'slug',
            'excerpt',
            'content',
            'categories',
            'user',
            'created_at',
            'updated_at'
        ]);
    }
}