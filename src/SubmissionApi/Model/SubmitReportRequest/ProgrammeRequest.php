<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\SubmissionApi\Model\SubmitReportRequest;

use ITB\DeqarApiClient\SubmissionApi\Validation as SubmissionValidation;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

final class ProgrammeRequest
{
    public const PROGRAMME_QF_EHEA_LEVEL_SHORT_CYCLE = 'short cycle';
    public const PROGRAMME_QF_EHEA_LEVEL_FIRST_CYCLE = 'first cycle';
    public const PROGRAMME_QF_EHEA_LEVEL_SECOND_CYCLE = 'second cycle';
    public const PROGRAMME_QF_EHEA_LEVEL_THIRD_CYCLE = 'third cycle';

    /**
     * @param string $namePrimary
     * @param ProgrammeIdentifierRequest[]|null $identifiers
     * @param string|null $qualificationPrimary
     * @param ProgrammeNameRequest[]|null $alternativeNames
     * @param string[]|null $countries
     * @param string|null $nqfLevel
     * @param string|null $qfEheaLevel
     */
    public function __construct(
        #[SerializedName('name_primary')]
        public string $namePrimary,
        #[SerializedName('identifiers')]
        /** @var ProgrammeIdentifierRequest[] */
        public ?array $identifiers = null,
        #[SerializedName('qualification_primary')]
        public ?string $qualificationPrimary = null,
        #[SerializedName('alternative_names')]
        /** @var ProgrammeNameRequest[] */
        public ?array $alternativeNames = null,
        #[SerializedName('countries')]
        public ?array $countries = null,
        #[SerializedName('nqf_level')]
        public ?string $nqfLevel = null,
        #[SerializedName('qf_ehea_level')]
        public ?string $qfEheaLevel = null,
    ) {
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint('namePrimary', new Assert\Length(min: 1, max: 255, groups: ['simple']));
        $metadata->addPropertyConstraint(
            'identifiers',
            new Assert\Sequentially([
                'constraints' => [
                    new Assert\Count(min: 1, groups: ['simple']),
                    new Assert\All(['constraints' => [new Assert\Type(ProgrammeIdentifierRequest::class)], 'groups' => ['simple']])
                ],
                'groups' => ['simple']
            ])
        );
        $metadata->addPropertyConstraint('identifiers', new Assert\Valid(groups: ['sub']));
        $metadata->addPropertyConstraint('qualificationPrimary', new Assert\Length(min: 1, max: 255, groups: ['simple']));
        $metadata->addPropertyConstraint(
            'alternativeNames',
            new Assert\Sequentially([
                'constraints' => [
                    new Assert\Count(min: 1, groups: ['simple']),
                    new Assert\All(['constraints' => [new Assert\Type(ProgrammeNameRequest::class)], 'groups' => ['simple']])
                ],
                'groups' => ['simple']
            ])
        );
        $metadata->addPropertyConstraint('alternativeNames', new Assert\Valid(groups: ['sub']));
        $metadata->addPropertyConstraint(
            'countries',
            new Assert\Sequentially([
                'constraints' => [
                    new Assert\Count(min: 1, groups: ['simple']),
                    new Assert\All(['constraints' => [new Assert\Country(alpha3: true)], 'groups' => ['simple']])
                ],
                'groups' => ['simple']
            ])
        );
        $metadata->addPropertyConstraint('nqfLevel', new Assert\Length(min: 1, max: 255, groups: ['simple']));
        $metadata->addPropertyConstraint(
            'qfEheaLevel',
            new Assert\Choice([
                self::PROGRAMME_QF_EHEA_LEVEL_SHORT_CYCLE,
                self::PROGRAMME_QF_EHEA_LEVEL_FIRST_CYCLE,
                self::PROGRAMME_QF_EHEA_LEVEL_SECOND_CYCLE,
                self::PROGRAMME_QF_EHEA_LEVEL_THIRD_CYCLE
            ], groups: ['simple'])
        );

        $metadata->addConstraint(new SubmissionValidation\ProgrammeRequestHasOneLevel(['groups' => ['class']]));

        // Class name is added in case I forgot a constraint
        $metadata->setGroupSequence(['ProgrammeRequest', 'simple', 'class', 'sub']);
        $metadata->defaultGroup = 'Default';
    }
}
