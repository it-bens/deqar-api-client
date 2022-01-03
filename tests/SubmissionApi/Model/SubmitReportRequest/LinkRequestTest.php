<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\Tests\SubmissionApi\Model\SubmitReportRequest;

use ITB\DeqarApiClient\SubmissionApi\Model\SubmitReportRequest\LinkRequest;
use ITB\DeqarApiClient\Tests\SubmissionApi\AbstractSubmissionApiTest;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\ConstraintViolation;

final class LinkRequestTest extends AbstractSubmissionApiTest
{
    /**
     * @return LinkRequest
     */
    public static function createLinkRequest(): LinkRequest
    {
        return new LinkRequest('http://srv.aneca.es/ListadoTitulos/node/1182321350', 'General information on programme');
    }

    /**
     * @return LinkRequest[][]
     */
    public function provideRequestInvalidDisplayName(): array
    {
        $request = self::createLinkRequest();
        $request->displayName = '';

        return [[$request]];
    }

    /**
     * @return LinkRequest[][]
     */
    public function provideRequestInvalidLink(): array
    {
        $request = self::createLinkRequest();
        $request->link = '';

        return [[$request]];
    }

    /**
     * @return LinkRequest[][]
     */
    public function provideRequestValid(): array
    {
        return [[self::createLinkRequest()]];
    }

    /**
     * @dataProvider provideRequestInvalidDisplayName
     * @param LinkRequest $request
     */
    public function testInvalidDisplayName(LinkRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(1, $violations);
        /** @var non-empty-list<ConstraintViolation> $violations */
        self::assertEquals('displayName', $violations[0]->getPropertyPath());
        self::assertInstanceOf(Length::class, $violations[0]->getConstraint());
    }

    /**
     * @dataProvider provideRequestInvalidLink
     * @param LinkRequest $request
     */
    public function testInvalidName(LinkRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(1, $violations);
        /** @var non-empty-list<ConstraintViolation> $violations */
        self::assertEquals('link', $violations[0]->getPropertyPath());
        self::assertInstanceOf(Length::class, $violations[0]->getConstraint());
    }

    /**
     * @dataProvider provideRequestValid
     * @param LinkRequest $request
     */
    public function testValid(LinkRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(0, $violations);
    }
}
