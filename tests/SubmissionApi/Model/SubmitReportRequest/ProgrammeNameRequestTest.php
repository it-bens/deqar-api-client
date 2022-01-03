<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\Tests\SubmissionApi\Model\SubmitReportRequest;

use ITB\DeqarApiClient\SubmissionApi\Model\SubmitReportRequest\ProgrammeNameRequest;
use ITB\DeqarApiClient\Tests\SubmissionApi\AbstractSubmissionApiTest;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\ConstraintViolation;

final class ProgrammeNameRequestTest extends AbstractSubmissionApiTest
{
    /**
     * @return ProgrammeNameRequest
     */
    public static function createProgrammeNameRequest(): ProgrammeNameRequest
    {
        return new ProgrammeNameRequest('Medical Natural Sciences', 'Master of Medicine in de specialistische geneeskunde');
    }

    /**
     * @return ProgrammeNameRequest[][]
     */
    public function provideRequestInvalidName(): array
    {
        $request = self::createProgrammeNameRequest();
        $request->name = '';

        return [[$request]];
    }

    /**
     * @return ProgrammeNameRequest[][]
     */
    public function provideRequestInvalidQualification(): array
    {
        $request = self::createProgrammeNameRequest();
        $request->qualification = '';

        return [[$request]];
    }

    /**
     * @return ProgrammeNameRequest[][]
     */
    public function provideRequestValid(): array
    {
        return [[self::createProgrammeNameRequest()]];
    }

    /**
     * @dataProvider provideRequestInvalidName
     * @param ProgrammeNameRequest $request
     */
    public function testInvalidName(ProgrammeNameRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(1, $violations);
        /** @var non-empty-list<ConstraintViolation> $violations */
        self::assertEquals('name', $violations[0]->getPropertyPath());
        self::assertInstanceOf(Length::class, $violations[0]->getConstraint());
    }

    /**
     * @dataProvider provideRequestInvalidQualification
     * @param ProgrammeNameRequest $request
     */
    public function testInvalidQualification(ProgrammeNameRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(1, $violations);
        /** @var non-empty-list<ConstraintViolation> $violations */
        self::assertEquals('qualification', $violations[0]->getPropertyPath());
        self::assertInstanceOf(Length::class, $violations[0]->getConstraint());
    }

    /**
     * @dataProvider provideRequestValid
     * @param ProgrammeNameRequest $request
     */
    public function testValid(ProgrammeNameRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(0, $violations);
    }
}
