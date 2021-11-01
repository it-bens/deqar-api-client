<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\SubmissionApi\Model;

use ITB\DeqarApiClient\SubmissionApi\Model\SubmitReportRequest\FileRequest;
use ITB\DeqarApiClient\SubmissionApi\Model\SubmitReportRequest\InstitutionRequest;
use ITB\DeqarApiClient\SubmissionApi\Model\SubmitReportRequest\LinkRequest;
use ITB\DeqarApiClient\SubmissionApi\Model\SubmitReportRequest\ProgrammeRequest;
use ITB\DeqarApiClient\SubmissionApi\Validation as SubmissionValidation;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

final class SubmitReportRequest
{
    public const REPORT_STATUS_OBLIGATORY = 'part of obligatory EQA system';
    public const REPORT_STATUS_VOLUNTARY = 'voluntary';

    public const REPORT_DECISION_POSITIVE = 'positive';
    public const REPORT_DECISION_POSITIVE_WITH_CONDITIONS = 'positive with conditions or restrictions';
    public const REPORT_DECISION_NEGATIVE = 'negative';
    public const REPORT_DECISION_NOT_APPLICABLE = 'not applicable';

    public const REPORT_DATE_FORMAT = '%Y-%m-%d';

    #[SerializedName('date_format')]
    public string $dateFormat = self::REPORT_DATE_FORMAT;

