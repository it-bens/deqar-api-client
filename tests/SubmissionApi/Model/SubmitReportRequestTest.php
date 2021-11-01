<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\Tests\SubmissionApi\Model;

use DateTimeImmutable;
use ITB\DeqarApiClient\SubmissionApi\Model\SubmitReportRequest;
use ITB\DeqarApiClient\SubmissionApi\Validation\ExistingActivity;
use ITB\DeqarApiClient\SubmissionApi\Validation\ExistingAgency;
use ITB\DeqarApiClient\Tests\SubmissionApi\AbstractSubmissionApiTest;
use ITB\DeqarApiClient\Tests\SubmissionApi\Model\SubmitReportRequest\FileRequestTest;
use ITB\DeqarApiClient\Tests\SubmissionApi\Model\SubmitReportRequest\InstitutionRequestTest;
use ITB\DeqarApiClient\Tests\SubmissionApi\Model\SubmitReportRequest\LinkRequestTest;
use ITB\DeqarApiClient\Tests\SubmissionApi\Model\SubmitReportRequest\ProgrammeRequestTest;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\ConstraintViolation;

final class SubmitReportRequestTest extends AbstractSubmissionApiTest
{
    public static function createSubmitReportRequest(): SubmitReportRequest
    {
        return new SubmitReportRequest(
            '5',
            '154',
            SubmitReportRequest::REPORT_STATUS_OBLIGATORY,
            SubmitReportRequest::REPORT_DECISION_POSITIVE,
            '2022-01-01',
            [FileRequestTest::createFileRequest()],
            [InstitutionRequestTest::createInstitutionRequest()],
            [ProgrammeRequestTest::createProgrammeRequest()],
            null,
            null,
            'ACQUIN-123456',
            // Activity local identifier is not tested because API documentation seems to be incomplete. Awaiting response from DEQAR.
            null,
            'I am a summary.',
            '2024-01-01',
            [LinkRequestTest::createLinkRequest()],
            'Keep going. Nothing to see, here.'
        );
    }

    /**
     * @return SubmitReportRequest[][]
     */
    public function provideRequestInvalidActivity(): array
    {
        $request = self::createSubmitReportRequest();
        $request->activity = '798576897';

        return [[$request]];
    }

    /**
     * @return SubmitReportRequest[][]
     */
    public function provideRequestInvalidActivityLocalIdentifier(): array
    {
        $request = self::createSubmitReportRequest();
        $request->activityLocalIdentifier = '';

        return [[$request]];
    }

    /**
     * @return SubmitReportRequest[][]
     */
    public function provideRequestInvalidAgency(): array
    {
        $request = self::createSubmitReportRequest();
        $request->agency = 'GRHG55';

        return [[$request]];
    }

    /**
     * @return SubmitReportRequest[][]
     */
    public function provideRequestInvalidComment(): array
    {
        $request = self::createSubmitReportRequest();
        $request->comment = '';

        return [[$request]];
    }

    /**
     * @return SubmitReportRequest[][]
     */
    public function provideRequestInvalidContributingAgenciesEmpty(): array
    {
        $request = self::createSubmitReportRequest();
        $request->contributingAgencies = [];

        return [[$request]];
    }

    /**
     * @return SubmitReportRequest[][]
     */
    public function provideRequestInvalidContributingAgenciesEmptyAgencyId(): array
    {
        $request = self::createSubmitReportRequest();
        $request->contributingAgencies = [''];

        return [[$request]];
    }

    /**
     * @return SubmitReportRequest[][]
     */
    public function provideRequestInvalidContributingAgenciesInvalidAgency(): array
    {
        $request = self::createSubmitReportRequest();
        $request->contributingAgencies = ['GRHG55'];

        return [[$request]];
    }

    /**
     * @return SubmitReportRequest[][]
     */
    public function provideRequestInvalidDecision(): array
    {
        $request = self::createSubmitReportRequest();
        $request->decision = 'I am an invalid decision!';

        return [[$request]];
    }

    /**
     * @return SubmitReportRequest[][]
     */
    public function provideRequestInvalidFilesEmpty(): array
    {
        $request = self::createSubmitReportRequest();
        $request->files = [];

        return [[$request]];
    }

    /**
     * @return SubmitReportRequest[][]
     */
    public function provideRequestInvalidFilesType(): array
    {
        $request = self::createSubmitReportRequest();
        $request->files = ['File1', 1337, true];

        return [[$request]];
    }

    /**
     * @return SubmitReportRequest[][]
     */
    public function provideRequestInvalidId(): array
    {
        $request = self::createSubmitReportRequest();
        $request->id = '';

        return [[$request]];
    }

    /**
     * @return SubmitReportRequest[][]
     */
    public function provideRequestInvalidInstitutions(): array
    {
        $request = self::createSubmitReportRequest();
        $request->institutions = ['Institution1', 1337, true];

        return [[$request]];
    }

    /**
     * @return SubmitReportRequest[][]
     */
    public function provideRequestInvalidLinksEmpty(): array
    {
        $request = self::createSubmitReportRequest();
        $request->links = [];

        return [[$request]];
    }

