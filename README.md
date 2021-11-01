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

## Which API Client provides this package?
This package contains clients for the WebAPI and the SubmissionAPI.

### WebAPI
The WebApi provides endpoints for agencies, countries, institutions and reports.
There is always an endpoint for retrieving a collection of resources and one or more for retrieving more detailed resources.
Currently, this client uses mostly the collection endpoints and provides methods for filtering them 
to retrieve a single less-detailed resource by unique identifiers.

Internally, the Symfony `HttpClientInterface` is used. With the static `create` method of the `WebApiClient`, 
an instance with the Symfony HttpClient and Symfony Serializer (with the necessary normalizers and extractors) is created.
However, the constructor is public and can be used to pass custom instances of the `HttpClientInterface` and the `SerializerInterface`.

The responses are mapped to DTOs via Symfony Serializer. The methods for retrieving a single less-detailed version of a resource,
rely on filtering the collection responses. To prevent unnecessary DEQAR API hits, a cached client, 
that decorated the existing client, will be implemented.

The following methods are provided by the `WebApiClient`:
* getActivities (result: array of `Activity` objects, the activities are extracted from the agencies)
* getActivity (result: `Activity` object or null, identified by `id` or `activity`)
* getAgencies (result: array of `SimpleAgency` objects)
* getAgencySimple (result: `SimpleAgency` object or null, identified by `id`, `deqar_id` or `name_primary`)
* getCountries (result: array of `SimpleCountry` objects)
* getInstitutions (result: array of `SimpleInstitution` objects)
* getInstitutionSimple (result: `SimpleInstitution` or null, identified by `deqar_id` or `eter_id`)
* getReports (result: array of `SimpleReport` objects, use with caution because over 60K reports are returned)

The favourable implementation of the `WebApiClientInterface` is the `CachedWebApiClient`. 
It uses the symfony/cache-contracts to cache the responses and increase the performance in decrease the WebApi hits.
When created via the static function, the filesystem adapter is used for caching. By using the constructor,
any adapter implementing the `CacheInterface`, can be used. The client caches its results for 1 day (86400 seconds).

### SubmissionAPI
The submission API implements only two endpoints: submit/update a report and delete a report.
The submission process involves severs server-sided checks of the provided data. 
Some checks are performed immediately, which results in a `SubmissionReportErrorResponse` if the data are invalid.
Other checks are done asynchronously, which means that reports can be marked as invalid after a successful submission.

To prevent sending invalid, data the inputs are validated against several constraints within this client.
The data have to be provided as a `SubmitReportRequest` object, which is validated with the Symfony Validator.
The following logic constraints are applied (aside from basic type and content validation):
* A certain number of institutions and programmes have to be provided according to (https://docs.deqar.eu/report_data/#activity)
* The provided agency, the activity and the institutions must exist (checked against the WebAPI)
* At least one the `ProgrammeRequest`'s levels must not be null (NQF level or QF EHEA level)
* All file links must point to PDF files (checked with the returned content-type header)

To allow the report updating, a `report_id` can be provided.

## What packages can be used with together with this one?
Currently, two packages are planned:
* Symfony bundle to provide the clients as services
* DEQAR contract bundle to provide abstract entities and repositories for API results, that can be easily persisted locally

## Contributing
I am really happy that the software developer community loves Open Source, like I do! ♥

That's why I appreciate every issue that is opened (preferably constructive)
and every pull request that provides other or even better code to this package.

You are all breathtaking!

## Special Thanks
This project is financed by the European Quality Assurance Register (EQAR), which I am very thankful for!