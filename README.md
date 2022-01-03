# DEQAR API Client(s)

![Maintenance Status](https://img.shields.io/badge/Maintained%3F-yes-green.svg)
![CI Status](https://github.com/it-bens/deqar-api-client/actions/workflows/ci.yaml/badge.svg?branch=master)
[![codecov](https://codecov.io/gh/it-bens/deqar-api-client/branch/master/graph/badge.svg?token=B39XLZT3DL)](https://codecov.io/gh/it-bens/deqar-api-client)

## What is DEQAR?
DEQAR is the database of EQAR, the European Quality Assurance Register for Higher Education.
It provides information about european institutions for higher education, the quality assurance agencies,
their reports and their work.

The information can be retrieved from the DEQAR REST APIs:
* https://backend.deqar.eu/connectapi/v1/swagger/ (partially public)
* https://backend.deqar.eu/webapi/v2/swagger/ (authentication required)

There is also an API, which can be used to submit quality assurance reports:
* https://backend.deqar.eu/submissionapi/v1/swagger/ (authentication required)

## How to install this package?
The package can be installed via Composer:
```bash
composer require it-bens/deqar-api-client
```
It requires at least PHP 8. It was tests against PHP 8.1.

## Which API Client provides this package?
This package contains clients for the WebAPI and the SubmissionAPI.

The WebApi provides endpoints for agencies, countries, institutions and reports. 
There is always an endpoint for retrieving a collection of resources 
and one or more for retrieving more detailed resources. 
Currently, this client uses mostly the collection endpoints and provides methods for filtering them 
to retrieve a single less-detailed resource by unique identifiers.

There is also a cached implementation of the WebApi client. It decorates the ordinary WebApi client.

The submission API implements only two endpoints: submit/update a report and delete a report.
The submission process involves several server-sided checks of the provided data. 
Some checks are performed immediately, which results in an error response if the data is invalid.
Other checks are done asynchronously, which means that reports can be marked as invalid after a successful submission.

## How to use the Client?
### The Web API Client
The `WebApiClientInterface` implementations provide a static `create` method, that takes the minimal amount of required data 
and creates components like HTTP client and serializer by themselves.

```php
use ITB\DeqarApiClient\WebApi\WebApiClient;

$webApiClient = WebApiClient::create($_ENV['DEQAR_API_USERNAME'], $_ENV['DEQAR_API_PASSWORD']);
```

The `CachedWebApiClient` decorates a `WebApiClientInterface` implementation.
```php
use ITB\DeqarApiClient\WebApi\CachedWebApiClient;

$cachedWebApiClient = CachedWebApiClient::create($webApiClient);
```

The following methods are provided by the `WebApiClientInterface`:
```php
use ITB\DeqarApiClient\WebApi\WebApiClient;

$webApiClient = new WebApiClient($username, $password, $httpClient, $serializer);

// returns an activity array (extracted from the agencies)
$activities = $webApiClient->getActivities();

// returns a single activity or null (identified by 'id' or 'activity' property)
$activity = $webApiClient->getActivity($identifier);

// returns an agency array
$agencies = $webApiClient->getAgencies();

// returns a single agency or null (identified by 'id', 'deqar_id' or 'name_primary' property)
$agency = $webApiClient->getAgencySimple($identifier);

// returns a country array
$countries = $webApiClient->getCountries();

// returns an institution array ('limit' and 'offset' can reduce the results)
$institutions = $webApiClient->getInstitutions(limit: 500, offset: 200);

// returns a single institution or null (identified by 'deqar_id' or 'eter_id' property)
$institution = $webApiClient->getInstitutionSimple();

// returns a report array ('limit' and 'offset' can reduce the results)
$reports = $webApiClient->getReports(limit: 500, offset: 200);
```

The responses are mapped to DTOs via Symfony Serializer. The methods for retrieving a single less-detailed version of a resource,
rely on filtering the collection responses. To prevent unnecessary DEQAR API hits, please use the `CachedWebApiClient`.
The client caches its results for 1 day (86400 seconds).

Internally, the Symfony `HttpClientInterface` is used for requests. With the static `create` method of the `WebApiClient`,
an instance with the Symfony HttpClient and Symfony Serializer (with the necessary normalizers and extractors) is created.
However, the constructor is public and can be used to pass custom instances of the `HttpClientInterface` and the `SerializerInterface`.

### The Submission API Client
The `SubmissionApiClientInterface` implementations provide a static `create` method, that takes the minimal amount of required data
and creates components like HTTP client, validator and serializer by themselves.

```php
use ITB\DeqarApiClient\SubmissionApi\SubmissionApiClient;

$submissionApiClient = SubmissionApiClient::create($_ENV['DEQAR_API_USERNAME'], $_ENV['DEQAR_API_PASSWORD'], test: true);
```
> ⚠ The `test` parameter is important to use the DEQAR sandbox in a development or test environment.
> Currently, the DEQAR credentials are valid for the sandbox and the production environment. Without the `test` parameter,
> the `SubmissionApiClient` will send data to the public version of DEQAR!

To prevent sending invalid, data the inputs are validated against several constraints within this client.
The data have to be provided as a `SubmitReportRequest` object, which is validated with the Symfony Validator.

```php
use ITB\DeqarApiClient\SubmissionApi\Model\SubmitReportRequest;

$request = new SubmitReportRequest(...);
$response = $this->submissionApiClient->submitReport($request);
```

The following constraints are applied to the `SubmitReportRequest`:
* `agency`: not blank, must exist in DEQAR (checked against the WebAPI)
* `activity`: not blank, must exist in DEQAR (checked against the WebAPI)
* `status`: 'part of obligatory EQA system' or 'voluntary'
* `decision`: 'positive', 'positive with conditions or restrictions', 'negative' or 'not applicable'
* `validFrom`: valid date
* `files`: if provided, all array elements must be valid `FileRequest` objects
* `institutions`: if provided, all array elements must be valid `InstitutionRequest` objects
* `programmes`: if provided, all array elements must be valid `ProgrammeRequest` objects
* `id`: nullable, but not blank
* `contributingAgencies`: nullable, but all array elements must be valid agencies (like above)
* `localIdentifier`: nullable, but length from 1 to 255 chars
* `summary`: nullable, but not blank
* `validTo`: nullable, but a valid date
* `links`: nullable, but all array elements must be valid `LinkRequest` objects
* `comment`: nullable, but not blank
* A certain number of institutions and programmes have to be provided according to (https://docs.deqar.eu/report_data/#activity)

The sub-objects of the request are validated too:
* `FileRequest`:
  * `originalLocation`: length from 1 to 500 chars, url that points to a remote PDF file (checked with the returned content-type header)
  * `languages`: at least one 3-letter country code
  * `displayName`: nullable, but length from 1 to 255 chars
* `InstitutionRequest`:
  * `deqarId` or (exclusive) `eterId` has to be provided and point to an existing institution
* `LinkRequest`:
  * `link`: length from 1 to 255 chars
  * `displayName`: nullable, but length from 1 to 200 chars
* `ProgrammeRequest`:
  * `namePrimary`: length from 1 to 255 chars
  * `identifiers` if provided, all array elements must be valid `ProgrammeIdentifierRequest` objects
  * `qualificationPrimary`: nullable, but length from 1 to 255 chars
  * `alternativeNames`: if provided, all array elements must be valid `ProgrammeNameRequest` objects
  * `nqfLevel`: nullable, but length from 1 to 255 chars
  * `qfEheaLevel`: nullable, but 'short cycle', 'first cycle', 'second cycle' or 'third cycle'
  * At least one the levels must not be null (NQF level or QF EHEA level)

To allow the report updating, a `report_id` can be provided.

The `submitReport` and the `deleteReport` methods return response objects. They contain a flag, 
that indicates if the request was successful and the returned data. The data contains important information about
server-side constraints that were violated.

## What packages can be used with together with this one?
Currently, two packages are planned:
* Symfony bundle to provide the clients as services
* DEQAR contract bundle to provide abstract entities and repositories for API results, that can be easily persisted locally

## How to test the package?
The package provides PHPUnit tests for both API clients. In a local environment the tests 
and static code analysis can be executed via docker.
```bash
./development.sh docker-build
docker-compose run --rm -T phpunit php vendor/bin/phpunit --configuration phpunit.xml tests
docker-compose run --rm -T phpunit php -d memory_limit=2G vendor/bin/phpstan analyse src tests --level 8
```

The PHPUnit tests and a static code analysis via PHPStan are also executed via GitHub actions on any push or PR.
The GitHub Actions CI runs with all supported PHP versions and all supported Symfony versions.

## Contributing
I am really happy that the software developer community loves Open Source, like I do! ♥

That's why I appreciate every issue that is opened (preferably constructive)
and every pull request that provides other or even better code to this package.

You are all breathtaking!

## Special Thanks
This project is financed by the European Quality Assurance Register (EQAR) and the European Union, which I am very thankful for!