    /**
     * @return SubmitReportRequest[][]
     */
    public function provideRequestInvalidLinksType(): array
    {
        $request = self::createSubmitReportRequest();
        $request->links = ['Link1', 1337, true];

        return [[$request]];
    }

    /**
     * @return SubmitReportRequest[][]
     */
    public function provideRequestInvalidLocalIdentifier(): array
    {
        $request = self::createSubmitReportRequest();
        $request->localIdentifier = '';

        return [[$request]];
    }

    /**
     * @return SubmitReportRequest[][]
     */
    public function provideRequestInvalidProgrammes(): array
    {
        $request = self::createSubmitReportRequest();
        $request->programmes = ['Programme1', 1337, true];

        return [[$request]];
    }

    /**
     * @return SubmitReportRequest[][]
     */
    public function provideRequestInvalidStatus(): array
    {
        $request = self::createSubmitReportRequest();
        $request->status = 'I am an invalid status!';

        return [[$request]];
    }

    /**
     * @return SubmitReportRequest[][]
     */
    public function provideRequestInvalidSummary(): array
    {
        $request = self::createSubmitReportRequest();
        $request->summary = '';

        return [[$request]];
    }

    /**
     * @return SubmitReportRequest[][]
     */
    public function provideRequestInvalidValidFrom(): array
    {
        $request = self::createSubmitReportRequest();
        $request->validFrom = 'I am an invalid date!';
        $request2 = self::createSubmitReportRequest();
        $request2->validFrom = (new DateTimeImmutable('2022-01-01'))->format('c');

        return ['invalid-date' => [$request], 'valid-datetime' => [$request2]];
    }

    /**
     * @return SubmitReportRequest[][]
     */
    public function provideRequestInvalidValidTo(): array
    {
        $request = self::createSubmitReportRequest();
        $request->validTo = 'I am an invalid date!';
        $request2 = self::createSubmitReportRequest();
        $request2->validTo = (new DateTimeImmutable('2022-01-01'))->format('c');

        return ['invalid-date' => [$request], 'valid-datetime' => [$request2]];
    }

    /**
     * @return SubmitReportRequest[][]
     */
    public function provideRequestValid(): array
    {
        return [[self::createSubmitReportRequest()]];
    }

    /**
     * @dataProvider provideRequestInvalidActivity
     * @param SubmitReportRequest $request
     */
    public function testInvalidActivity(SubmitReportRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(1, $violations);
        self::assertEquals('activity', $violations[0]->getPropertyPath());
        self::assertInstanceOf(ExistingActivity::class, $violations[0]->getConstraint());
    }

    /**
     * @dataProvider provideRequestInvalidActivityLocalIdentifier
     * @param SubmitReportRequest $request
     */
    public function testInvalidActivityLocalIdentifier(SubmitReportRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(1, $violations);
        self::assertEquals('activityLocalIdentifier', $violations[0]->getPropertyPath());
        self::assertInstanceOf(Length::class, $violations[0]->getConstraint());
    }

    /**
     * @dataProvider provideRequestInvalidAgency
     * @param SubmitReportRequest $request
     */
    public function testInvalidAgency(SubmitReportRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(1, $violations);
        self::assertEquals('agency', $violations[0]->getPropertyPath());
        self::assertInstanceOf(ExistingAgency::class, $violations[0]->getConstraint());
    }

    /**
     * @dataProvider provideRequestInvalidComment
     * @param SubmitReportRequest $request
     */
    public function testInvalidComment(SubmitReportRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(1, $violations);
        self::assertEquals('comment', $violations[0]->getPropertyPath());
        self::assertInstanceOf(NotBlank::class, $violations[0]->getConstraint());
    }

    /**
     * @dataProvider provideRequestInvalidContributingAgenciesEmpty
     * @param SubmitReportRequest $request
     */
    public function testInvalidContributingAgenciesEmpty(SubmitReportRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(1, $violations);
        self::assertEquals('contributingAgencies', $violations[0]->getPropertyPath());
        self::assertInstanceOf(Count::class, $violations[0]->getConstraint());
    }

    /**
     * @dataProvider provideRequestInvalidContributingAgenciesEmptyAgencyId
     * @param SubmitReportRequest $request
     */
    public function testInvalidContributingAgenciesEmptyAgencyId(SubmitReportRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(1, $violations);
        self::assertEquals('contributingAgencies[0]', $violations[0]->getPropertyPath());
        self::assertInstanceOf(NotBlank::class, $violations[0]->getConstraint());
    }

    /**
     * @dataProvider provideRequestInvalidContributingAgenciesInvalidAgency
     * @param SubmitReportRequest $request
     */
    public function testInvalidContributingAgenciesInvalidAgency(SubmitReportRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(1, $violations);
        self::assertEquals('contributingAgencies[0]', $violations[0]->getPropertyPath());
        self::assertInstanceOf(ExistingAgency::class, $violations[0]->getConstraint());
    }

    /**
     * @dataProvider provideRequestInvalidDecision
     * @param SubmitReportRequest $request
     */
    public function testInvalidDecision(SubmitReportRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(1, $violations);
        self::assertEquals('decision', $violations[0]->getPropertyPath());
        self::assertInstanceOf(Choice::class, $violations[0]->getConstraint());
    }

