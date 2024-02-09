<?php

namespace App\Http\Controllers;

use PhpCfdi\SatWsDescargaMasiva\Services\Query\QueryParameters;
use PhpCfdi\SatWsDescargaMasiva\Shared\ComplementoCfdi;
use PhpCfdi\SatWsDescargaMasiva\Shared\DateTimePeriod;
use PhpCfdi\SatWsDescargaMasiva\Shared\DocumentStatus;
use PhpCfdi\SatWsDescargaMasiva\Shared\DocumentType;
use PhpCfdi\SatWsDescargaMasiva\Shared\DownloadType;
use PhpCfdi\SatWsDescargaMasiva\Shared\RequestType;
use PhpCfdi\SatWsDescargaMasiva\Shared\RfcMatch;
use PhpCfdi\SatWsDescargaMasiva\Shared\RfcMatches;
use PhpCfdi\SatWsDescargaMasiva\Shared\RfcOnBehalf;
use PhpCfdi\SatWsDescargaMasiva\Shared\ServiceType;
use PhpCfdi\SatWsDescargaMasiva\Shared\Uuid;

class QueryParametersFactory
{
    private QueryParameters $queryParameters;

    public function __construct()
    {
        $this->queryParameters = QueryParameters::create();
    }

    public static function create(): QueryParametersFactory
    {
        return new self();
    }

    public function withServiceType(string|null $serviceType): QueryParametersFactory
    {
        $this->queryParameters = $this->queryParameters->withServiceType(
            match ($serviceType) {
                'cfdi' => ServiceType::cfdi(),
                'retenciones' => ServiceType::retenciones(),
                default => ServiceType::cfdi(),
            }
        );

        return $this;

    }

    public function withPeriod($start, $end): QueryParametersFactory
    {
        $this->queryParameters = $this->queryParameters->withPeriod(
            DateTimePeriod::createFromValues($start, $end)
        );
        return $this;
    }

    public function withDownloadType(string|null $downloadType): QueryParametersFactory
    {
        $this->queryParameters = $this->queryParameters->withDownloadType(
            match ($downloadType) {
                'RfcEmisor' => DownloadType::issued(),
                'RfcReceptor' => DownloadType::received(),
                default => DownloadType::issued(),
            }
        );

        return $this;
    }

    public function withRequestType(string|null $requestType): QueryParametersFactory
    {
        $this->queryParameters = $this->queryParameters->withRequestType(
            match ($requestType) {
                'metadata' => RequestType::metadata(),
                'xml' => RequestType::xml(),
                default => RequestType::metadata(),

            }
        );

        return $this;
    }

    public function withDocumentType(string|null $documentType): QueryParametersFactory
    {
        $this->queryParameters = $this->queryParameters->withDocumentType(
            match ($documentType) {
                'I' => DocumentType::ingreso(),
                'E' => DocumentType::egreso(),
                'T' => DocumentType::traslado(),
                'N' => DocumentType::nomina(),
                'P' => DocumentType::pago(),
                default => DocumentType::undefined()
            }
        );
        return $this;
    }

    public function withComplement(string|null $complement = ''): QueryParametersFactory
    {
        if ($complement != null) {
            $this->queryParameters = $this->queryParameters->withComplement(
                ComplementoCfdi::create($complement)
            );
        }

        return $this;
    }

    public function withDocumentStatus(string|null $documentStatus = ''): QueryParametersFactory
    {
        $this->queryParameters = $this->queryParameters->withDocumentStatus(
            match ($documentStatus) {
                default => DocumentStatus::undefined(),
                '1' => DocumentStatus::active(),
                '0' => DocumentStatus::cancelled()
            }
        );
        return $this;
    }

    public function withUuid(string|null $uuid): QueryParametersFactory
    {
        if ($uuid != null) {
            $this->queryParameters = $this->queryParameters->withUuid(Uuid::create($uuid));
        }
        return $this;
    }

    public function withRfcOnBehalf(string|null $rfcOnBehalf): QueryParametersFactory
    {
        if ($rfcOnBehalf != null) {
            $this->queryParameters = $this->queryParameters->withRfcOnBehalf(RfcOnBehalf::create($rfcOnBehalf));
        }
        return $this;
    }

    public function withRfcMatches(array|null $rfcMatches): QueryParametersFactory
    {
        if ($rfcMatches != null) {
            $rfcMatches = RfcMatches::createFromValues(...$rfcMatches);

            $this->queryParameters = $this->queryParameters->withRfcMatches($rfcMatches);
        }


        return $this;
    }

    public function getQueryParameters(): QueryParameters
    {
        return $this->queryParameters;
    }

}