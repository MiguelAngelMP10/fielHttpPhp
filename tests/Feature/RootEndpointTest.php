<?php

namespace Tests\Feature;

use Laravel\Lumen\Testing\TestCase;

class RootEndpointTest extends TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../bootstrap/app.php';
    }

    public function testRootEndpointReturnsApiFielMessage()
    {
        // Llama al endpoint raíz
        $response = $this->call('GET', '/');

        // Verifica que el código de respuesta HTTP sea 200
        $this->assertEquals(200, $response->status(), 'Expected status 200');

        // Verifica que el contenido sea JSON
        $this->assertStringContainsString(
            'application/json',
            $response->headers->get('Content-Type'),
            'Expected Content-Type to be application/json'
        );

        // Verifica que el contenido tenga el mensaje esperado
        $expected = ['message' => 'Api Fiel'];
        $this->assertEquals($expected, $response->getOriginalContent(), 'Response does not match expected content');
    }
}
