<?php

use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('un usuario puede crear un post con categorías', function () {
    // Crear un usuario autenticado
    $user = User::factory()->create();
    $this->actingAs($user);
    
    // Crear categorías
    $category1 = Category::factory()->create(['name' => 'noticias']);
    $category3 = Category::factory()->create(['name' => 'demo']);
    
    // Datos para el post
    $postData = [
        'title' => 'Mi nueva publicación',
        'excerpt' => 'Lorem ipsum sit amet',
        'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
        'categories' => [$category1->id, $category3->id]
    ];
    
    // Enviar la solicitud a la ruta correcta
    $response = $this->postJson('/api/posts', $postData);
    
    // Verificar la respuesta
    $response->assertStatus(201)
             ->assertJsonStructure([
                'id',
                'title',
                'slug',
                'excerpt',
                'content',
                'categories' => [
                    '*' => [
                        'id',
                        'name'
                    ]
                ],
                'user' => [
                    'id',
                    'name',
                    'email'
                ],
                'created_at',
                'updated_at'
             ]);
    
    // Verificar que el post existe en la base de datos
    $this->assertDatabaseHas('posts', [
        'title' => 'Mi nueva publicación',
        'slug' => 'mi-nueva-publicacion',
        'user_id' => $user->id
    ]);
    
    // Verificar las relaciones en la tabla pivote
    $this->assertDatabaseHas('category_post', [
        'category_id' => $category1->id,
        'post_id' => $response->json('id')
    ]);
    
    $this->assertDatabaseHas('category_post', [
        'category_id' => $category3->id,
        'post_id' => $response->json('id')
    ]);
});