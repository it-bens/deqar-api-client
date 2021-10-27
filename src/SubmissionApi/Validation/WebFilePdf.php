<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\SubmissionApi\Validation;

use Attribute;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
#[Attribute]
final class WebFilePdf extends Constraint
{
    public string $message = 'The file link "{{ file_link }}" does not point to a PDF file.';

    public function getTargets(): array|string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
