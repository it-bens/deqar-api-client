<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\Tests\WebApi;

use ITB\DeqarApiClient\WebApi\CachedWebApiClient;
use ITB\DeqarApiClient\WebApi\Model\DetailedInstitution;
use ITB\DeqarApiClient\WebApi\Model\SimpleAgency;
use ITB\DeqarApiClient\WebApi\Model\SimpleCountry;
use ITB\DeqarApiClient\WebApi\Model\SimpleInstitution;
use ITB\DeqarApiClient\WebApi\Model\SimpleReport;
use ITB\DeqarApiClient\WebApi\WebApiClient;
use ITB\DeqarApiClient\WebApi\WebApiClientInterface;
use PHPUnit\Framework\TestCase;

final class CachedWebApiClientTest extends TestCase
{
    private WebApiClientInterface $cachedWebApiClient;

    public function setUp(): void
    {
        $webApiClient = WebApiClient::create($_ENV['DEQAR_API_USERNAME'], $_ENV['DEQAR_API_PASSWORD']);
        $this->cachedWebApiClient = CachedWebApiClient::create($webApiClient);
    }

    public function testGetActivities(): void
    {
        $activites = $this->cachedWebApiClient->getActivities();
        $this->assertContainsOnlyInstancesOf(SimpleAgency\Activity::class, $activites);
    }

    public function testGetActivity(): void
    {
        $activity = $this->cachedWebApiClient->getActivity('154');
        self::assertInstanceOf(SimpleAgency\Activity::class, $activity);
    }

    public function testGetActivityInvalid(): void
    {
        $activity = $this->cachedWebApiClient->getActivity('0');
        self::assertNull($activity);
    }

    public function testGetAgencies(): void
    {
        $agencies = $this->cachedWebApiClient->getAgencies();
        $this->assertContainsOnlyInstancesOf(SimpleAgency::class, $agencies);
    }

    public function testGetAgencySimple(): void
    {
        $agency = $this->cachedWebApiClient->getAgencySimple('5');
        self::assertInstanceOf(SimpleAgency::class, $agency);
    }

    public function testGetAgencySimpleInvalid(): void
    {
        $agency = $this->cachedWebApiClient->getAgencySimple('0');
        self::assertNull($agency);
    }

    public function testGetCountries(): void
    {
        $countries = $this->cachedWebApiClient->getCountries();
        $this->assertContainsOnlyInstancesOf(SimpleCountry::class, $countries);
    }

    public function testGetInstitutionDetailed(): void
    {
        $institution = $this->cachedWebApiClient->getInstitutionDetailed('2191');
        $this->assertInstanceOf(DetailedInstitution::class, $institution);
    }

    public function testGetInstitutionDetailedInvalid(): void
    {
        $institution = $this->cachedWebApiClient->getInstitutionDetailed('0');
        $this->assertNull($institution);
    }

    public function testGetInstitutionSimple(): void
    {
        $institution = $this->cachedWebApiClient->getInstitutionSimple('2191');
        $this->assertInstanceOf(SimpleInstitution::class, $institution);
    }

    public function testGetInstitutionSimpleInvalid(): void
    {
        $institution = $this->cachedWebApiClient->getInstitutionSimple('0');
        $this->assertNull($institution);
    }

    public function testGetInstitutions(): void
    {
        $institutions = $this->cachedWebApiClient->getInstitutions();
        $this->assertContainsOnlyInstancesOf(SimpleInstitution::class, $institutions);
    }

    public function testGetReports(): void
    {
        $reports = $this->cachedWebApiClient->getReports();
        $this->assertContainsOnlyInstancesOf(SimpleReport::class, $reports);
    }
}
