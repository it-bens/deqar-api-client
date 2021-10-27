<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\SubmissionApi\Model\SubmitReportRequest;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

final class LinkRequest
{
    public function __construct(
        #[SerializedName('link')]
        public string $link,
        #[SerializedName('display_name')]
        public ?string $displayName = null,
    ) {
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint('link', new Assert\Length(min: 1, max: 255, groups: ['simple']));
        $metadata->addPropertyConstraint('displayName', new Assert\Length(min: 1, max: 200, groups: ['simple']));

        // Class name is added in case I forgot a constraint
        $metadata->setGroupSequence(['LinkRequest', 'simple']);
        $metadata->defaultGroup = 'Default';
    }
}
