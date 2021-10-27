<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\WebApi\Model;

use ITB\DeqarApiClient\WebApi\Model\SimpleAgency\Activity;
use Symfony\Component\Serializer\Annotation\SerializedName;

final class SimpleAgency
{
    public function __construct(
        #[SerializedName('id')]
        public int $id,
        #[SerializedName('deqar_id')]
        public string $deqarId,
        #[SerializedName('name_primary')]
        public string $namePrimary,
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
