<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\SubmissionApi\Validation;

use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
#[Attribute]
final class ValidActivityInstitutionProgrammeCombination extends Constraint
{
    public string $message = 'The report submit request with the activity type "{{ activity_type }}" requires {{ institution_count }} institution(s) and {{ programme_count }} programme(s).';

    public function getTargets(): array|string
    {
        return self::CLASS_CONSTRAINT;
    }
}
