<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\SubmissionApi\Validation;

use ITB\DeqarApiClient\SubmissionApi\Model\SubmitReportRequest\InstitutionRequest;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class InstitutionRequestHasOneIdValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof InstitutionRequestHasOneId) {
            throw new UnexpectedTypeException($constraint, InstitutionRequestHasOneId::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value) {
            return;
        }

        if (!$value instanceof InstitutionRequest) {
            throw new UnexpectedValueException($value, InstitutionRequest::class);
        }

        if (null === $value->deqarId && null === $value->eterId) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
        if (null !== $value->deqarId && null !== $value->eterId) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
