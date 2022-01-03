<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\Tests\SubmissionApi\Model\SubmitReportRequest;

use ITB\DeqarApiClient\SubmissionApi\Model\SubmitReportRequest\ProgrammeRequest;
use ITB\DeqarApiClient\Tests\SubmissionApi\AbstractSubmissionApiTest;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Country;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\ConstraintViolation;

final class ProgrammeRequestTest extends AbstractSubmissionApiTest
{
    /**
     * @return ProgrammeRequest
     */
    public static function createProgrammeRequest(): ProgrammeRequest
    {
        return new ProgrammeRequest(
            'Informatik, angewandte',
            [ProgrammeIdentifierRequestTest::createProgrammeIdentifierRequest()],
            'Master of Medicine',
            [ProgrammeNameRequestTest::createProgrammeNameRequest()],
            ['GBR', 'DEU'],
            'Level 6',
            qfEheaLevel: ProgrammeRequest::PROGRAMME_QF_EHEA_LEVEL_FIRST_CYCLE
        );
    }

    /**
     * @return ProgrammeRequest[][]
     */
    public function provideRequestInvalidAlternativeNamesEmpty(): array
    {
        $request = self::createProgrammeRequest();
        $request->alternativeNames = [];

        return [[$request]];
    }

    /**
     * @return ProgrammeRequest[][]
     */
    public function provideRequestInvalidAlternativeNamesType(): array
    {
        $request = self::createProgrammeRequest();
        /** @phpstan-ignore-next-line */
        $request->alternativeNames = ['AlternativeName1', 1337, true];

        return [[$request]];
    }

    /**
     * @return ProgrammeRequest[][]
     */
    public function provideRequestInvalidCountriesEmpty(): array
    {
        $request = self::createProgrammeRequest();
        $request->countries = [];

        return [[$request]];
    }

    /**
     * @return ProgrammeRequest[][]
     */
    public function provideRequestInvalidCountriesInvalidCountryCode(): array
    {
        $request = self::createProgrammeRequest();
        /** @phpstan-ignore-next-line */
        $request->countries = ['eng', 123];

        return [[$request]];
    }

    /**
     * @return ProgrammeRequest[][]
     */
    public function provideRequestInvalidIdentifiersEmpty(): array
    {
        $request = self::createProgrammeRequest();
        $request->identifiers = [];

        return [[$request]];
    }

    /**
     * @return ProgrammeRequest[][]
     */
    public function provideRequestInvalidIdentifiersType(): array
    {
        $request = self::createProgrammeRequest();
        /** @phpstan-ignore-next-line */
        $request->identifiers = ['Identifier1', 1337, true];

        return [[$request]];
    }

    /**
     * @return ProgrammeRequest[][]
     */
    public function provideRequestInvalidNamePrimary(): array
    {
        $request = self::createProgrammeRequest();
        $request->namePrimary = '';

        return [[$request]];
    }

    /**
     * @return ProgrammeRequest[][]
     */
    public function provideRequestInvalidNqfLevel(): array
    {
        $request = self::createProgrammeRequest();
        $request->nqfLevel = '';

        return [[$request]];
    }

    /**
     * @return ProgrammeRequest[][]
     */
    public function provideRequestInvalidQfEheaLevel(): array
    {
        $request = self::createProgrammeRequest();
        $request->qfEheaLevel = 'blub';

        return [[$request]];
    }

    /**
     * @return ProgrammeRequest[][]
     */
    public function provideRequestTestInvalidQualificationPrimary(): array
    {
        $request = self::createProgrammeRequest();
        $request->qualificationPrimary = '';

        return [[$request]];
    }

    /**
     * @return ProgrammeRequest[][]
     */
    public function provideRequestValid(): array
    {
        return [[self::createProgrammeRequest()]];
    }

    /**
     * @dataProvider provideRequestInvalidAlternativeNamesEmpty
     * @param ProgrammeRequest $request
     */
    public function testInvalidAlternativeNamesEmpty(ProgrammeRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(1, $violations);
        /** @var non-empty-list<ConstraintViolation> $violations */
        self::assertEquals('alternativeNames', $violations[0]->getPropertyPath());
        self::assertInstanceOf(Count::class, $violations[0]->getConstraint());
    }

