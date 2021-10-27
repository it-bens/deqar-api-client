<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\SubmissionApi\Validation;

use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
#[Attribute]
final class InstitutionRequestHasOneId extends Constraint
{
    public string $message = 'The institution request requires exactly one valid id: DEQAR ID or ETER ID.';

    public function getTargets(): array|string
    {
        return self::CLASS_CONSTRAINT;
    }
}
