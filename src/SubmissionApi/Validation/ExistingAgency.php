<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\SubmissionApi\Validation;

use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
#[Attribute]
final class ExistingAgency extends Constraint
{
    public string $message = 'The agency id (id|deqar id|primary name) "{{ agency_id }}" does not belong to an existing agency.';

    public function getTargets(): array|string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
