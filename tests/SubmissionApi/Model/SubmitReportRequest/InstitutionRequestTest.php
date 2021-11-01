<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\Tests\SubmissionApi\Model\SubmitReportRequest;

use ITB\DeqarApiClient\SubmissionApi\Model\SubmitReportRequest\InstitutionRequest;
use ITB\DeqarApiClient\SubmissionApi\Validation\ExistingInstitution;
use ITB\DeqarApiClient\SubmissionApi\Validation\InstitutionRequestHasOneId;
use ITB\DeqarApiClient\ValidatorTrait;
use ITB\DeqarApiClient\WebApi\WebApiClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class InstitutionRequestTest extends TestCase
{
    use ValidatorTrait;

    private ValidatorInterface $validator;

    public static function createInstitutionRequest(): InstitutionRequest
    {
        return new InstitutionRequest('DEQARINST0332');
    }

    /**
     * @return InstitutionRequest[][]
     */
    public function provideInvalidInstitutionNonExistent(): array
    {
        $request = self::createInstitutionRequest();
        $request->deqarId = 'DEQARINST00000';
        $request->eterId = null;
        $request2 = self::createInstitutionRequest();
        $request2->deqarId = null;
        $request2->eterId = 'ETTHBNE';

        return ['with-deqar-id' => [$request], 'with-eter-id' => [$request2]];
    }

    /**
     * @return InstitutionRequest[][]
     */
    public function provideRequestInvalidDeqarIdInvalidLength(): array
    {
        $request = self::createInstitutionRequest();
        $request->deqarId = '';

        return [[$request]];
    }

    /**
     * @return InstitutionRequest[][]
     */
    public function provideRequestInvalidDeqarInvalidRegex(): array
    {
        $request = self::createInstitutionRequest();
        $request->deqarId = 'DAQ46546';

        return [[$request]];
    }

    public function provideRequestInvalidEterId(): array
    {
        $request = self::createInstitutionRequest();
        $request->eterId = '';

        return [[$request]];
    }

    /**
     * @return InstitutionRequest[][]
     */
    public function provideRequestInvalidHasOneId(): array
    {
        $request = self::createInstitutionRequest();
        $request->deqarId = null;
        $request->eterId = null;
        $request2 = self::createInstitutionRequest();
        $request2->deqarId = 'DEQARINST0332';
        $request2->eterId = 'RO0001';

        return ['no-id' => [$request], 'both-id' => [$request]];
    }

    /**
     * @return InstitutionRequest[][]
     */
    public function provideRequestValid(): array
    {
        return [[self::createInstitutionRequest()]];
    }

    public function setUp(): void
    {
        $httpClient = HttpClient::create(['headers' => ['Accept' => 'application/json'], 'verify_host' => false, 'verify_peer' => false]);

        $webApiClient = WebApiClient::create($_ENV['DEQAR_API_USERNAME'], $_ENV['DEQAR_API_PASSWORD']);
        $this->validator = self::buildValidator($httpClient, $webApiClient);
    }

    /**
     * @dataProvider provideRequestInvalidDeqarIdInvalidLength
     * @param InstitutionRequest $request
     */
    public function testInvalidDeqarIdInvalidLength(InstitutionRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(1, $violations);
        self::assertEquals('deqarId', $violations[0]->getPropertyPath());
        self::assertInstanceOf(Length::class, $violations[0]->getConstraint());
    }

    /**
     * @dataProvider provideRequestInvalidDeqarInvalidRegex
     * @param InstitutionRequest $request
     */
    public function testInvalidDeqarIdInvalidRegex(InstitutionRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(1, $violations);
        self::assertEquals('deqarId', $violations[0]->getPropertyPath());
        self::assertInstanceOf(Regex::class, $violations[0]->getConstraint());
    }

    /**
     * @dataProvider provideRequestInvalidEterId
     * @param InstitutionRequest $request
     */
    public function testInvalidEterId(InstitutionRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(1, $violations);
        self::assertEquals('eterId', $violations[0]->getPropertyPath());
        self::assertInstanceOf(Length::class, $violations[0]->getConstraint());
    }

    /**
     * @dataProvider provideRequestInvalidHasOneId
     * @param InstitutionRequest $request
     */
    public function testInvalidHasOneId(InstitutionRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(1, $violations);
        self::assertInstanceOf(InstitutionRequestHasOneId::class, $violations[0]->getConstraint());
    }

    /**
     * @dataProvider provideInvalidInstitutionNonExistent
     * @param InstitutionRequest $request
     */
    public function testInvalidInstitutionNonExistent(InstitutionRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(1, $violations);
        self::assertInstanceOf(ExistingInstitution::class, $violations[0]->getConstraint());
    }

    /**
     * @dataProvider provideRequestValid
     * @param InstitutionRequest $request
     */
    public function testValid(InstitutionRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(0, $violations);
    }
}
