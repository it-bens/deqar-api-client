<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\SubmissionApi\Model\SubmitReportRequest;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

final class ProgrammeIdentifierRequest
{
    /**
     * @param string $identifier
     * @param string|null $resource
     */
    public function __construct(
        #[SerializedName('identifier')]
        public string $identifier,
        #[SerializedName('resource')]
        public ?string $resource = null,
    ) {
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint('identifier', new Assert\Length(min: 1, max: 50, groups: ['simple']));
        $metadata->addPropertyConstraint('resource', new Assert\Length(min: 1, max: 200, groups: ['simple']));

        // Class name is added in case I forgot a constraint
        $metadata->setGroupSequence(['ProgrammeIdentifierRequest', 'simple']);
        $metadata->defaultGroup = 'Default';
    }
}
