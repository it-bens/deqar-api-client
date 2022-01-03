<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\SubmissionApi\Validation;

use ITB\DeqarApiClient\WebApi\WebApiClientInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class ExistingActivityValidator extends ConstraintValidator
{
    public function __construct(private WebApiClientInterface $webApiClient)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ExistingActivity) {
            throw new UnexpectedTypeException($constraint, ExistingActivity::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        $activity = $this->webApiClient->getActivity($value);
        if (null === $activity) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ activity_id }}', $value)
                ->addViolation();
        }
    }
}
