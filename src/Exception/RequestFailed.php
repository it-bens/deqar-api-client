<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\Exception;

use RuntimeException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

final class RequestFailed extends RuntimeException
{
    private ?array $responseContent;

    private function __construct(string $message, private ?int $statusCode = null, ?ExceptionInterface $previousException = null, string $responseContent = null)
    {
        $this->responseContent = null !== $responseContent
            ? json_decode($responseContent, true, 512, JSON_THROW_ON_ERROR)
            : null;

        parent::__construct($message, 0, $previousException);
    }

    public static function forbidden(string $uri, ?HttpExceptionInterface $previousException = null): self
    {
        return new self(sprintf('The request to the URI "%s" returned "Forbidden (403)" as a response.', $uri), 403, $previousException);
    }

    public static function notFound(string $uri, ?HttpExceptionInterface $previousException = null): self
    {
        return new self(sprintf('The request to the URI "%s" returned "Not Found (404)" as a response.', $uri), 404, $previousException);
    }

    public static function unauthorized(string $uri, ?HttpExceptionInterface $previousException = null): self
    {
        return new self(sprintf('The request to the URI "%s" returned "Unauthorized (401)" as a response.', $uri), 401, $previousException);
    }

    public static function withClientException(string $uri, ?ClientExceptionInterface $previousException = null, ?string $responseContent = null): self
    {
        return new self(sprintf('The request to the URI "%s" returned a client error (%d) as a response.', $uri, $previousException->getCode()), $previousException->getCode(), $previousException, $responseContent);
    }

    public static function withRedirectionException(string $uri, ?RedirectionExceptionInterface $previousException = null, ?string $responseContent = null): self
    {
        return new self(sprintf('The request to the URI "%s" returned a redirection error (%d) as a response.', $uri, $previousException->getCode()), $previousException->getCode(), $previousException, $responseContent);
    }

    public static function withServerException(string $uri, ServerExceptionInterface $previousException, ?string $responseContent = null): self
    {
        return new self(sprintf('The request to the URI "%s" returned a server error (%d) as a response.', $uri, $previousException->getCode()), $previousException->getCode(), $previousException, $responseContent);
    }

    public static function withTransportException(string $uri, TransportExceptionInterface $previousException): self
    {
        return new self(sprintf('The request to the URI "%s" failed at transport level.', $uri), 0, $previousException);
    }

    public static function withUnexpectedStatusCode(string $uri, int $expectedStatusCode, int $statusCode, ?string $responseContent = null): self
    {
        return new self(sprintf('The request to the URI "%s" returned with status code %s, but the expected status code was %s.', $uri, $statusCode, $expectedStatusCode), $statusCode, responseContent: $responseContent);
    }

    public function getResponseContent(): ?array
    {
        return $this->responseContent;
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }
}
