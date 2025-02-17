<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Laravel\Lumen\Http\ResponseFactory;
use PhpCfdi\Credentials\Credential;
use PhpCfdi\SatWsDescargaMasiva\RequestBuilder\FielRequestBuilder\Fiel;
use PhpCfdi\SatWsDescargaMasiva\RequestBuilder\FielRequestBuilder\FielRequestBuilder;
use PhpCfdi\SatWsDescargaMasiva\RequestBuilder\RequestBuilderException;
use PhpCfdi\SatWsDescargaMasiva\Shared\DateTime;
use Illuminate\Http\Request;

class FielXmlController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    private Fiel $fiel;

    private FielRequestBuilder $requestBuilder;

    public function __construct()
    {
        $this->fiel = Fiel::create(
            file_get_contents(env('CER_PATH')),
            file_get_contents(env('KEY_PATH')),
            env('PASSWORD_FIEL')
        );

        if (!$this->fiel->isValid()) {
            return;
        }

        $this->requestBuilder = new FielRequestBuilder($this->fiel);
    }

    public function info(): Response|ResponseFactory
    {
        $fiel = Credential::openFiles(
            env('CER_PATH'),
            env('KEY_PATH'),
            env('PASSWORD_FIEL')
        );
        $certificado = $fiel->certificate();


        return response([
            'rfc' => $fiel->rfc(),
            'legalName' => $certificado->legalName()
        ]);
    }

    /**
     * @throws RequestBuilderException
     */
    public function authorization(): Response|ResponseFactory
    {
        return response($this->requestBuilder->authorization(DateTime::now(), DateTime::now()->modify('+5 minutes')), 200, [
            'Content-Type' => 'application/xml'
        ]);
    }

    /**
     * @throws RequestBuilderException
     */
    public function query(Request $request): Response|ResponseFactory
    {
        $factory = QueryParametersFactory::create()
            ->withServiceType($request->input('serviceType'))
            ->withPeriod($request->input('period.start'), $request->input('period.end'))
            ->withDownloadType($request->input('downloadType'))
            ->withRequestType($request->input('requestType'))
            ->withDocumentType($request->input('documentType'))
            ->withComplement($request->input('complement'))
            ->withDocumentStatus($request->input('documentStatus'))
            ->withUuid($request->input('uuid'))
            ->withRfcOnBehalf($request->input('rfcOnBehalf'))
            ->withRfcMatches($request->input('rfcMatches'));

        return response($this->requestBuilder->query($factory->getQueryParameters()), 200, [
            'Content-Type' => 'application/xml'
        ]);

    }

    /**
     * @throws RequestBuilderException
     */
    public function verify(string $requestId): Response|ResponseFactory
    {
        return response($this->requestBuilder->verify($requestId), 200, [
            'Content-Type' => 'application/xml'
        ]);
    }

    /**
     * @throws RequestBuilderException
     */
    public function download(string $packageId): Response|ResponseFactory
    {
        return response($this->requestBuilder->download($packageId), 200, [
            'Content-Type' => 'application/xml'
        ]);
    }
}
