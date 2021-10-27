<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\Tests;

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

    public function testGetAgencies(): void
    {
        $agencies = $this->webApiClient->getAgencies();
        $this->assertContainsOnlyInstancesOf(SimpleAgency::class, $agencies);
    }

    public function testGetCountries(): void
    {
        $countries = $this->webApiClient->getCountries();
        $this->assertContainsOnlyInstancesOf(SimpleCountry::class, $countries);
    }

    public function testGetInstitution(): void
    {
        $institution = $this->webApiClient->getInstitution('2191');
        $this->assertInstanceOf(DetailedInstitution::class, $institution);
    }

    public function testGetInstitutionInvalid(): void
    {
        $institution = $this->webApiClient->getInstitution('0');
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