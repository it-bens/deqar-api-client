<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\Tests\SubmissionApi;

use ITB\DeqarApiClient\SubmissionApi\Model\DeleteReportResponseSuccess;
use ITB\DeqarApiClient\SubmissionApi\Model\SubmitReportRequest;
use ITB\DeqarApiClient\SubmissionApi\Model\SubmitReportResponseSuccess;
use ITB\DeqarApiClient\SubmissionApi\SubmissionApiClient;
use ITB\DeqarApiClient\SubmissionApi\SubmissionApiClientInterface;
use ITB\DeqarApiClient\Tests\SubmissionApi\Model\SubmitReportRequestTest;
use PHPUnit\Framework\TestCase;

final class SubmissionApiClientTest extends TestCase
{
    private SubmissionApiClientInterface $submissionApiClient;

    public function setUp(): void
    {
        $this->submissionApiClient = SubmissionApiClient::create($_ENV['DEQAR_API_USERNAME'], $_ENV['DEQAR_API_PASSWORD'], true);
    }

    public function provideSubmitReportRequestValid(): array
    {
        return [[SubmitReportRequestTest::createSubmitReportRequest()]];
    }

    /**
     * @dataProvider provideSubmitReportRequestValid
     * @param SubmitReportRequest $request
     */
    public function testSubmitReportValid(SubmitReportRequest $request): void
    {
        $response = $this->submissionApiClient->submitReport($request);
        self::assertInstanceOf(SubmitReportResponseSuccess::class, $response);
    }

    public function testDeleteReport(): void
    {
        $response = $this->submissionApiClient->deleteReport('70013');
        self::assertInstanceOf(DeleteReportResponseSuccess::class, $response);
    }
}