    /**
     * @dataProvider provideRequestInvalidAlternativeNamesType
     * @param ProgrammeRequest $request
     */
    public function testInvalidAlternativeNamesType(ProgrammeRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(3, $violations);
        foreach ($violations as $i => $violation) {
            /** @var ConstraintViolation $violation */
            self::assertEquals(sprintf('alternativeNames[%d]', $i), $violation->getPropertyPath());
            self::assertInstanceOf(Type::class, $violation->getConstraint());
        }
    }

    /**
     * @dataProvider provideRequestInvalidCountriesEmpty
     * @param ProgrammeRequest $request
     */
    public function testInvalidCountriesEmpty(ProgrammeRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(1, $violations);
        /** @var non-empty-list<ConstraintViolation> $violations */
        self::assertEquals('countries', $violations[0]->getPropertyPath());
        self::assertInstanceOf(Count::class, $violations[0]->getConstraint());
    }

    /**
     * @dataProvider provideRequestInvalidCountriesInvalidCountryCode
     * @param ProgrammeRequest $request
     */
    public function testInvalidCountriesInvalidCountryCode(ProgrammeRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(2, $violations);
        foreach ($violations as $i => $violation) {
            /** @var ConstraintViolation $violation */
            self::assertEquals(sprintf('countries[%d]', $i), $violation->getPropertyPath());
            self::assertInstanceOf(Country::class, $violation->getConstraint());
        }
    }

    /**
     * @dataProvider provideRequestInvalidIdentifiersEmpty
     * @param ProgrammeRequest $request
     */
    public function testInvalidIdentifiersEmpty(ProgrammeRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(1, $violations);
        /** @var non-empty-list<ConstraintViolation> $violations */
        self::assertEquals('identifiers', $violations[0]->getPropertyPath());
        self::assertInstanceOf(Count::class, $violations[0]->getConstraint());
    }

    /**
     * @dataProvider provideRequestInvalidIdentifiersType
     * @param ProgrammeRequest $request
     */
    public function testInvalidIdentifiersType(ProgrammeRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(3, $violations);
        foreach ($violations as $i => $violation) {
            /** @var ConstraintViolation $violation */
            self::assertEquals(sprintf('identifiers[%d]', $i), $violation->getPropertyPath());
            self::assertInstanceOf(Type::class, $violation->getConstraint());
        }
    }

    /**
     * @dataProvider provideRequestInvalidNamePrimary
     * @param ProgrammeRequest $request
     */
    public function testInvalidNamePrimary(ProgrammeRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(1, $violations);
        /** @var non-empty-list<ConstraintViolation> $violations */
        self::assertEquals('namePrimary', $violations[0]->getPropertyPath());
        self::assertInstanceOf(Length::class, $violations[0]->getConstraint());
    }

    /**
     * @dataProvider provideRequestInvalidNqfLevel
     * @param ProgrammeRequest $request
     */
    public function testInvalidNqfLevel(ProgrammeRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(1, $violations);
        /** @var non-empty-list<ConstraintViolation> $violations */
        self::assertEquals('nqfLevel', $violations[0]->getPropertyPath());
        self::assertInstanceOf(Length::class, $violations[0]->getConstraint());
    }

    /**
     * @dataProvider provideRequestInvalidQfEheaLevel
     * @param ProgrammeRequest $request
     */
    public function testInvalidQfEheaLevel(ProgrammeRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(1, $violations);
        /** @var non-empty-list<ConstraintViolation> $violations */
        self::assertEquals('qfEheaLevel', $violations[0]->getPropertyPath());
        self::assertInstanceOf(Choice::class, $violations[0]->getConstraint());
    }

    /**
     * @dataProvider provideRequestTestInvalidQualificationPrimary
     * @param ProgrammeRequest $request
     */
    public function testInvalidQualificationPrimary(ProgrammeRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(1, $violations);
        /** @var non-empty-list<ConstraintViolation> $violations */
        self::assertEquals('qualificationPrimary', $violations[0]->getPropertyPath());
        self::assertInstanceOf(Length::class, $violations[0]->getConstraint());
    }

    /**
     * @dataProvider provideRequestValid
     * @param ProgrammeRequest $request
     */
    public function testValid(ProgrammeRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(0, $violations);
    }
}
