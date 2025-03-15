<?php

use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('se pueden crear posts con títulos duplicados generando slugs únicos', function () {
    // Crear un usuario autenticado
    $user = User::factory()->create();
    $this->actingAs($user);
    
    // Crear una categoría
    $category = Category::factory()->create(['name' => 'general']);
    
    // Datos para el primer post
    $firstPostData = [
        'title' => 'Hola',
        'excerpt' => 'Primer post de prueba',
        'content' => 'Este es el contenido del primer post de prueba',
        'categories' => [$category->id]
    ];
    
    // Crear el primer post
    $firstResponse = $this->postJson('/api/v1/posts', $firstPostData);
    
    // Verificar que el primer post se creó correctamente
    $firstResponse->assertStatus(201);
    $firstResponse->assertJson([
        'title' => 'Hola',
        'slug' => 'hola'
    ]);
    
    // Datos para el segundo post con el mismo título
    $secondPostData = [
        'title' => 'Hola',
        'excerpt' => 'Segundo post de prueba',
        'content' => 'Este es el contenido del segundo post de prueba',
        'categories' => [$category->id]
    ];
    
    // Crear el segundo post con el mismo título
    $secondResponse = $this->postJson('/api/v1/posts', $secondPostData);
    
    // Verificar que el segundo post se creó correctamente
    $secondResponse->assertStatus(201);
    
    // Verificar que el slug del segundo post es diferente al primero
    $this->assertNotEquals(
        $firstResponse->json('slug'),
        $secondResponse->json('slug')
    );
    
    // Verificar que el segundo post tiene un slug incremental
    $this->assertStringStartsWith('hola-', $secondResponse->json('slug'));
    
    // Datos para el tercer post con el mismo título
    $thirdPostData = [
        'title' => 'Hola',
        'excerpt' => 'Tercer post de prueba',
        'content' => 'Este es el contenido del tercer post de prueba',
        'categories' => [$category->id]
    ];
    
    // Crear el tercer post con el mismo título
    $thirdResponse = $this->postJson('/api/v1/posts', $thirdPostData);
    
    // Verificar que el tercer post se creó correctamente
    $thirdResponse->assertStatus(201);
    
    // Verificar que el slug del tercer post es diferente a los anteriores
    $this->assertNotEquals(
        $firstResponse->json('slug'),
        $thirdResponse->json('slug')
    );
    $this->assertNotEquals(
        $secondResponse->json('slug'),
        $thirdResponse->json('slug')
    );
    
    // Verificar que el tercer post tiene un slug incremental
    $this->assertStringStartsWith('hola-', $thirdResponse->json('slug'));
    
    // Verificar que los tres posts existen en la base de datos
    $this->assertDatabaseCount('posts', 3);
    $this->assertDatabaseHas('posts', [
        'title' => 'Hola',
        'slug' => 'hola'
    ]);
    $this->assertDatabaseHas('posts', [
        'title' => 'Hola',
        'slug' => 'hola-1'
    ]);
    $this->assertDatabaseHas('posts', [
        'title' => 'Hola',
        'slug' => 'hola-2'
    ]);
});