    /**
     * @param string $agency
     * @param string $activity
     * @param string $status
     * @param string $decision
     * @param string $validFrom
     * @param FileRequest[] $files
     * @param InstitutionRequest[] $institutions
     * @param ProgrammeRequest[] $programmes
     * @param string|null $id
     * @param string[]|null $contributingAgencies
     * @param string|null $localIdentifier
     * @param string|null $activityLocalIdentifier
     * @param string|null $summary
     * @param string|null $validTo
     * @param LinkRequest[]|null $links
     * @param string|null $comment
     */
    public function __construct(
        #[SerializedName('agency')]
        public string $agency,
        #[SerializedName('activity')]
        public string $activity,
        #[SerializedName('status')]
        public string $status,
        #[SerializedName('decision')]
        public string $decision,
        #[SerializedName('valid_from')]
        public string $validFrom,
        #[SerializedName('report_files')]
        /** @var FileRequest[] */
        public array $files,
        #[SerializedName('institutions')]
        /** @var InstitutionRequest[] */
        public array $institutions,
        #[SerializedName('programmes')]
        /** @var ProgrammeRequest[] */
        public array $programmes,
        #[SerializedName('report_id')]
        public ?string $id = null,
        #[SerializedName('contributing_agencies')]
        public ?array $contributingAgencies = null,
        #[SerializedName('local_identifier')]
        public ?string $localIdentifier = null,
        #[SerializedName('activity_local_identifier')]
        public ?string $activityLocalIdentifier = null,
        #[SerializedName('summary')]
        public ?string $summary = null,
        #[SerializedName('valid_to')]
        public ?string $validTo = null,
        #[SerializedName('report_links')]
        /** @var LinkRequest[] */
        public ?array $links = null,
        #[SerializedName('other_comment')]
        public ?string $comment = null,
    ) {
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint(
            'agency',
            new Assert\Sequentially([
                'constraints' => [
                    new Assert\NotBlank(allowNull: false, groups: ['simple']),
                    new SubmissionValidation\ExistingAgency(['groups' => ['complex']])],
                'groups' => ['simple', 'complex']
            ])
        );
        $metadata->addPropertyConstraint(
            'activity',
            new Assert\Sequentially([
                'constraints' => [
                    new Assert\NotBlank(allowNull: false, groups: ['simple']),
                    new SubmissionValidation\ExistingActivity(['groups' => ['complex']])],
                'groups' => ['simple', 'complex']
            ])
        );
        $metadata->addPropertyConstraint(
            'status',
            new Assert\Choice([self::REPORT_STATUS_OBLIGATORY, self::REPORT_STATUS_VOLUNTARY], groups: ['simple'])
        );
        $metadata->addPropertyConstraint(
            'decision',
            new Assert\Choice([
                self::REPORT_DECISION_POSITIVE,
                self::REPORT_DECISION_POSITIVE_WITH_CONDITIONS,
                self::REPORT_DECISION_NEGATIVE,
                self::REPORT_DECISION_NOT_APPLICABLE
            ], groups: ['simple'])
        );
        $metadata->addPropertyConstraint('validFrom', new Assert\Date(groups: ['simple']));
        $metadata->addPropertyConstraint(
            'files',
            new Assert\Sequentially([
                'constraints' => [
                    new Assert\Count(min: 1, groups: ['simple']),
                    new Assert\All(['constraints' => [new Assert\Type(FileRequest::class)], 'groups' => ['simple']])
                ],
                'groups' => ['simple']
            ])
        );
        $metadata->addPropertyConstraint('files', new Assert\Valid(groups: ['sub']));
        $metadata->addPropertyConstraint(
            'institutions',
            new Assert\Sequentially([
                'constraints' => [new Assert\All(['constraints' => [new Assert\Type(InstitutionRequest::class)], 'groups' => ['simple']])],
                'groups' => ['simple']
            ])
        );
        $metadata->addPropertyConstraint('institutions', new Assert\Valid(groups: ['sub']));
        $metadata->addPropertyConstraint(
            'programmes',
            new Assert\Sequentially([
                'constraints' => [new Assert\All(['constraints' => [new Assert\Type(ProgrammeRequest::class)], 'groups' => ['simple']])],
                'groups' => ['simple']
            ])
        );
        $metadata->addPropertyConstraint('programmes', new Assert\Valid(groups: ['sub']));
        $metadata->addPropertyConstraint('id', new Assert\NotBlank(allowNull: true, groups: ['simple']));
        $metadata->addPropertyConstraint(
            'contributingAgencies',
            new Assert\Sequentially([
                'constraints' => [
                    new Assert\Count(min: 1, groups: ['simple']),
                    new Assert\All([
                        'constraints' => [
                            new Assert\NotBlank(allowNull: false, groups: ['simple']),
                            new SubmissionValidation\ExistingAgency(['groups' => ['complex']])
                        ],
                        'groups' => ['simple', 'complex']
                    ])
                ],
                'groups' => ['simple', 'complex']
            ])
        );
        $metadata->addPropertyConstraint('localIdentifier', new Assert\Length(min: 1, max: 255, groups: ['simple']));
        $metadata->addPropertyConstraint('activityLocalIdentifier', new Assert\Length(min: 1, max: 200, groups: ['simple']));
        $metadata->addPropertyConstraint('summary', new Assert\NotBlank(allowNull: true, groups: ['simple']));
        $metadata->addPropertyConstraint('validTo', new Assert\Date(groups: ['simple']));
        $metadata->addPropertyConstraint(
            'links',
            new Assert\Sequentially([
                'constraints' => [
                    new Assert\Count(min: 1, groups: ['simple']),
                    new Assert\All(['constraints' => [new Assert\Type(LinkRequest::class)], 'groups' => ['simple']])
                ],
                'groups' => ['simple']
            ])
        );
        $metadata->addPropertyConstraint('links', new Assert\Valid(groups: ['sub']));
        $metadata->addPropertyConstraint('comment', new Assert\NotBlank(allowNull: true, groups: ['simple']));

        $metadata->addConstraint(new SubmissionValidation\ValidActivityInstitutionProgrammeCombination(['groups' => ['Class']]));

        // Class name is added in case I forgot a constraint
        $metadata->setGroupSequence(['SubmitReportRequest', 'simple', 'complex', 'complex', 'sub']);
        $metadata->defaultGroup = 'Default';
    }
}
