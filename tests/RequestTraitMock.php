<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\Tests;

use ITB\DeqarApiClient\RequestTrait;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class RequestTraitMock
{
    use RequestTrait;

    /** @phpstan-ignore-next-line */
    public function publicRequest(
        HttpClientInterface $httpClient,
        string $uri,
        string $method,
        string $token,
        int $resultsPerRequest,
        ?int $limit = null,
        int $offset = 0
    ): array {
        return $this->request($httpClient, $uri, $method, $token, $resultsPerRequest, $limit, $offset);
    }

    /** @phpstan-ignore-next-line */
    public function publicRequestSingle(HttpClientInterface $httpClient, string $uri, string $method, string $token): ?array
    {
        return $this->requestSingle($httpClient, $uri, $method, $token);
    }

    /** @phpstan-ignore-next-line */
    private function publicDoRequest(
        HttpClientInterface $httpClient,
        string $uri,
        string $method,
        string $token,
        array $query = [],
        array $json = []
    ): ResponseInterface {
        return $this->doRequest($httpClient, $uri, $method, $token, $query, $json);
    }

    /** @phpstan-ignore-next-line */
    private function publicParseJsonResponse(ResponseInterface $response): array
    {
        return $this->parseJsonResponse($response);
    }

    /** @phpstan-ignore-next-line */
    private function publicParseResponse(ResponseInterface $response, int $expectedStatusCode = 200): string
    {
        return $this->parseResponse($response, $expectedStatusCode);
    }

    /** @phpstan-ignore-next-line */
    private function publicRequestAll(HttpClientInterface $httpClient, string $uri, string $method, string $token, int $resultsPerRequest): array
    {
        return $this->requestAll($httpClient, $uri, $method, $token, $resultsPerRequest);
    }
}
