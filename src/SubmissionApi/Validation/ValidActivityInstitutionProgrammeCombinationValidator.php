<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\SubmissionApi\Validation;

use ITB\DeqarApiClient\SubmissionApi\Model\SubmitReportRequest;
use ITB\DeqarApiClient\WebApi\WebApiClientInterface;
use RuntimeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class ValidActivityInstitutionProgrammeCombinationValidator extends ConstraintValidator
{
    public function __construct(private WebApiClientInterface $webApiClient)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidActivityInstitutionProgrammeCombination) {
            throw new UnexpectedTypeException($constraint, ValidActivityInstitutionProgrammeCombination::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value) {
            return;
        }

        if (!$value instanceof SubmitReportRequest) {
            throw new UnexpectedValueException($value, SubmitReportRequest::class);
        }

        $activity = $this->webApiClient->getActivity($value->activity);
        if (null === $activity) {
            throw new RuntimeException(sprintf('The %s must be called after the existence of the activity is validated.', self::class));
        }

        switch ($activity->type) {
            case 'institutional':
                if (0 === count($value->institutions) || 0 !== count($value->programmes)) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ activity_type }}', $activity->type)
                        ->setParameter('{{ institution_count }}', 'one or more')
                        ->setParameter('{{ programme_count }}', 'no')
                        ->addViolation();
                }
                break;
            case 'institutional/programme':
            case 'programme':
                if (1 !== count($value->institutions) || 0 === count($value->programmes)) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ activity_type }}', $activity->type)
                        ->setParameter('{{ institution_count }}', 'exactly one')
                        ->setParameter('{{ programme_count }}', 'one or more')
                        ->addViolation();
                }
                break;
            case 'joint programme':
                if (2 > count($value->institutions) || 2 > count($value->programmes)) {
                    $this->context->buildViolation($constraint->message)
                        ->setParameter('{{ activity_type }}', $activity->type)
                        ->setParameter('{{ institution_count }}', 'two or more')
                        ->setParameter('{{ programme_count }}', 'two or more')
                        ->addViolation();
                }
                break;
            default:
                // TODO: throw exception
        }
    }
}
