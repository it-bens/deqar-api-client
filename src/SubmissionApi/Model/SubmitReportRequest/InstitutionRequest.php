<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\SubmissionApi\Model\SubmitReportRequest;

use ITB\DeqarApiClient\SubmissionApi\Validation as SubmissionValidation;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

final class InstitutionRequest
{
    public function __construct(
        #[SerializedName('deqar_id')]
        public ?string $deqarId = null,
        #[SerializedName('eter_id')]
        public ?string $eterId = null,
    ) {
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint(
            'deqarId',
            new Assert\Sequentially([
                'constraints' => [
                    new Assert\Length(min: 1, max: 25, groups: ['simple']),
                    new Assert\Regex('/DEQARINST[0-9]{1,}/', groups: ['simple']),
                ],
                'groups' => ['simple']
            ])
        );
        $metadata->addPropertyConstraint('eterId', new Assert\Length(min: 1, max: 15, groups: ['simple']));

        $metadata->addConstraint(new SubmissionValidation\InstitutionRequestHasOneId(['groups' => ['class']]));
        $metadata->addConstraint(new SubmissionValidation\ExistingInstitution(['groups' => ['class-complex']]));

        // Class name is added in case I forgot a constraint
        $metadata->setGroupSequence(['InstitutionRequest', 'simple', 'class', 'class-complex']);
        $metadata->defaultGroup = 'Default';
    }
}
