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
    private const CACHE_KEY_ACTIVITY_BY_ID = 'deqar.web_api.activities.id.';

    private const CACHE_KEY_AGENCIES = 'deqar.web_api.agencies';
    private const CACHE_KEY_AGENCY_BY_ID = 'deqar.web_api.agencies.id.';
    private const CACHE_KEY_AGENCY_BY_DEQAR_ID = 'deqar.web_api.agencies.deqar_id.';
    private const CACHE_KEY_AGENCY_BY_NAME_PRIMARY = 'deqar.web_api.agencies.name_primary.';

    private const CACHE_KEY_COUNTRIES = 'deqar.web_api.countries';
    private const CACHE_KEY_COUNTRY_BY_ID = 'deqar.web_api.countries.id.';
    private const CACHE_KEY_COUNTRY_BY_COUNTRY_CODE = 'deqar.web_api.countries.country_code.';

    private const CACHE_KEY_INSTITUTIONS = 'deqar.web_api.institutions';
    private const CACHE_KEY_INSTITUTION_BY_ID = 'deqar.web_api.institutions.id.';
    private const CACHE_KEY_INSTITUTION_BY_DEQAR_ID = 'deqar.web_api.institutions.deqar_id.';
    private const CACHE_KEY_INSTITUTION_BY_NAME_PRIMARY = 'deqar.web_api.institutions.name_primary.';

    private const CACHE_KEY_INSTITUTION_DETAILED_BY_ID = 'deqar.web_api.institutions_detailed.id';

    private const CACHE_KEY_REPORTS = 'deqar.web_api.reports';
    private const CACHE_KEY_REPORT_BY_ID = 'deqar.web_api.reports.';

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
     * @return SimpleAgency\Activity[]
     * @throws InvalidArgumentException
     */
    public function getActivities(): array
    {
        return $this->cache->get(self::CACHE_KEY_ACTIVITIES, function (ItemInterface $item) {
            $item->expiresAfter(self::EXPIRATION_TIME);
            $remoteActivities = $this->webApiClient->getActivities();
            foreach ($remoteActivities as $remoteActivity) {
                $this->cache->get(
                    self::CACHE_KEY_ACTIVITY_BY_ID . $this->slugger->slug((string)$remoteActivity->id),
                    function (ItemInterface $activityItem) use ($remoteActivity) {
                        $activityItem->expiresAfter(self::EXPIRATION_TIME);
                        return $remoteActivity;
                    }
                );
            }

            return $remoteActivities;
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

        return $this->cache->get(
            self::CACHE_KEY_ACTIVITY_BY_ID . $this->slugger->slug($identifier),
            function (ItemInterface $item) use ($identifier) {
                $item->expiresAfter(self::EXPIRATION_TIME);
                return $this->webApiClient->getActivity($identifier);
            }
        );
    }

    /**
     * @return SimpleAgency[]
     * @throws InvalidArgumentException
     */
    public function getAgencies(): array
    {
        return $this->cache->get(self::CACHE_KEY_AGENCIES, function (ItemInterface $item) {
            $item->expiresAfter(self::EXPIRATION_TIME);
            $remoteAgencies = $this->webApiClient->getAgencies();
            foreach ($remoteAgencies as $remoteAgency) {
                $this->cache->get(
                    self::CACHE_KEY_AGENCY_BY_ID . $this->slugger->slug((string)$remoteAgency->id),
                    function (ItemInterface $activityItem) use ($remoteAgency) {
                        $activityItem->expiresAfter(self::EXPIRATION_TIME);
                        return $remoteAgency;
                    }
                );
                $this->cache->get(
                    self::CACHE_KEY_AGENCY_BY_DEQAR_ID . $this->slugger->slug($remoteAgency->deqarId),
                    function (ItemInterface $activityItem) use ($remoteAgency) {
                        $activityItem->expiresAfter(self::EXPIRATION_TIME);
                        return $remoteAgency;
                    }
                );
                $this->cache->get(
                    self::CACHE_KEY_AGENCY_BY_NAME_PRIMARY . $this->slugger->slug($remoteAgency->namePrimary),
                    function (ItemInterface $activityItem) use ($remoteAgency) {
                        $activityItem->expiresAfter(self::EXPIRATION_TIME);
                        return $remoteAgency;
                    }
                );
            }

            return $remoteAgencies;
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

        $agencyById = $this->cache->get(
            self::CACHE_KEY_AGENCY_BY_ID . $this->slugger->slug($identifier),
            function (ItemInterface $item) use ($identifier) {
                $item->expiresAfter(self::EXPIRATION_TIME);
                return $this->webApiClient->getActivity($identifier);
            }
        );
        if (null !== $agencyById) {
            return $agencyById;
        }

        $agencyById = $this->cache->get(
            self::CACHE_KEY_AGENCY_BY_DEQAR_ID . $this->slugger->slug($identifier),
            function (ItemInterface $item) use ($identifier) {
                $item->expiresAfter(self::EXPIRATION_TIME);
                return $this->webApiClient->getActivity($identifier);
            }
        );
        if (null !== $agencyById) {
            return $agencyById;
        }

        $agencyById = $this->cache->get(
            self::CACHE_KEY_AGENCY_BY_NAME_PRIMARY . $this->slugger->slug($identifier),
            function (ItemInterface $item) use ($identifier) {
                $item->expiresAfter(self::EXPIRATION_TIME);
                return $this->webApiClient->getActivity($identifier);
            }
        );
        if (null !== $agencyById) {
            return $agencyById;
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
            $remoteCountries = $this->webApiClient->getCountries();
            foreach ($remoteCountries as $remoteCountry) {
                $this->cache->get(
                    self::CACHE_KEY_COUNTRY_BY_ID . $this->slugger->slug((string)$remoteCountry->id),
                    function (ItemInterface $activityItem) use ($remoteCountry) {
                        $activityItem->expiresAfter(self::EXPIRATION_TIME);
                        return $remoteCountry;
                    }
                );
                $this->cache->get(
                    self::CACHE_KEY_COUNTRY_BY_COUNTRY_CODE . $this->slugger->slug($remoteCountry->countryCode),
                    function (ItemInterface $activityItem) use ($remoteCountry) {
                        $activityItem->expiresAfter(self::EXPIRATION_TIME);
                        return $remoteCountry;
                    }
                );
            }

            return $remoteCountries;
        });
    }

    /**
     * @param string $id
     * @return DetailedInstitution|null
     * @throws InvalidArgumentException
     */
    public function getInstitutionDetailed(string $id): ?DetailedInstitution
    {
        $institutionById = $this->cache->get(
            self::CACHE_KEY_INSTITUTION_DETAILED_BY_ID . $this->slugger->slug($id),
            function (ItemInterface $item) use ($id) {
                $item->expiresAfter(self::EXPIRATION_TIME);
                return $this->webApiClient->getInstitutionDetailed($id);
            }
        );

        return $institutionById;
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

        $institutionById = $this->cache->get(
            self::CACHE_KEY_INSTITUTION_BY_ID . $this->slugger->slug($identifier),
            function (ItemInterface $item) use ($identifier) {
                $item->expiresAfter(self::EXPIRATION_TIME);
                return $this->webApiClient->getActivity($identifier);
            }
        );
        if (null !== $institutionById) {
            return $institutionById;
        }

        $institutionById = $this->cache->get(
            self::CACHE_KEY_INSTITUTION_BY_DEQAR_ID . $this->slugger->slug($identifier),
            function (ItemInterface $item) use ($identifier) {
                $item->expiresAfter(self::EXPIRATION_TIME);
                return $this->webApiClient->getActivity($identifier);
            }
        );
        if (null !== $institutionById) {
            return $institutionById;
        }

        $institutionById = $this->cache->get(
            self::CACHE_KEY_INSTITUTION_BY_NAME_PRIMARY . $this->slugger->slug($identifier),
            function (ItemInterface $item) use ($identifier) {
                $item->expiresAfter(self::EXPIRATION_TIME);
                return $this->webApiClient->getActivity($identifier);
            }
        );
        if (null !== $institutionById) {
            return $institutionById;
        }

        return null;
    }

    /**
     * @return SimpleInstitution[]
     * @throws InvalidArgumentException
     */
    public function getInstitutions(): array
    {
        return $this->cache->get(self::CACHE_KEY_INSTITUTIONS, function (ItemInterface $item) {
            $item->expiresAfter(self::EXPIRATION_TIME);
            $remoteInstitutions = $this->webApiClient->getInstitutions();
            foreach ($remoteInstitutions as $remoteInstitution) {
                $this->cache->get(
                    self::CACHE_KEY_INSTITUTION_BY_ID . $this->slugger->slug((string)$remoteInstitution->id),
                    function (ItemInterface $activityItem) use ($remoteInstitution) {
                        $activityItem->expiresAfter(self::EXPIRATION_TIME);
                        return $remoteInstitution;
                    }
                );
                $this->cache->get(
                    self::CACHE_KEY_INSTITUTION_BY_DEQAR_ID . $this->slugger->slug($remoteInstitution->deqarId),
                    function (ItemInterface $activityItem) use ($remoteInstitution) {
                        $activityItem->expiresAfter(self::EXPIRATION_TIME);
                        return $remoteInstitution;
                    }
                );
                $this->cache->get(
                    self::CACHE_KEY_INSTITUTION_BY_NAME_PRIMARY . $this->slugger->slug($remoteInstitution->namePrimary),
                    function (ItemInterface $activityItem) use ($remoteInstitution) {
                        $activityItem->expiresAfter(self::EXPIRATION_TIME);
                        return $remoteInstitution;
                    }
                );
            }

            return $remoteInstitutions;
        });
    }

    /**
     * @return SimpleReport[]
     * @throws InvalidArgumentException
     */
    public function getReports(): array
    {
        return $this->cache->get(self::CACHE_KEY_REPORTS, function (ItemInterface $item) {
            $item->expiresAfter(self::EXPIRATION_TIME);
            $remoteReports = $this->webApiClient->getReports();
            foreach ($remoteReports as $remoteReport) {
                $this->cache->get(
                    self::CACHE_KEY_REPORT_BY_ID . $this->slugger->slug((string)$remoteReport->id),
                    function (ItemInterface $activityItem) use ($remoteReport) {
                        $activityItem->expiresAfter(self::EXPIRATION_TIME);
                        return $remoteReport;
                    }
                );
            }

            return $remoteReports;
        });
    }
}
