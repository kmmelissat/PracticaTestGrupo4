<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ErrorAutenticacionEP extends TestCase
{
    use RefreshDatabase;

    /**
     * Prueba que verifica que un usuario no autenticado 
     * recibe un error 401 al intentar acceder a endpoints protegidos.
     *
     * @return void
     */
    public function test_unauthenticated_user_cannot_access_protected_endpoints()
    {
        // Intentar acceder al endpoint de listado de posts (suponiendo que está protegido)
        $response = $this->getJson('/api/v1/posts');
        
        // Verificar que recibe un código de estado 401 (Unauthorized)
        $this->assertEquals(401, $response->status());
        
        // O alternativamente puedes usar la aserción de Laravel
        // $response->assertUnauthorized();
    }

    /**
     * Prueba que verifica que un usuario autenticado puede acceder
     * a un endpoint protegido.
     *
     * @return void
     */
    public function test_authenticated_user_can_access_protected_endpoints()
    {
        // Crear un usuario para la prueba
        $user = User::factory()->create();
        
        // Autenticar al usuario (método para API)
        $this->actingAs($user, 'sanctum');
        
        // Hacer solicitud a un endpoint protegido
        $response = $this->getJson('/api/v1/posts');
        
        // Verificar que la solicitud es exitosa
        $response->assertSuccessful();
    }
}