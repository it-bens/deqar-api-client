<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\WebApi;

use ITB\DeqarApiClient\WebApi\Model\DetailedInstitution;
use ITB\DeqarApiClient\WebApi\Model\SimpleAgency;
use ITB\DeqarApiClient\WebApi\Model\SimpleCountry;
use ITB\DeqarApiClient\WebApi\Model\SimpleInstitution;
use ITB\DeqarApiClient\WebApi\Model\SimpleReport;

interface WebApiClientInterface
{
    /**
     * @return SimpleAgency\Activity[]
     */
    public function getActivities(): array;

    /**
     * @param string $identifier
     * @return SimpleAgency\Activity|null
     */
    public function getActivity(string $identifier): ?SimpleAgency\Activity;

    /**
     * @return SimpleAgency[]
     */
    public function getAgencies(): array;

    /**
     * @param string $identifier
     * @return SimpleAgency|null
     */
    public function getAgencySimple(string $identifier): ?SimpleAgency;

    // public function getAgency(string $id);

    /**
     * @return SimpleCountry[]
     */
    public function getCountries(): array;

    // public function getCountry(string $id);

    /**
     * @param string $id
     * @return DetailedInstitution|null
     */
    public function getInstitutionDetailed(string $id): ?DetailedInstitution;

    /**
     * @param string $identifier
     * @return SimpleInstitution|null
     */
    public function getInstitutionSimple(string $identifier): ?SimpleInstitution;

    /**
     * @return SimpleInstitution[]
     */
    public function getInstitutions(): array;

    // public function getReport(string $id);

    /**
     * @return SimpleReport[]
     */
    public function getReports(): array;
}
