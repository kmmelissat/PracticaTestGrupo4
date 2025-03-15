<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_no_autenticado_no_puede_acceder_al_endpoint_protegido()
    {
        $response = $this->getJson(route('posts.index'));
        $response->assertStatus(401);
    }
}
