<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\SubmissionApi\Validation;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
#[\Attribute]
final class ProgrammeRequestHasOneLevel extends Constraint
{
    public string $message = 'The programme request requires at least one valid level: NQF level or QF EHEA level.';

    public function getTargets(): array|string
    {
        return self::CLASS_CONSTRAINT;
    }
}
