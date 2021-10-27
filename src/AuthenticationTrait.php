<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient;

use ITB\DeqarApiClient\Exception\AuthenticationFailed;
use ITB\DeqarApiClient\Exception\RequestFailed;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

trait AuthenticationTrait
{
    use RequestTrait;

    private function getAuthToken(string $username, string $password): string
    {
        $httpClient = HttpClient::create(
            ['headers' => ['Accept' => 'application/json'], 'verify_host' => false, 'verify_peer' => false]
        );
        $uri = 'https://backend.deqar.eu/accounts/get_token/';

        try {
            $response = $httpClient->request('POST', $uri, ['json' => compact('username', 'password')]);
        } catch (TransportExceptionInterface $exception) {
            throw AuthenticationFailed::withRequestFailed(RequestFailed::withTransportException($uri, $exception));
        }

        $decodedPayload = $this->parseJsonResponse($response);

        if (!array_key_exists('state', $decodedPayload) || 'success' !== $decodedPayload['state']) {
            throw AuthenticationFailed::invalidReponse('state');
        }
        if (!array_key_exists('token', $decodedPayload) || !is_string($decodedPayload['token'])) {
            throw AuthenticationFailed::invalidReponse('token');
        }

        return $decodedPayload['token'];
    }
}
