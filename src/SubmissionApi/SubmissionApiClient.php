<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\SubmissionApi;

use ITB\DeqarApiClient\AuthenticationTrait;
use ITB\DeqarApiClient\Exception\RequestFailed;
use ITB\DeqarApiClient\RequestTrait;
use ITB\DeqarApiClient\SerializerTrait;
use ITB\DeqarApiClient\SubmissionApi\Model\DeleteReportResponseError;
use ITB\DeqarApiClient\SubmissionApi\Model\DeleteReportResponseInterface;
use ITB\DeqarApiClient\SubmissionApi\Model\DeleteReportResponseSuccess;
use ITB\DeqarApiClient\SubmissionApi\Model\SubmitReportRequest;
use ITB\DeqarApiClient\SubmissionApi\Model\SubmitReportResponseError;
use ITB\DeqarApiClient\SubmissionApi\Model\SubmitReportResponseInterface;
use ITB\DeqarApiClient\SubmissionApi\Model\SubmitReportResponseSuccess;
use ITB\DeqarApiClient\ValidatorTrait;
use ITB\DeqarApiClient\WebApi\WebApiClient;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class SubmissionApiClient implements SubmissionApiClientInterface
{
    use AuthenticationTrait;
    use RequestTrait;
    use SerializerTrait;
    use ValidatorTrait;

    private const BASE_URI = 'https://backend.deqar.eu/submissionapi/v1';
    private const BASE_TEST_URI = 'https://backend.sandbox.deqar.eu/submissionapi/v1';
    private const REPORT_SUBMIT_ENDPOINT = '/submit/report';
    private const REPORT_DELETE_ENDPOINT = '/delete/report';

    private string $reportSubmitEndpoint;
    private string $reportDeleteEndpoint;
    private ?string $authToken = null;

    public function __construct(
        private string $username,
        private string $password,
        private HttpClientInterface $httpClient,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        bool $test = false
    ) {
        $baseUri = $test ? self::BASE_TEST_URI : self::BASE_URI;
        $this->reportSubmitEndpoint = $baseUri . self::REPORT_SUBMIT_ENDPOINT;
        $this->reportDeleteEndpoint = $baseUri . self::REPORT_DELETE_ENDPOINT;
    }

    public static function create(string $username, string $password, bool $test = false): self
    {
        $httpClient = HttpClient::create(['headers' => ['Accept' => 'application/json'], 'verify_host' => false, 'verify_peer' => false]);
        $serializer = self::buildSerializer();

        $webApiClient = WebApiClient::create($username, $password);
        $validator = self::buildValidator($httpClient, $webApiClient);

        return new self($username, $password, $httpClient, $serializer, $validator, $test);
    }

    public function deleteReport(string $id): DeleteReportResponseInterface
    {
        $this->authenticate();

        $uri = $this->reportDeleteEndpoint . '/' . $id;
        $response = $this->doRequest($this->httpClient, $uri, 'DELETE', $this->authToken);
        try {
            $this->parseJsonResponse($response);
        } catch (RequestFailed $exception) {
            return new DeleteReportResponseError($id);
        }

        return new DeleteReportResponseSuccess($id);
    }

    public function submitReport(SubmitReportRequest $request): SubmitReportResponseInterface
    {
        $this->authenticate();

        $violations = $this->validator->validate($request);
        if (0 !== count($violations)) {
            throw new ValidationFailedException($request, $violations);
        }

        $json = $this->serializeToArray($request, $this->serializer, [AbstractObjectNormalizer::SKIP_NULL_VALUES => true]);
        $response = $this->doRequest($this->httpClient, $this->reportSubmitEndpoint, 'POST', $this->authToken, json: $json);
        try {
            $responseData = $this->parseJsonResponse($response);
        } catch (RequestFailed $exception) {
            if (null !== $exception->getResponseContent()) {
                return new SubmitReportResponseError($exception->getResponseContent());
            }

            throw $exception;
        }

        return new SubmitReportResponseSuccess($responseData);
    }

    private function authenticate(): void
    {
        if (null === $this->authToken) {
            $this->authToken = $this->getAuthToken($this->username, $this->password);
        }
    }
}
