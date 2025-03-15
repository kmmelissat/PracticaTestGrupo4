<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('un usuario no autenticado no puede acceder a un endpoint protegido', function () {
    // Simular la solicitud GET al endpoint protegido
    $response = $this->getJson('/api/v1/protegido');

    // Verificar que la respuesta sea 401 (No autenticado)
    $response->assertStatus(401)
             ->assertJson([
                 'message' => 'Unauthenticated.'
             ]);
});
