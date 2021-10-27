<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\SubmissionApi\Validation;

use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
#[Attribute]
final class ExistingInstitution extends Constraint
{
    public string $message = 'The institution id (deqar id|eter id) "{{ institution_id }}" does not belong to an existing institution.';

    public function getTargets(): array|string
    {
        return self::CLASS_CONSTRAINT;
    }
}
