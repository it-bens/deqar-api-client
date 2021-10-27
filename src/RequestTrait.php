<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient;

use ITB\DeqarApiClient\Exception\RequestFailed;
use JsonException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

trait RequestTrait
{
    private function doRequest(HttpClientInterface $httpClient, string $uri, string $method, string $token, array $query = [], array $json = []): ResponseInterface
    {
        try {
            return $httpClient->request($method, $uri, ['auth_bearer' => $token, 'query' => $query, 'json' => $json]);
        } catch (TransportExceptionInterface $exception) {
            // TODO: throw exception
        }
    }

    private function parseJsonResponse(ResponseInterface $response): array
    {
        $content = $this->parseResponse($response);

        try {
            return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            // TODO: throw exception
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

    private function requestAll(HttpClientInterface $httpClient, string $uri, string $method, string $token, int $resultsPerRequest): array
    {
        $limit = $resultsPerRequest;
        $offset = 0;

        $next = true;
        $results = [];
        while ($next) {
            $query = compact('limit', 'offset');
            $response = $this->doRequest($httpClient, $uri, $method, $token, $query);
            $decodedResponse = $this->parseJsonResponse($response);

            if (!array_key_exists('results', $decodedResponse)) {
                // Some endpoints don't provide paging and deliver results directly.
                // Results are at the top level of the response.
                return $decodedResponse;
            }
            $results = array_merge($results, $decodedResponse['results']);

            if (!array_key_exists('next', $decodedResponse) || false === (bool)$decodedResponse['next']) {
                $next = false;
            }

            $offset += $resultsPerRequest;
        }

        return $results;
    }

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
