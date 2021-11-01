<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\WebApi\Model;

use Symfony\Component\Serializer\Annotation\SerializedName;

final class SimpleCountry
{
    /**
     * @param int $id
     * @param string $countryCode
     * @param string $externalQaaIsPermitted
     * @param string $europeanApproachIsPermitted
     * @param bool $hasFullInstitutionList
     * @param string $eheaKeyCommitment
     * @param int $agencyCount
     * @param string|null $eqarGovernmentalMemberStart
     */
    public function __construct(
        #[SerializedName('id')]
        public int $id,
        #[SerializedName('iso_3166_alpha2')]
        public string $countryCode,
        #[SerializedName('external_QAA_is_permitted')]
        public string $externalQaaIsPermitted,
        #[SerializedName('european_approach_is_permitted')]
        public string $europeanApproachIsPermitted,
        #[SerializedName('has_full_institution_list')]
        public bool $hasFullInstitutionList,
        #[SerializedName('ehea_key_commitment')]
        public string $eheaKeyCommitment,
        #[SerializedName('agency_count')]
        public int $agencyCount,
        #[SerializedName('eqar_governmental_member_start')]
        public ?string $eqarGovernmentalMemberStart = null,
    ) {
    }
}
