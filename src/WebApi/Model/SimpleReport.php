<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\WebApi\Model;

use ITB\DeqarApiClient\WebApi\Model\SimpleReport\Institution;
use ITB\DeqarApiClient\WebApi\Model\SimpleReport\Programme;
use ITB\DeqarApiClient\WebApi\Model\SimpleReport\ReportFile;
use ITB\DeqarApiClient\WebApi\Model\SimpleReport\ReportLink;
use Symfony\Component\Serializer\Annotation\SerializedName;

final class SimpleReport
{
    /**
     * @param int $id
     * @param string $agencyName
     * @param string $agencyAcronym
     * @param string $agencyEsgActivity
     * @param string $agencyEsgActivityType
     * @param Institution[] $institutions
     * @param Programme[] $programmes
     * @param string $validFrom
     * @param string $status
     * @param string $decision
     * @param ReportFile[] $reportFiles
     * @param ReportLink[] $reportLinks
     * @param string $created
     * @param string $updated
     * @param string $flagLevel
     * @param string[] $localIds
     * @param string|null $validTo
     * @param string[] $countries
     */
    public function __construct(
        #[SerializedName('id')]
        public int $id,
        #[SerializedName('agency_name')]
        public string $agencyName,
        #[SerializedName('agency_acronym')]
        public string $agencyAcronym,
        #[SerializedName('agency_esg_activity')]
        public string $agencyEsgActivity,
        #[SerializedName('agency_esg_activity_type')]
        public string $agencyEsgActivityType,
        #[SerializedName('institutions')]
        public array $institutions,
        #[SerializedName('programmes')]
        public array $programmes,
        #[SerializedName('valid_from')]
        public string $validFrom,
        #[SerializedName('status')]
        public string $status,
        #[SerializedName('decision')]
        public string $decision,
        #[SerializedName('report_files')]
        public array $reportFiles,
        #[SerializedName('report_links')]
        public array $reportLinks,
        #[SerializedName('date_created')]
        public string $created,
        #[SerializedName('date_updated')]
        public string $updated,
        #[SerializedName('flag_level')]
        public string $flagLevel,
        #[SerializedName('local_id')]
        public array $localIds = [],
        #[SerializedName('valid_to')]
        public ?string $validTo = null,
        #[SerializedName('country')]
        public array $countries = [],
    ) {
    }
}
