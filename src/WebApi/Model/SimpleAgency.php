<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\WebApi\Model;

use ITB\DeqarApiClient\WebApi\Model\SimpleAgency\Activity;
use Symfony\Component\Serializer\Annotation\SerializedName;

final class SimpleAgency
{
    /**
     * @param int $id
     * @param string $deqarId
     * @param string $namePrimary
     * @param string $acronym
     * @param Activity[] $activities
     * @param string $registrationStart
     * @param string $registrationValidTo
     * @param string $registrationNote
     * @param int $institutionCount
     * @param int $reportCount
     * @param string|null $nameOfficialDisplay
     * @param string|null $logoLink
     */
    public function __construct(
        #[SerializedName('id')]
        public int $id,
        #[SerializedName('deqar_id')]
        public string $deqarId,
        #[SerializedName('name_primary')]
        public string $namePrimary,
        #[SerializedName('acronym_primary')]
        public string $acronym,
        #[SerializedName('activities')]
        /** @var Activity[] */ public array $activities,
        #[SerializedName('registration_start')]
        public string $registrationStart,
        #[SerializedName('registration_valid_to')]
        public string $registrationValidTo,
        #[SerializedName('registration_note')]
        public string $registrationNote,
        #[SerializedName('institution_count')]
        public int $institutionCount,
        #[SerializedName('report_count')]
        public int $reportCount,
        #[SerializedName('name_official_display')]
        public ?string $nameOfficialDisplay = null,
        #[SerializedName('logo')]
        public ?string $logoLink = null,
    ) {
    }
}
