<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\SubmissionApi\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Throwable;

final class WebFilePdfValidator extends ConstraintValidator
{
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof WebFilePdf) {
            throw new UnexpectedTypeException($constraint, WebFilePdf::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        try {
            $response = $this->httpClient->request('GET', $value);
            $contentTypeHeaders = $response->getHeaders(true)['content-type'];
            if (!in_array('application/pdf', $contentTypeHeaders)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ file_link }}', $value)
                    ->addViolation();
                return;
            }
        } catch (Throwable $exception) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ file_link }}', $value)
                ->addViolation();
            return;
        }
    }
}
