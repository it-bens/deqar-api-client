<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\Tests\SubmissionApi;

use ITB\DeqarApiClient\ValidatorTrait;
use ITB\DeqarApiClient\WebApi\WebApiClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractSubmissionApiTest extends TestCase
{
    use ValidatorTrait;

    protected ValidatorInterface $validator;

    public function setUp(): void
    {
        $httpClient = HttpClient::create(['headers' => ['Accept' => 'application/json'], 'verify_host' => false, 'verify_peer' => false]);

        $webApiClient = WebApiClient::create($_ENV['DEQAR_API_USERNAME'], $_ENV['DEQAR_API_PASSWORD']);
        $this->validator = self::buildValidator($httpClient, $webApiClient);
    }
}