    /**
     * @dataProvider provideRequestInvalidFilesEmpty
     * @param SubmitReportRequest $request
     */
    public function testInvalidFilesEmpty(SubmitReportRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(1, $violations);
        self::assertEquals('files', $violations[0]->getPropertyPath());
        self::assertInstanceOf(Count::class, $violations[0]->getConstraint());
    }

    /**
     * @dataProvider provideRequestInvalidFilesType
     * @param SubmitReportRequest $request
     */
    public function testInvalidFilesType(SubmitReportRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(3, $violations);
        foreach ($violations as $i => $violation) {
            /** @var ConstraintViolation $violation */
            self::assertEquals(sprintf('files[%d]', $i), $violation->getPropertyPath());
            self::assertInstanceOf(Type::class, $violation->getConstraint());
        }
    }

    /**
     * @dataProvider provideRequestInvalidId
     * @param SubmitReportRequest $request
     */
    public function testInvalidId(SubmitReportRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(1, $violations);
        self::assertEquals('id', $violations[0]->getPropertyPath());
        self::assertInstanceOf(NotBlank::class, $violations[0]->getConstraint());
    }

    /**
     * @dataProvider provideRequestInvalidInstitutions
     * @param SubmitReportRequest $request
     */
    public function testInvalidInstitutions(SubmitReportRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(3, $violations);
        foreach ($violations as $i => $violation) {
            /** @var ConstraintViolation $violation */
            self::assertEquals(sprintf('institutions[%d]', $i), $violation->getPropertyPath());
            self::assertInstanceOf(Type::class, $violation->getConstraint());
        }
    }

    /**
     * @dataProvider provideRequestInvalidLinksEmpty
     * @param SubmitReportRequest $request
     */
    public function testInvalidLinksEmpty(SubmitReportRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(1, $violations);
        self::assertEquals('links', $violations[0]->getPropertyPath());
        self::assertInstanceOf(Count::class, $violations[0]->getConstraint());
    }

    /**
     * @dataProvider provideRequestInvalidLinksType
     * @param SubmitReportRequest $request
     */
    public function testInvalidLinksType(SubmitReportRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(3, $violations);
        foreach ($violations as $i => $violation) {
            /** @var ConstraintViolation $violation */
            self::assertEquals(sprintf('links[%d]', $i), $violation->getPropertyPath());
            self::assertInstanceOf(Type::class, $violation->getConstraint());
        }
    }

    /**
     * @dataProvider provideRequestInvalidLocalIdentifier
     * @param SubmitReportRequest $request
     */
    public function testInvalidLocalIdentifier(SubmitReportRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(1, $violations);
        self::assertEquals('localIdentifier', $violations[0]->getPropertyPath());
        self::assertInstanceOf(Length::class, $violations[0]->getConstraint());
    }

    /**
     * @dataProvider provideRequestInvalidProgrammes
     * @param SubmitReportRequest $request
     */
    public function testInvalidProgrammes(SubmitReportRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(3, $violations);
        foreach ($violations as $i => $violation) {
            /** @var ConstraintViolation $violation */
            self::assertEquals(sprintf('programmes[%d]', $i), $violation->getPropertyPath());
            self::assertInstanceOf(Type::class, $violation->getConstraint());
        }
    }

    /**
     * @dataProvider provideRequestInvalidStatus
     * @param SubmitReportRequest $request
     */
    public function testInvalidStatus(SubmitReportRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(1, $violations);
        self::assertEquals('status', $violations[0]->getPropertyPath());
        self::assertInstanceOf(Choice::class, $violations[0]->getConstraint());
    }

    /**
     * @dataProvider provideRequestInvalidSummary
     * @param SubmitReportRequest $request
     */
    public function testInvalidSummary(SubmitReportRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(1, $violations);
        self::assertEquals('summary', $violations[0]->getPropertyPath());
        self::assertInstanceOf(NotBlank::class, $violations[0]->getConstraint());
    }

    /**
     * @dataProvider provideRequestInvalidValidFrom
     * @param SubmitReportRequest $request
     */
    public function testInvalidValidFrom(SubmitReportRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(1, $violations);
        self::assertEquals('validFrom', $violations[0]->getPropertyPath());
        self::assertInstanceOf(Date::class, $violations[0]->getConstraint());
    }

    /**
     * @dataProvider provideRequestInvalidValidTo
     * @param SubmitReportRequest $request
     */
    public function testInvalidValidTo(SubmitReportRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(1, $violations);
        self::assertEquals('validTo', $violations[0]->getPropertyPath());
        self::assertInstanceOf(Date::class, $violations[0]->getConstraint());
    }

    /**
     * @dataProvider provideRequestValid
     * @param SubmitReportRequest $request
     */
    public function testValid(SubmitReportRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(0, $violations);
    }
    #    public function testInvalidInstitutional(): void {}
    #    public function testInvalidInstitutionalProgramme(): void {}
    #    public function testInvalidProgramme(): void {}
    #    public function testInvalidJointProgramme(): void {}
}
