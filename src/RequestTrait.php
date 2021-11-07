<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient;

use ITB\DeqarApiClient\Exception\RequestFailed;
use JsonException;
use RuntimeException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

trait RequestTrait
{
    /**
     * @phpstan-ignore-next-line
     * @param HttpClientInterface $httpClient
     * @param string $uri
     * @param string $method
     * @param string $token
     * @param int $resultsPerRequest
     * @param int|null $limit
     * @param int $offset
     * @return array
     */
    public function request(
        HttpClientInterface $httpClient,
        string $uri,
        string $method,
        string $token,
        int $resultsPerRequest,
        ?int $limit = null,
        int $offset = 0
    ): array {
        $remainingResults = $limit;

        $next = true;
        $results = [];
        while ($next) {
            if (null === $remainingResults || $remainingResults > $resultsPerRequest) {
                $limit = $resultsPerRequest;
            } else {
                $limit = $remainingResults;
            }

            $query = compact('limit', 'offset');
            $response = $this->doRequest($httpClient, $uri, $method, $token, $query);
            $decodedResponse = $this->parseJsonResponse($response);

            if (!array_key_exists('results', $decodedResponse)) {
                // Some endpoints don't provide paging and deliver results directly.
                // Results are at the top level of the response.
                return $decodedResponse;
            }
            $results = array_merge($results, $decodedResponse['results']);
            $offset += $limit;

            // A missing or false 'next' key requires a break in every case.
            if (!array_key_exists('next', $decodedResponse) || false === (bool)$decodedResponse['next']) {
                $next = false;
            }

            // A break is also required if no more results are requested by limit.
            if (null !== $remainingResults) {
                $remainingResults -= count($decodedResponse['results']);
                if ($remainingResults <= 0) {
                    $next = false;
                }
            }
        }

        return $results;
    }

    /**
     * @phpstan-ignore-next-line
     * @param HttpClientInterface $httpClient
     * @param string $uri
     * @param string $method
     * @param string $token
     * @param array $query
     * @param array $json
     * @return ResponseInterface
     */
    private function doRequest(
        HttpClientInterface $httpClient,
        string $uri,
        string $method,
        string $token,
        array $query = [],
        array $json = []
    ): ResponseInterface {
        try {
            return $httpClient->request($method, $uri, ['auth_bearer' => $token, 'query' => $query, 'json' => $json]);
        } catch (TransportExceptionInterface $exception) {
            throw new RuntimeException('The request failed because of a JSON error.', previous: $exception);
        }
    }

    /**
     * @phpstan-ignore-next-line
     * @param ResponseInterface $response
     * @return array
     */
    private function parseJsonResponse(ResponseInterface $response): array
    {
        $content = $this->parseResponse($response);

        try {
            return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException('The response parsing failed because of a JSON error.', previous: $exception);
        }
    }

    private function parseResponse(ResponseInterface $response, int $expectedStatusCode = 200): string
    {
        $uri = $response->getInfo()['url'];

        try {
            $statusCode = $response->getStatusCode();
        } catch (TransportExceptionInterface $exception) {
            throw RequestFailed::withTransportException($uri, $exception);
        }

        try {
            $content = $response->getContent(true);
        } catch (ClientExceptionInterface $exception) {
            if (401 === $exception->getCode()) {
                throw RequestFailed::unauthorized($uri, $exception);
            }
            if (403 === $exception->getCode()) {
                throw RequestFailed::forbidden($uri, $exception);
            }
            if (404 === $exception->getCode()) {
                throw RequestFailed::notFound($uri, $exception);
            }

            $content = $response->getContent(false);
            throw RequestFailed::withClientException($uri, $exception, $content);
        } catch (RedirectionExceptionInterface $exception) {
            $content = $response->getContent(false);
            throw RequestFailed::withRedirectionException($uri, $exception, $content);
        } catch (ServerExceptionInterface $exception) {
            $content = $response->getContent(false);
            throw RequestFailed::withServerException($uri, $exception, $content);
        } catch (TransportExceptionInterface $exception) {
            throw RequestFailed::withTransportException($uri, $exception);
        }

        if ($expectedStatusCode !== $statusCode) {
            RequestFailed::withUnexpectedStatusCode($uri, $expectedStatusCode, $statusCode, $content);
        }

        return $content;
    }

    /**
     * @phpstan-ignore-next-line
     * @param HttpClientInterface $httpClient
     * @param string $uri
     * @param string $method
     * @param string $token
     * @param int $resultsPerRequest
     * @return array
     */
    private function requestAll(HttpClientInterface $httpClient, string $uri, string $method, string $token, int $resultsPerRequest): array
    {
        return $this->request($httpClient, $uri, $method, $token, $resultsPerRequest);
    }

    /**
     * @phpstan-ignore-next-line
     * @param HttpClientInterface $httpClient
     * @param string $uri
     * @param string $method
     * @param string $token
     * @return array|null
     */
    private function requestSingle(HttpClientInterface $httpClient, string $uri, string $method, string $token): ?array
    {
        $response = $this->doRequest($httpClient, $uri, $method, $token);
        try {
            $result = $this->parseJsonResponse($response);
        } catch (RequestFailed $exception) {
            if (404 === $exception->getStatusCode()) {
                return null;
            }

            throw $exception;
        }

        return $result;
    }
}
