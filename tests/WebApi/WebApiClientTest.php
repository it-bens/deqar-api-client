<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\Tests\WebApi;

use ITB\DeqarApiClient\WebApi\Model\DetailedInstitution;
use ITB\DeqarApiClient\WebApi\Model\SimpleAgency;
use ITB\DeqarApiClient\WebApi\Model\SimpleCountry;
use ITB\DeqarApiClient\WebApi\Model\SimpleInstitution;
use ITB\DeqarApiClient\WebApi\Model\SimpleReport;
use ITB\DeqarApiClient\WebApi\WebApiClient;
use ITB\DeqarApiClient\WebApi\WebApiClientInterface;
use PHPUnit\Framework\TestCase;

final class WebApiClientTest extends TestCase
{
    private WebApiClientInterface $webApiClient;

    public function setUp(): void
    {
        $this->webApiClient = WebApiClient::create($_ENV['DEQAR_API_USERNAME'], $_ENV['DEQAR_API_PASSWORD']);
    }

    public function testGetActivities(): void
    {
        $activities = $this->webApiClient->getActivities();
        self::assertContainsOnlyInstancesOf(SimpleAgency\Activity::class, $activities);
    }

    public function testGetActivity(): void
    {
        $activity = $this->webApiClient->getActivity('154');
        self::assertInstanceOf(SimpleAgency\Activity::class, $activity);
    }

    public function testGetActivityInvalid(): void
    {
        $activity = $this->webApiClient->getActivity('0');
        self::assertNull($activity);
    }

    public function testGetAgencies(): void
    {
        $agencies = $this->webApiClient->getAgencies();
        self::assertContainsOnlyInstancesOf(SimpleAgency::class, $agencies);
    }

    public function testGetAgencySimple(): void
    {
        $agency = $this->webApiClient->getAgencySimple('5');
        self::assertInstanceOf(SimpleAgency::class, $agency);
    }

    public function testGetAgencySimpleInvalid(): void
    {
        $agency = $this->webApiClient->getAgencySimple('0');
        self::assertNull($agency);
    }

    public function testGetCountries(): void
    {
        $countries = $this->webApiClient->getCountries();
        self::assertContainsOnlyInstancesOf(SimpleCountry::class, $countries);
    }

    public function testGetInstitutionDetailed(): void
    {
        $institution = $this->webApiClient->getInstitutionDetailed('2191');
        self::assertInstanceOf(DetailedInstitution::class, $institution);
    }

    public function testGetInstitutionDetailedInvalid(): void
    {
        $institution = $this->webApiClient->getInstitutionDetailed('0');
        self::assertNull($institution);
    }

    public function testGetInstitutionSimple(): void
    {
        $institution = $this->webApiClient->getInstitutionSimple('2191');
        self::assertInstanceOf(SimpleInstitution::class, $institution);
    }

    public function testGetInstitutionSimpleInvalid(): void
    {
        $institution = $this->webApiClient->getInstitutionSimple('0');
        self::assertNull($institution);
    }

    /**
     * This test will be disabled for CI pipelines for performance reasons.
     * @group local-only
     */
    public function testGetInstitutions(): void
    {
        $institutions = $this->webApiClient->getInstitutions();
        self::assertContainsOnlyInstancesOf(SimpleInstitution::class, $institutions);
    }

    public function testGetInstitutionsWithLimit(): void
    {
        $institutions = $this->webApiClient->getInstitutions(1500);
        self::assertCount(1500, $institutions);
        self::assertContainsOnlyInstancesOf(SimpleInstitution::class, $institutions);
    }

    /**
     * This test will be disabled for CI pipelines for performance reasons.
     * @group local-only
     */
    public function testGetReports(): void
    {
        $reports = $this->webApiClient->getReports();
        self::assertContainsOnlyInstancesOf(SimpleReport::class, $reports);
    }

    public function testGetReportsWithLimit(): void
    {
        $reports = $this->webApiClient->getReports(3000);
        self::assertCount(3000, $reports);
        self::assertContainsOnlyInstancesOf(SimpleReport::class, $reports);
    }
}
