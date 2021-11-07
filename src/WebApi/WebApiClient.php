<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\WebApi;

use ITB\DeqarApiClient\AuthenticationTrait;
use ITB\DeqarApiClient\Exception\AuthenticationFailed;
use ITB\DeqarApiClient\RequestTrait;
use ITB\DeqarApiClient\SerializerTrait;
use ITB\DeqarApiClient\WebApi\Model\DetailedInstitution;
use ITB\DeqarApiClient\WebApi\Model\SimpleAgency;
use ITB\DeqarApiClient\WebApi\Model\SimpleCountry;
use ITB\DeqarApiClient\WebApi\Model\SimpleInstitution;
use ITB\DeqarApiClient\WebApi\Model\SimpleReport;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class WebApiClient implements WebApiClientInterface
{
    use AuthenticationTrait;
    use RequestTrait;
    use SerializerTrait;

    private const BASE_URI = 'https://backend.deqar.eu/webapi/v2/browse';

    private const AGENCIES_ENDPOINT = self::BASE_URI . '/agencies/';
    private const COUNTRIES_ENDPOINT = self::BASE_URI . '/countries/';
    private const INSTITUTIONS_ENDPOINT = self::BASE_URI . '/institutions/';
    private const REPORTS_ENDPOINT = self::BASE_URI . '/reports/';

    private const MAX_RESULTS_PER_REQUEST = 1000;

    private string $authToken;

    public function __construct(
        private string $username,
        private string $password,
        private HttpClientInterface $httpClient,
        private SerializerInterface $serializer
    ) {
    }

    public static function create(string $username, string $password): self
    {
        $httpClient = HttpClient::create(['headers' => ['Accept' => 'application/json'], 'verify_host' => false, 'verify_peer' => false]);
        $serializer = self::buildSerializer();

        return new self($username, $password, $httpClient, $serializer);
    }

    /**
     * @return SimpleAgency\Activity[]
     */
    public function getActivities(): array
    {
        $this->authenticate();

        $agencies = $this->getAgencies();
        $activities = [];
        foreach ($agencies as $agency) {
            $activities = array_merge($activities, $agency->activities);
        }

        return $activities;
    }

    /**
     * @param string $identifier
     * @return SimpleAgency\Activity|null
     */
    public function getActivity(string $identifier): ?SimpleAgency\Activity
    {
        $this->authenticate();

        $activities = $this->getActivities();
        foreach ($activities as $activity) {
            if ($identifier === (string)$activity->id) {
                return $activity;
            }
        }

        return null;
    }

    /**
     * @return SimpleAgency[]
     */
    public function getAgencies(): array
    {
        $this->authenticate();

        $results = $this->requestAll(
            $this->httpClient,
            self::AGENCIES_ENDPOINT,
            'GET',
            $this->authToken,
            self::MAX_RESULTS_PER_REQUEST
        );

        return $this->deserializeFromArray($results, SimpleAgency::class . '[]', $this->serializer);
    }

    /**
     * @param string $identifier
     * @return SimpleAgency|null
     */
    public function getAgencySimple(string $identifier): ?SimpleAgency
    {
        $this->authenticate();

        $agencies = $this->getAgencies();
        foreach ($agencies as $agency) {
            if ($identifier === (string)$agency->id) {
                return $agency;
            }
            if ($identifier === $agency->deqarId) {
                return $agency;
            }
            if ($identifier === $agency->namePrimary) {
                return $agency;
            }
        }

        return null;
    }

    /**
     * @return SimpleCountry[]
     */
    public function getCountries(): array
    {
        $this->authenticate();

        $results = $this->requestAll(
            $this->httpClient,
            self::COUNTRIES_ENDPOINT,
            'GET',
            $this->authToken,
            self::MAX_RESULTS_PER_REQUEST
        );

        return $this->deserializeFromArray($results, SimpleCountry::class . '[]', $this->serializer);
    }

    /**
     * @param string $id
     * @return DetailedInstitution|null
     */
    public function getInstitutionDetailed(string $id): ?DetailedInstitution
    {
        $this->authenticate();

        $uri = self::INSTITUTIONS_ENDPOINT . $id . '/';
        $result = $this->requestSingle($this->httpClient, $uri, 'GET', $this->authToken);
        if (null === $result) {
            return null;
        }

        return $this->deserializeFromArray($result, DetailedInstitution::class, $this->serializer);
    }

    /**
     * @param string $identifier
     * @return SimpleInstitution|null
     */
    public function getInstitutionSimple(string $identifier): ?SimpleInstitution
    {
        $this->authenticate();

        $institutions = $this->getInstitutions();
        foreach ($institutions as $institution) {
            if ($identifier === (string)$institution->id) {
                return $institution;
            }
            if ($identifier === $institution->deqarId) {
                return $institution;
            }
            if ($identifier === $institution->eterId) {
                return $institution;
            }
        }

        return null;
    }

    /**
     * @return SimpleInstitution[]
     */
    public function getInstitutions(?int $limit = null, int $offset = 0): array
    {
        $this->authenticate();

        $results = $this->request(
            $this->httpClient,
            self::INSTITUTIONS_ENDPOINT,
            'GET',
            $this->authToken,
            self::MAX_RESULTS_PER_REQUEST,
            $limit,
            $offset
        );

        return $this->deserializeFromArray($results, SimpleInstitution::class . '[]', $this->serializer);
    }

    /**
     * @return SimpleReport[]
     */
    public function getReports(?int $limit = null, int $offset = 0): array
    {
        $this->authenticate();

        $results = $this->request(
            $this->httpClient,
            self::REPORTS_ENDPOINT,
            'GET',
            $this->authToken,
            self::MAX_RESULTS_PER_REQUEST,
            $limit,
            $offset
        );

        return $this->deserializeFromArray($results, SimpleReport::class . '[]', $this->serializer);
    }

    private function authenticate(): void
    {
        if (!isset($this->authToken)) {
            $this->authToken = $this->getAuthToken($this->username, $this->password);
        }
    }
}
