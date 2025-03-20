<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnauthenticatedPostAccessTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function unauthenticated_user_cannot_access_posts()
    {
        // Intentar acceder sin autenticación
        $response = $this->getJson('/api/v1/posts');
        
        // Verificar que se deniega el acceso (401 Unauthorized)
        $response->assertStatus(401);
    }
}