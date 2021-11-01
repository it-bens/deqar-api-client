<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\SubmissionApi\Model\SubmitReportRequest;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

final class ProgrammeNameRequest
{
    /**
     * @param string $name
     * @param string|null $qualification
     */
    public function __construct(
        #[SerializedName('name_alternative')]
        public string $name,
        #[SerializedName('qualification_alternative')]
        public ?string $qualification = null,
    ) {
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint('name', new Assert\Length(min: 1, max: 200, groups: ['simple']));
        $metadata->addPropertyConstraint('qualification', new Assert\Length(min: 1, max: 200, groups: ['simple']));

        // Class name is added in case I forgot a constraint
        $metadata->setGroupSequence(['ProgrammeNameRequest', 'simple']);
        $metadata->defaultGroup = 'Default';
    }
}
