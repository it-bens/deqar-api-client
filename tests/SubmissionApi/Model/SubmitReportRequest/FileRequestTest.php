<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\Tests\SubmissionApi\Model\SubmitReportRequest;

use ITB\DeqarApiClient\SubmissionApi\Model\SubmitReportRequest\FileRequest;
use ITB\DeqarApiClient\SubmissionApi\Validation\WebFilePdf;
use ITB\DeqarApiClient\ValidatorTrait;
use ITB\DeqarApiClient\WebApi\WebApiClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Language;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class FileRequestTest extends TestCase
{
    use ValidatorTrait;

    private ValidatorInterface $validator;

    public static function createFileRequest(): FileRequest
    {
        return new FileRequest('https://www.orimi.com/pdf-test.pdf', ['eng'], 'PDF TEST');
    }

    /**
     * @return FileRequest[][]
     */
    public function provideRequestInvalidDisplayName(): array
    {
        $request = self::createFileRequest();
        $request->displayName = '';

        return [[$request]];
    }

    /**
     * @return FileRequest[][]
     */
    public function provideRequestInvalidLanguagesEmpty(): array
    {
        $request = self::createFileRequest();
        $request->languages = [];

        return [[$request]];
    }

    /**
     * @return FileRequest[][]
     */
    public function provideRequestInvalidLanguagesInvalidLanguageCode(): array
    {
        $request = self::createFileRequest();
        $request->languages = ['uk', '123'];

        return [[$request]];
    }

    /**
     * @return FileRequest[][]
     */
    public function provideRequestInvalidOriginalLocationInvalidFile(): array
    {
        $request = self::createFileRequest();
        $request->originalLocation = 'https://www.orimi.com/pdf-test-blub.pdf';
        $request2 = self::createFileRequest();
        $request2->originalLocation = 'https://www.shutterstock.com/image-photo/businessman-survey-results-analysis-discovery-concept-1027421584';

        return ['inexistent-file' => [$request], 'non-pdf-file' => [$request]];
    }

    /**
     * @return FileRequest[][]
     */
    public function provideRequestInvalidOriginalLocationInvalidUrl(): array
    {
        $request = self::createFileRequest();
        $request->originalLocation = 'blub//orimi.com/pdf-test.pdf';

        return [[$request]];
    }

    /**
     * @return FileRequest[][]
     */
    public function provideRequestValid(): array
    {
        return [[self::createFileRequest()]];
    }

    public function setUp(): void
    {
        $httpClient = HttpClient::create(['headers' => ['Accept' => 'application/json'], 'verify_host' => false, 'verify_peer' => false]);

        $webApiClient = WebApiClient::create($_ENV['DEQAR_API_USERNAME'], $_ENV['DEQAR_API_PASSWORD']);
        $this->validator = self::buildValidator($httpClient, $webApiClient);
    }

    /**
     * @dataProvider provideRequestInvalidDisplayName
     * @param FileRequest $request
     */
    public function testInvalidDisplayName(FileRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(1, $violations);
        self::assertEquals('displayName', $violations[0]->getPropertyPath());
        self::assertInstanceOf(Length::class, $violations[0]->getConstraint());
    }

    /**
     * @dataProvider provideRequestInvalidLanguagesEmpty
     * @param FileRequest $request
     */
    public function testInvalidLanguagesEmpty(FileRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(1, $violations);
        self::assertEquals('languages', $violations[0]->getPropertyPath());
        self::assertInstanceOf(Count::class, $violations[0]->getConstraint());
    }

    /**
     * @dataProvider provideRequestInvalidLanguagesInvalidLanguageCode
     * @param FileRequest $request
     */
    public function testInvalidLanguagesInvalidLanguageCode(FileRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(2, $violations);
        foreach ($violations as $i => $violation) {
            /** @var ConstraintViolation $violation */
            self::assertEquals(sprintf('languages[%d]', $i), $violation->getPropertyPath());
            self::assertInstanceOf(Language::class, $violation->getConstraint());
        }
    }

    /**
     * @dataProvider provideRequestInvalidOriginalLocationInvalidFile
     * @param FileRequest $request
     */
    public function testInvalidOriginalLocationInvalidFile(FileRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(1, $violations);
        self::assertEquals('originalLocation', $violations[0]->getPropertyPath());
        self::assertInstanceOf(WebFilePdf::class, $violations[0]->getConstraint());
    }

    /**
     * @dataProvider provideRequestInvalidOriginalLocationInvalidUrl
     * @param FileRequest $request
     */
    public function testInvalidOriginalLocationInvalidUrl(FileRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(1, $violations);
        self::assertEquals('originalLocation', $violations[0]->getPropertyPath());
        self::assertInstanceOf(Url::class, $violations[0]->getConstraint());
    }

    /**
     * @dataProvider provideRequestValid
     * @param FileRequest $request
     */
    public function testValid(FileRequest $request): void
    {
        $violations = $this->validator->validate($request);
        self::assertCount(0, $violations);
    }
}
