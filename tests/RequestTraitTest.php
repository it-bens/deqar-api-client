<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\Tests;

use ITB\DeqarApiClient\RequestTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\String\ByteString;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class RequestTraitTest extends TestCase
{
    private RequestTraitMock $requestTrait;

    /**
     * @return MockHttpClient[][]
     */
    public function provideHttpClientWithPaging(): array
    {
        return [[new MockHttpClient($this->generateMockResponseWithPaging())]];
    }

    /**
     * @return MockHttpClient[][]
     */
    public function provideHttpClientWithoutPaging(): array
    {
        return [[new MockHttpClient($this->generateMockResponseWithoutPaging())]];
    }

    public function setUp(): void
    {
        $this->requestTrait = new RequestTraitMock();
    }

    /**
     * @dataProvider provideHttpClientWithPaging
     * @param HttpClientInterface $httpClient
     */
    public function testRequestWithPagingWithLimitLowerRppWithOffset(HttpClientInterface $httpClient): void
    {
        $results = $this->requestTrait->request($httpClient, 'http://localhost', 'GET', 'ABCD', 1000, 800, 2400);
        self::assertCount(100, $results);
    }

    /**
     * @dataProvider provideHttpClientWithPaging
     * @param HttpClientInterface $httpClient
     */
    public function testRequestWithPagingWithLimitLowerRppWithoutOffset(HttpClientInterface $httpClient): void
    {
        $results = $this->requestTrait->request($httpClient, 'http://localhost', 'GET', 'ABCD', 1000, 800);
        self::assertCount(800, $results);
    }

    /**
     * @dataProvider provideHttpClientWithPaging
     * @param HttpClientInterface $httpClient
     */
    public function testRequestWithPagingWithLimitHigherRppWithOffset(HttpClientInterface $httpClient): void
    {
        $results = $this->requestTrait->request($httpClient, 'http://localhost', 'GET', 'ABCD', 1000, 1400, 600);
        self::assertCount(1400, $results);
    }

    /**
     * @dataProvider provideHttpClientWithPaging
     * @param HttpClientInterface $httpClient
     */
    public function testRequestWithPagingWithLimitHigherRppWithoutOffset(HttpClientInterface $httpClient): void
    {
        $results = $this->requestTrait->request($httpClient, 'http://localhost', 'GET', 'ABCD', 1000, 1400);
        self::assertCount(1400, $results);
    }

    /**
     * @dataProvider provideHttpClientWithPaging
     * @param HttpClientInterface $httpClient
     */
    public function testRequestWithPagingWithoutLimitWithOffset(HttpClientInterface $httpClient): void
    {
        $results = $this->requestTrait->request($httpClient, 'http://localhost', 'GET', 'ABCD', 1000, offset: 1200);
        self::assertCount(1300, $results);
    }

    /**
     * @dataProvider provideHttpClientWithPaging
     * @param HttpClientInterface $httpClient
     */
    public function testRequestWithPagingWithoutLimitWithoutOffset(HttpClientInterface $httpClient): void
    {
        $results = $this->requestTrait->request($httpClient, 'http://localhost', 'GET', 'ABCD', 1000);
        self::assertCount(2500, $results);
    }

    /**
     * @dataProvider provideHttpClientWithoutPaging
     * @param HttpClientInterface $httpClient
     */
    public function testRequestWithoutPagingWithLimitLowerRppWithOffset(HttpClientInterface $httpClient): void
    {
        $results = $this->requestTrait->request($httpClient, 'http://localhost', 'GET', 'ABCD', 1000, 800, 2400);
        self::assertCount(100, $results);
    }

    /**
     * @dataProvider provideHttpClientWithoutPaging
     * @param HttpClientInterface $httpClient
     */
    public function testRequestWithoutPagingWithLimitLowerRppWithoutOffset(HttpClientInterface $httpClient): void
    {
        $results = $this->requestTrait->request($httpClient, 'http://localhost', 'GET', 'ABCD', 1000, 800);
        self::assertCount(800, $results);
    }

    /**
     * @dataProvider provideHttpClientWithoutPaging
     * @param HttpClientInterface $httpClient
     */
    public function testRequestWithoutPagingWithLimitHigherRppWithOffset(HttpClientInterface $httpClient): void
    {
        $results = $this->requestTrait->request($httpClient, 'http://localhost', 'GET', 'ABCD', 1000, 1400, 600);
        self::assertCount(1000, $results);
    }

    /**
     * @dataProvider provideHttpClientWithoutPaging
     * @param HttpClientInterface $httpClient
     */
    public function testRequestWithoutPagingWithLimitHigherRppWithoutOffset(HttpClientInterface $httpClient): void
    {
        $results = $this->requestTrait->request($httpClient, 'http://localhost', 'GET', 'ABCD', 1000, 1400);
        self::assertCount(1000, $results);
    }

    /**
     * @dataProvider provideHttpClientWithoutPaging
     * @param HttpClientInterface $httpClient
     */
    public function testRequestWithoutPagingWithoutLimitWithOffset(HttpClientInterface $httpClient): void
    {
        $results = $this->requestTrait->request($httpClient, 'http://localhost', 'GET', 'ABCD', 1000, offset: 1200);
        self::assertCount(1000, $results);
    }

    /**
     * @dataProvider provideHttpClientWithoutPaging
     * @param HttpClientInterface $httpClient
     */
    public function testRequestWithoutPagingWithoutLimitWithoutOffset(HttpClientInterface $httpClient): void
    {
        $results = $this->requestTrait->request($httpClient, 'http://localhost', 'GET', 'ABCD', 1000);
        self::assertCount(1000, $results);
    }

    /**
     * @return callable
     */
    private function generateMockResponseWithPaging(): callable
    {
        return function (string $method, string $url, array $options = []): MockResponse {
            $allResults = $this->generateRandomArray(2500);

            $queryString = parse_url($url, PHP_URL_QUERY) ?? '';
            $query = [];
            /** @phpstan-ignore-next-line */
            parse_str($queryString, $query);

            $limit = (int)($query['limit'] ?? null);
            $offset = (int)($query['offset'] ?? 0);

            $results = array_slice($allResults, $offset, $limit);
            $nextResults = array_slice($allResults, $offset + $limit, $limit);

            return new MockResponse(json_encode(['results' => $results, 'next' => (0 !== count($nextResults))], JSON_THROW_ON_ERROR));
        };
    }

    /**
     * @return callable
     */
    private function generateMockResponseWithoutPaging(): callable
    {
        return function (string $method, string $url, array $options = []): MockResponse {
            $allResults = $this->generateRandomArray(2500);

            $queryString = parse_url($url, PHP_URL_QUERY) ?? '';
            $query = [];
            /** @phpstan-ignore-next-line */
            parse_str($queryString, $query);

            $limit = (int)($query['limit'] ?? null);
            $offset = (int)($query['offset'] ?? 0);
            $results = array_slice($allResults, $offset, $limit);

            return new MockResponse(json_encode($results, JSON_THROW_ON_ERROR));
        };
    }

    /**
     * @param int $results
     * @return string[]
     */
    private function generateRandomArray(int $results): array
    {
        $array = [];
        for ($i = 0; $i < $results; $i++) {
            $array[] = (string)ByteString::fromRandom(10);
        }

        return $array;
    }
}
