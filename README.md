# Fiel Http Php

**Fiel Http Php** es una implementación de los servicios necesarios para realizar la descarga masiva de CFDI y Retenciones del SAT, utilizando una FIEL para autenticación y firma digital.

---
## Requisitos

- **PHP 8.0 o superior**
- **Composer**
- Certificados **.cer**, **.key**, y contraseña de la FIEL.
---

## Servicios necesarios para descarga masiva 
* Autenticación: Esto se hace con tu FIEL y la librería oculta la lógica de obtener y usar el Token.
* Solicitud: Presentar una solicitud incluyendo la fecha de inicio, fecha de fin, tipo de solicitud emitidas/recibidas y tipo de información solicitada (cfdi o metadata).
* Verificación: pregunta al SAT si ya tiene disponible la solicitud.
* Descargar los paquetes emitidos por la solicitud.

---

## Instalación

1. Clona este repositorio:
    ```bash
    git clone https://github.com/MiguelAngelMP10/fielHttpPhp.git
    cd fielHttpPhp
    ```

2. Instala las dependencias usando Composer:
    ```bash
    composer install
    ```

3. Crea un archivo `.env` basado en `.env.example` y ajusta las variables de entorno:
    ```env
    CER_PATH=/ruta/al/certificado.cer
    KEY_PATH=/ruta/al/certificado.key
    PASSWORD_FIEL=contraseña
    API_TOKEN=algun-token-que-se-usuara-para-autenticarce-a-los-servicios
    ```

4. Levanta el servidor de desarrollo:
    ```bash
    php -S localhost:8000 -t public
    ```
---  

## Endpoints Disponibles

### 1. **Info**
Obtiene información de la FIEL.

- **Método**: `GET`
- **URL**: `/info`
- **Respuesta**:
    ```json
    {
        "rfc": "XAXX010101000",
        "legalName": "EMPRESA DEMO SA DE CV"
    }
    ```

### 2. **Authorization**
Genera un XML firmado con la FIEL.

- **Método**: `GET`
- **URL**: `/authorization`
- **Respuesta**:
    ```xml
    <Autenticacion>...</Autenticacion>
    ```

### 3. **Query**
Presenta una solicitud al SAT.

- **Método**: `POST`
- **URL**: `/query`
- **Body** (JSON):
    ```json
    {
        "serviceType": "cfdi",
        "period": {
            "start": "2023-01-01T00:00:00",
            "end": "2023-01-31T23:59:59"
        },
        "downloadType": "received",
        "requestType": "metadata"
    }
    ```
- **Respuesta**:
    ```xml
    <QueryResult>
        <RequestID>123456789</RequestID>
    </QueryResult>
    ```

### 4. **Verify**
Verifica el estado de una solicitud.

- **Método**: `GET`
- **URL**: `/verify/{requestId}`

### 5. **Download**
Descarga un paquete aprobado.

- **Método**: `GET`
- **URL**: `/download/{packageId}`

---

## Pruebas

Para ejecutar las pruebas, ejecuta el siguiente comando:

```bash
  vendor/bin/phpunit --testdox
```

## Ejemplo de usar los servios del api fiel

```php
<?php

namespace App\phpcfdi\SatWsDescargaMasiva\RequestBuilder\FielHttpRequestBuilder;

use Illuminate\Support\Facades\Http;
use PhpCfdi\SatWsDescargaMasiva\RequestBuilder\RequestBuilderInterface;
use PhpCfdi\SatWsDescargaMasiva\Services\Query\QueryParameters;
use PhpCfdi\SatWsDescargaMasiva\Shared\DateTime;

final class FielHttpRequestBuilder implements RequestBuilderInterface
{
    private string $baseUrl;

    private string $token;

    public function __construct(string $baseUrl, string $token)
    {
        $this->baseUrl = $baseUrl;
        $this->token = $token;
    }

    public function authorization(DateTime $created, DateTime $expires, string $securityTokenId = ''): string
    {
        return Http::withToken($this->token)->get($this->baseUrl.'authorization')->body();
    }

    public function query(QueryParameters $queryParameters): string
    {
        return Http::withToken($this->token)->post($this->baseUrl.'query', $queryParameters->jsonSerialize())->body();
    }

    public function verify(string $requestId): string
    {
        return Http::withToken($this->token)->get($this->baseUrl.'verify/'.$requestId)->body();
    }

    public function download(string $packageId): string
    {
        return Http::withToken($this->token)->get($this->baseUrl.'download/'.$packageId)->body();
    }
}

```
* Forma de uso en conjuto de la libreria de **phpcfdi/sat-ws-descarga-masiva** se crea el **$requestBuilder** usando **FielHttpRequestBuilder**


```php

$requestBuilder = new \App\phpcfdi\SatWsDescargaMasiva\RequestBuilder\FielHttpRequestBuilder\FielHttpRequestBuilder($baseUrl, $authorization);

$service = new \PhpCfdi\SatWsDescargaMasiva\Service($requestBuilder, $webClient);

```
https://github.com/phpcfdi/sat-ws-descarga-masiva?tab=readme-ov-file#creaci%C3%B3n-el-servicio