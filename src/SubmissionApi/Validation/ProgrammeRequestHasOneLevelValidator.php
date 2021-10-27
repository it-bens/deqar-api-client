<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\SubmissionApi\Validation;

use ITB\DeqarApiClient\SubmissionApi\Model\SubmitReportRequest\ProgrammeRequest;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class ProgrammeRequestHasOneLevelValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ProgrammeRequestHasOneLevel) {
            throw new UnexpectedTypeException($constraint, ProgrammeRequestHasOneLevel::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value) {
            return;
        }

        if (!$value instanceof ProgrammeRequest) {
            throw new UnexpectedValueException($value, ProgrammeRequest::class);
        }

        if (null === $value->nqfLevel && null === $value->qfEheaLevel) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
