<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\SubmissionApi\Model\SubmitReportRequest;

use ITB\DeqarApiClient\SubmissionApi\Validation as SubmissionValidation;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

final class FileRequest
{
    /**
     * @param string $originalLocation
     * @param string[] $languages
     * @param string|null $displayName
     */
    public function __construct(
        #[SerializedName('original_location')]
        public string $originalLocation,
        #[SerializedName('report_language')]
        public array $languages,
        #[SerializedName('display_name')]
        public ?string $displayName = null,
    ) {
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint(
            'originalLocation',
            new Assert\Sequentially([
                'constraints' => [
                    new Assert\Length(min: 1, max: 500, groups: ['Simple']),
                    new Assert\Url(groups: ['Simple']),
                    new SubmissionValidation\WebFilePdf(['groups' => ['Complex']])],
                'groups' => ['Simple', 'Complex']
            ])
        );
        $metadata->addPropertyConstraint(
            'languages',
            new Assert\Sequentially([
                'constraints' => [
                    new Assert\Count(min: 1, groups: ['Simple']),
                    new Assert\All(['constraints' => [new Assert\Language(alpha3: true)], 'groups' => ['Simple']])
                ],
                'groups' => ['Simple']
            ])
        );
        $metadata->addPropertyConstraint('displayName', new Assert\Length(min: 1, max: 255, groups: ['Simple']));

        // Class name is added in case I forgot a constraint
        $metadata->setGroupSequence(['FileRequest', 'Simple', 'Complex']);
        $metadata->defaultGroup = 'Default';
    }
}
