<?php

namespace Tests\Feature;

use Laravel\Lumen\Testing\TestCase;

class FielXmlControllerTest extends TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../../bootstrap/app.php';
    }

    public function testInfo()
    {
        // Simula una respuesta válida del método `info`
        $response = $this->call('GET', '/info');
        $this->assertEquals(200, $response->status());
        $this->assertArrayHasKey('rfc', $response->getOriginalContent());
        $this->assertArrayHasKey('legalName', $response->getOriginalContent());
    }

    public function testAuthorization()
    {
        $response = $this->call('GET', '/authorization');
        $this->assertEquals(200, $response->status());
        // Verifica que el encabezado Content-Type sea application/xml
        $this->assertStringContainsString(
            'application/xml',
            $response->headers->get('Content-Type'),
            'Expected Content-Type to be application/xml'
        );

        // Verifica que el contenido sea un XML válido
        $content = $response->getContent();
        $this->assertTrue($this->isValidXml($content), 'Response content is not valid XML');
    }

    public function testQuery()
    {
        $data = [
            'serviceType' => 'cfdi',
            'period' => ['start' => '2024-01-01', 'end' => '2024-12-31'],
            'downloadType' => 'metadata',
            'requestType' => 'cfdi',
            'documentType' => 'any',
            'documentStatus' => 'active',
            'uuid' => null,
            'rfcOnBehalf' => null,
            'rfcMatches' => null,
        ];

        $response = $this->call('POST', '/query', $data);

        $this->assertEquals(200, $response->status());
        // Verifica que el encabezado Content-Type sea application/xml
        $this->assertStringContainsString(
            'application/xml',
            $response->headers->get('Content-Type'),
            'Expected Content-Type to be application/xml'
        );

        // Verifica que el contenido sea un XML válido
        $content = $response->getContent();
        $this->assertTrue($this->isValidXml($content), 'Response content is not valid XML');
    }

    public function testVerify()
    {
        $requestId = 'dummy-request-id';
        $response = $this->call('GET', "/verify/$requestId");
        $this->assertEquals(200, $response->status());
        // Verifica que el encabezado Content-Type sea application/xml
        $this->assertStringContainsString(
            'application/xml',
            $response->headers->get('Content-Type'),
            'Expected Content-Type to be application/xml'
        );

        // Verifica que el contenido sea un XML válido
        $content = $response->getContent();
        $this->assertTrue($this->isValidXml($content), 'Response content is not valid XML');
    }

    public function testDownload()
    {
        $packageId = 'dummy-package-id';
        $response = $this->call('GET', "/download/$packageId");

        $this->assertEquals(200, $response->status());
        // Verifica que el encabezado Content-Type sea application/xml
        $this->assertStringContainsString(
            'application/xml',
            $response->headers->get('Content-Type'),
            'Expected Content-Type to be application/xml'
        );

        // Verifica que el contenido sea un XML válido
        $content = $response->getContent();
        $this->assertTrue($this->isValidXml($content), 'Response content is not valid XML');
    }

    private function isValidXml(string $xmlContent): bool
    {
        libxml_use_internal_errors(true); // Evitar que los errores se impriman directamente
        $xml = simplexml_load_string($xmlContent);
        $errors = libxml_get_errors();
        libxml_clear_errors();

        return $xml !== false && empty($errors);
    }


}
