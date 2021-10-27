<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\SubmissionApi\Validation;

use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
#[Attribute]
final class ExistingActivity extends Constraint
{
    public string $message = 'The activity id (id|activity name) "{{ activity_id }}" does not belong to an existing activity.';

    public function getTargets(): array|string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
