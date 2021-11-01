<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\Tests\SubmissionApi\Model\SubmitReportRequest;

use ITB\DeqarApiClient\SubmissionApi\Model\SubmitReportRequest\ProgrammeIdentifierRequest;
use ITB\DeqarApiClient\Tests\SubmissionApi\AbstractSubmissionApiTest;
use Symfony\Component\Validator\Constraints\Length;

final class ProgrammeIdentifierRequestTest extends AbstractSubmissionApiTest
{
    /**
     * @return ProgrammeIdentifierRequest
     */
    public static function createProgrammeIdentifierRequest(): ProgrammeIdentifierRequest
    {
        return new ProgrammeIdentifierRequest('HCERES21', 'national authority');
    }

    /**
     * @return ProgrammeIdentifierRequest[][]
     */
    public function provideRequestInvalidIdentifier(): array
    {
        $request = self::createProgrammeIdentifierRequest();
        $request->identifier = '';

        return [[$request]];
    }

    /**
     * @return ProgrammeIdentifierRequest[][]
     */
    public function provideRequestInvalidResource(): array
    {
        $request = self::createProgrammeIdentifierRequest();
        $request->resource = '';

        return [[$request]];
    }

    /**
     * @return ProgrammeIdentifierRequest[][]
     */
    public function provideRequestValid(): array
    {
        return [[self::createProgrammeIdentifierRequest()]];
    }

    /**
     * @dataProvider provideRequestInvalidIdentifier
     * @param ProgrammeIdentifierRequest $request
     */
    public function testInvalidIdentifier(ProgrammeIdentifierRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(1, $violations);
        self::assertEquals('identifier', $violations[0]->getPropertyPath());
        self::assertInstanceOf(Length::class, $violations[0]->getConstraint());
    }

    /**
     * @dataProvider provideRequestInvalidResource
     * @param ProgrammeIdentifierRequest $request
     */
    public function testInvalidResource(ProgrammeIdentifierRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(1, $violations);
        self::assertEquals('resource', $violations[0]->getPropertyPath());
        self::assertInstanceOf(Length::class, $violations[0]->getConstraint());
    }

    /**
     * @dataProvider provideRequestValid
     * @param ProgrammeIdentifierRequest $request
     */
    public function testValid(ProgrammeIdentifierRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(0, $violations);
    }
}
