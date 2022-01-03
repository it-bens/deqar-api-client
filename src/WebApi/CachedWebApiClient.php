<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\WebApi;

use ITB\DeqarApiClient\WebApi\Model\DetailedInstitution;
use ITB\DeqarApiClient\WebApi\Model\SimpleAgency;
use ITB\DeqarApiClient\WebApi\Model\SimpleCountry;
use ITB\DeqarApiClient\WebApi\Model\SimpleInstitution;
use ITB\DeqarApiClient\WebApi\Model\SimpleReport;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class CachedWebApiClient implements WebApiClientInterface
{
    private const EXPIRATION_TIME = 86400;

    private const CACHE_KEY_ACTIVITIES = 'deqar.web_api.activities';
    private const CACHE_KEY_ACTIVITY_TEMPLATE = 'deqar.web_api.activities.%s.%s';

    private const CACHE_KEY_AGENCIES = 'deqar.web_api.agencies';
    private const CACHE_KEY_AGENCY_TEMPLATE = 'deqar.web_api.agencies.%s.%s';

    private const CACHE_KEY_COUNTRIES = 'deqar.web_api.countries';
    private const CACHE_KEY_COUNTRY_TEMPLATE = 'deqar.web_api.countries.%s.%s';

    private const CACHE_KEY_INSTITUTIONS = 'deqar.web_api.institutions';
    private const CACHE_KEY_INSTITUTION_TEMPLATE = 'deqar.web_api.institutions.%s.%s';

    private const CACHE_KEY_INSTITUTION_DETAILED_BY_ID = 'deqar.web_api.institutions_detailed.id';

    private const CACHE_KEY_REPORTS = 'deqar.web_api.reports';
    private const CACHE_KEY_REPORT_TEMPLATE = 'deqar.web_api.reports.%s.%s';

    private SluggerInterface $slugger;

    public function __construct(private WebApiClientInterface $webApiClient, private CacheInterface $cache)
    {
        $this->slugger = new AsciiSlugger();
    }

    public static function create(WebApiClientInterface $webApiClient): self
    {
        return new self($webApiClient, new FilesystemAdapter());
    }

    /**
     * @param SimpleAgency\Activity[] $activities
     * @throws InvalidArgumentException
     */
    public function cacheActivities(array $activities): void
    {
        foreach ($activities as $activity) {
            $this->cacheActivity($activity, 'id', (string)$activity->id);
        }
    }

    /**
     * @param SimpleCountry[] $countries
     * @throws InvalidArgumentException
     */
    public function cacheCountries(array $countries): void
    {
        foreach ($countries as $country) {
            $this->cacheCountry($country, 'id', (string)$country->id);
            $this->cacheCountry($country, 'country_code', $country->countryCode);
        }
    }

    /**
     * @return SimpleAgency\Activity[]
     * @throws InvalidArgumentException
     */
    public function getActivities(): array
    {
        return $this->cache->get(self::CACHE_KEY_ACTIVITIES, function (ItemInterface $item) {
            $item->expiresAfter(self::EXPIRATION_TIME);
            $activities = $this->webApiClient->getActivities();
            $this->cacheActivities($activities);

            return $activities;
        });
    }

    /**
     * @param string $identifier
     * @return SimpleAgency\Activity|null
     * @throws InvalidArgumentException
     */
    public function getActivity(string $identifier): ?SimpleAgency\Activity
    {
        // This ensures the activity cache is filled.
        $this->getActivities();

        if (null !== $activityById = $this->cacheActivity(null, 'id', $identifier)) {
            return $activityById;
        }

        return null;
    }

    /**
     * @return SimpleAgency[]
     * @throws InvalidArgumentException
     */
    public function getAgencies(): array
    {
        return $this->cache->get(self::CACHE_KEY_AGENCIES, function (ItemInterface $item) {
            $item->expiresAfter(self::EXPIRATION_TIME);
            $agencies = $this->webApiClient->getAgencies();
            $this->cacheAgencies($agencies);

            return $agencies;
        });
    }

    /**
     * @param string $identifier
     * @return SimpleAgency|null
     * @throws InvalidArgumentException
     */
    public function getAgencySimple(string $identifier): ?SimpleAgency
    {
        // This ensures the agency cache is filled.
        $this->getAgencies();

        if (null !== $agencyById = $this->cacheAgency(null, 'id', $identifier)) {
            return $agencyById;
        }

        if (null !== $agencyByDeqarId = $this->cacheAgency(null, 'deqar_id', $identifier)) {
            return $agencyByDeqarId;
        }

        if (null !== $agencyByNamePrimary = $this->cacheAgency(null, 'name_primary', $identifier)) {
            return $agencyByNamePrimary;
        }

        return null;
    }

    /**
     * @return SimpleCountry[]
     * @throws InvalidArgumentException
     */
    public function getCountries(): array
    {
        return $this->cache->get(self::CACHE_KEY_COUNTRIES, function (ItemInterface $item) {
            $item->expiresAfter(self::EXPIRATION_TIME);
            $countries = $this->webApiClient->getCountries();
            $this->cacheCountries($countries);

            return $countries;
        });
    }

    /**
     * @param string $id
     * @return DetailedInstitution|null
     * @throws InvalidArgumentException
     */
    public function getInstitutionDetailed(string $id): ?DetailedInstitution
    {
        return $this->cache->get(self::CACHE_KEY_INSTITUTION_DETAILED_BY_ID . $this->slugger->slug($id), function (ItemInterface $item) use ($id) {
            $item->expiresAfter(self::EXPIRATION_TIME);
            return $this->webApiClient->getInstitutionDetailed($id);
        });
    }

    /**
     * @param string $identifier
     * @return SimpleInstitution|null
     * @throws InvalidArgumentException
     */
    public function getInstitutionSimple(string $identifier): ?SimpleInstitution
    {
        // This ensures the institution cache is filled.
        $this->getInstitutions();

        if (null !== $institutionById = $this->cacheInstitution(null, 'id', $identifier)) {
            return $institutionById;
        }

        if (null !== $institutionByDeqarId = $this->cacheInstitution(null, 'deqar_id', $identifier)) {
            return $institutionByDeqarId;
        }

        if (null !== $institutionByNamePrimary = $this->cacheInstitution(null, 'name_primary', $identifier)) {
            return $institutionByNamePrimary;
        }

        return null;
    }

    /**
     * @return SimpleInstitution[]
     * @throws InvalidArgumentException
     */
    public function getInstitutions(?int $limit = null, int $offset = 0): array
    {
        $cacheKey = self::CACHE_KEY_INSTITUTIONS . '_LIMIT_' . ((null !== $limit)
                ? (string)$limit
                : 'NULL') . '_OFFSET_' . $offset;
        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($limit, $offset) {
            $item->expiresAfter(self::EXPIRATION_TIME);
            $institutions = $this->webApiClient->getInstitutions($limit, $offset);
            $this->cacheInstitutions($institutions);

            return $institutions;
        });
    }

    /**
     * @return SimpleReport[]
     * @throws InvalidArgumentException
     */
    public function getReports(?int $limit = null, int $offset = 0): array
    {
        $cacheKey = self::CACHE_KEY_REPORTS . '_LIMIT_' . ((null !== $limit)
                ? (string)$limit
                : 'NULL') . '_OFFSET_' . $offset;
        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($limit, $offset) {
            $item->expiresAfter(self::EXPIRATION_TIME);
            $remoteReports = $this->webApiClient->getReports($limit, $offset);
            $this->cacheReports($remoteReports);

            return $remoteReports;
        });
    }

    /**
     * @param SimpleAgency\Activity|null $activity
     * @param string $identifierType
     * @param string $identifier
     * @return SimpleAgency\Activity|null
     * @throws InvalidArgumentException
     */
    private function cacheActivity(?SimpleAgency\Activity $activity, string $identifierType, string $identifier): ?SimpleAgency\Activity
    {
        $cacheKey = sprintf(self::CACHE_KEY_ACTIVITY_TEMPLATE, $identifierType, $this->slugger->slug($identifier));
        return $this->cache->get($cacheKey, function (ItemInterface $cacheItem) use ($activity, $identifier) {
            $cacheItem->expiresAfter(self::EXPIRATION_TIME);
            return $activity ?? $this->webApiClient->getActivity($identifier);
        });
    }

    /**
     * @param SimpleAgency[] $agencies
     * @throws InvalidArgumentException
     */
    private function cacheAgencies(array $agencies): void
    {
        foreach ($agencies as $agency) {
            $this->cacheAgency($agency, 'id', (string)$agency->id);
            $this->cacheAgency($agency, 'deqar_id', (string)$agency->deqarId);
            $this->cacheAgency($agency, 'name_primary', $agency->namePrimary);
        }
    }

    /**
     * @param SimpleAgency|null $agency
     * @param string $identifierType
     * @param string $identifier
     * @return SimpleAgency|null
     * @throws InvalidArgumentException
     */
    private function cacheAgency(?SimpleAgency $agency, string $identifierType, string $identifier): ?SimpleAgency
    {
        $cacheKey = sprintf(self::CACHE_KEY_AGENCY_TEMPLATE, $identifierType, $this->slugger->slug($identifier));
        return $this->cache->get($cacheKey, function (ItemInterface $cacheItem) use ($agency, $identifier) {
            $cacheItem->expiresAfter(self::EXPIRATION_TIME);
            return $agency ?? $this->webApiClient->getAgencySimple($identifier);
        });
    }

    /**
     * There is currently no getCountry() method in the WebApiClientInterface.
     *
     * @param SimpleCountry $country
     * @param string $identifierType
     * @param string $identifier
     * @return SimpleCountry|null
     * @throws InvalidArgumentException
     */
    private function cacheCountry(SimpleCountry $country, string $identifierType, string $identifier): ?SimpleCountry
    {
        $cacheKey = sprintf(self::CACHE_KEY_COUNTRY_TEMPLATE, $identifierType, $this->slugger->slug($identifier));
        return $this->cache->get($cacheKey, function (ItemInterface $cacheItem) use ($country) {
            $cacheItem->expiresAfter(self::EXPIRATION_TIME);
            return $country;
        });
    }

    /**
     * @param SimpleInstitution|null $institution
     * @param string $identifierType
     * @param string $identifier
     * @return SimpleInstitution|null
     * @throws InvalidArgumentException
     */
    private function cacheInstitution(?SimpleInstitution $institution, string $identifierType, string $identifier): ?SimpleInstitution
    {
        $cacheKey = sprintf(self::CACHE_KEY_INSTITUTION_TEMPLATE, $identifierType, $this->slugger->slug($identifier));
        return $this->cache->get($cacheKey, function (ItemInterface $cacheItem) use ($institution, $identifier) {
            $cacheItem->expiresAfter(self::EXPIRATION_TIME);
            return $institution ?? $this->webApiClient->getInstitutionSimple($identifier);
        });
    }

    /**
     * @param SimpleInstitution[] $institutions
     * @throws InvalidArgumentException
     */
    private function cacheInstitutions(array $institutions): void
    {
        foreach ($institutions as $institution) {
            $this->cacheInstitution($institution, 'id', (string)$institution->id);
            $this->cacheInstitution($institution, 'deqar_id', $institution->deqarId);
            $this->cacheInstitution($institution, 'name_primary', $institution->namePrimary);
        }
    }

    /**
     * There is currently no getCountry() method in the WebApiClientInterface.
     *
     * @param SimpleReport $report
     * @param string $identifierType
     * @param string $identifier
     * @return SimpleReport|null
     * @throws InvalidArgumentException
     */
    private function cacheReport(SimpleReport $report, string $identifierType, string $identifier): ?SimpleReport
    {
        $cacheKey = sprintf(self::CACHE_KEY_REPORT_TEMPLATE, $identifierType, $this->slugger->slug($identifier));
        return $this->cache->get($cacheKey, function (ItemInterface $cacheItem) use ($report) {
            $cacheItem->expiresAfter(self::EXPIRATION_TIME);
            return $report;
        });
    }

    /**
     * @param SimpleReport[] $reports
     * @throws InvalidArgumentException
     */
    private function cacheReports(array $reports): void
    {
        foreach ($reports as $report) {
            $this->cacheReport($report, 'id', (string)$report->id);
        }
    }
}
