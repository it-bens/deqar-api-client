<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\SubmissionApi\Validation;

use ITB\DeqarApiClient\WebApi\WebApiClientInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class ExistingAgencyValidator extends ConstraintValidator
{
    public function __construct(private WebApiClientInterface $webApiClient)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ExistingAgency) {
            throw new UnexpectedTypeException($constraint, ExistingAgency::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        $agency = $this->webApiClient->getAgencySimple($value);
        if (null === $agency) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ agency_id }}', $value)
                ->addViolation();
        }
    }
}
