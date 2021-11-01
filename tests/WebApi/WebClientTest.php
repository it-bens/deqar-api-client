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

final class WebClientTest extends TestCase
{
    private WebApiClientInterface $webApiClient;

    public function setUp(): void
    {
        $this->webApiClient = WebApiClient::create($_ENV['DEQAR_API_USERNAME'], $_ENV['DEQAR_API_PASSWORD']);
    }

    public function testGetActivities(): void
    {
        $activites = $this->webApiClient->getActivities();
        $this->assertContainsOnlyInstancesOf(SimpleAgency\Activity::class, $activites);
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
        $this->assertContainsOnlyInstancesOf(SimpleAgency::class, $agencies);
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
        $this->assertContainsOnlyInstancesOf(SimpleCountry::class, $countries);
    }

    public function testGetInstitutionDetailed(): void
    {
        $institution = $this->webApiClient->getInstitutionDetailed('2191');
        $this->assertInstanceOf(DetailedInstitution::class, $institution);
    }

    public function testGetInstitutionDetailedInvalid(): void
    {
        $institution = $this->webApiClient->getInstitutionDetailed('0');
        $this->assertNull($institution);
    }

    public function testGetInstitutionSimple(): void
    {
        $institution = $this->webApiClient->getInstitutionSimple('2191');
        $this->assertInstanceOf(SimpleInstitution::class, $institution);
    }

    public function testGetInstitutionSimpleInvalid(): void
    {
        $institution = $this->webApiClient->getInstitutionSimple('0');
        $this->assertNull($institution);
    }

    public function testGetInstitutions(): void
    {
        $institutions = $this->webApiClient->getInstitutions();
        $this->assertContainsOnlyInstancesOf(SimpleInstitution::class, $institutions);
    }

    public function testGetReports(): void
    {
        $reports = $this->webApiClient->getReports();
        $this->assertContainsOnlyInstancesOf(SimpleReport::class, $reports);
    }
}
