<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\SubmissionApi\Validation;

use ITB\DeqarApiClient\SubmissionApi\Model\SubmitReportRequest\InstitutionRequest;
use ITB\DeqarApiClient\WebApi\WebApiClientInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class ExistingInstitutionValidator extends ConstraintValidator
{
    public function __construct(private WebApiClientInterface $webApiClient)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ExistingInstitution) {
            throw new UnexpectedTypeException($constraint, ExistingInstitution::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value) {
            return;
        }

        if (!$value instanceof InstitutionRequest) {
            throw new UnexpectedValueException($value, 'string');
        }

        if (null !== $value->deqarId) {
            $institution = $this->webApiClient->getInstitutionSimple($value->deqarId);
            if (null === $institution) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ institution_id }}', $value->deqarId)
                    ->addViolation();
            }
            return;
        }

        if (null !== $value->eterId) {
            $institution = $this->webApiClient->getInstitutionSimple($value->eterId);
            if (null === $institution) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ institution_id }}', $value->eterId)
                    ->addViolation();
            }
            return;
        }
        // TODO: throw exception because this validation should not be executed without the InstitutionRequestHasOneId validation.
    }
}
