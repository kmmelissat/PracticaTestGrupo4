<?php

use App\Models\User;
use App\Models\Category;

beforeEach(function () {
    // Crear un usuario para las pruebas
    $this->user = User::factory()->create();
    
    // Autenticar al usuario
    $this->actingAs($this->user);
    
    // Crear algunas categorías para las pruebas
    Category::factory()->create(['name' => 'noticias']);
    Category::factory()->create(['name' => 'tecnología']);
    Category::factory()->create(['name' => 'demo']);
});

test('error de validación al crear post por falta de datos requeridos', function () {
    // Datos incompletos (falta el título)
    $postData = [
        'excerpt' => 'Lorem ipsum sit amet',
        'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
        'categories' => [1,3]
    ];

    // Realizar la petición POST sin el título
    $response = $this->postJson('/api/v1/posts', $postData);

    // Verificar que la respuesta sea un error de validación (422)
    $response->assertStatus(422);
    
    // Verificar que el error incluya el campo title
    $response->assertJsonValidationErrors(['title']);
    
    // Datos incompletos (falta el contenido)
    $postData = [
        'title' => 'Mi nueva publicación',
        'excerpt' => 'Lorem ipsum sit amet',
        'categories' => [1,3]
    ];

    // Realizar la petición POST sin el contenido
    $response = $this->postJson('/api/v1/posts', $postData);
    
    // Verificar que la respuesta sea un error de validación (422)
    $response->assertStatus(422);
    
    // Verificar que el error incluya el campo content
    $response->assertJsonValidationErrors(['content']);
    
    // Datos incompletos (faltan las categorías)
    $postData = [
        'title' => 'Mi nueva publicación',
        'excerpt' => 'Lorem ipsum sit amet',
        'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'
    ];

    // Realizar la petición POST sin categorías
    $response = $this->postJson('/api/v1/posts', $postData);
    
    // Verificar que la respuesta sea un error de validación (422)
    $response->assertStatus(422);
    
    // Verificar que el error incluya el campo categories
    $response->assertJsonValidationErrors(['categories']);
});