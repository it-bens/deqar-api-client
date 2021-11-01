<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\WebApi\Model;

use Symfony\Component\Serializer\Annotation\SerializedName;

final class SimpleInstitution
{
    /**
     * @param int $id
     * @param string $deqarId
     * @param string $namePrimary
     * @param string $nameSelectDisplay
     * @param string $nameOfficialDisplay
     * @param string|null $eterId
     * @param string|null $website
     * @param string[] $countries
     * @param string[] $qfEheaLevel
     */
    public function __construct(
        #[SerializedName('id')]
        public int $id,
        #[SerializedName('deqar_id')]
        public string $deqarId,
        #[SerializedName('name_primary')]
        public string $namePrimary,
        #[SerializedName('name_select_display')]
        public string $nameSelectDisplay,
        #[SerializedName('name_official_display')]
        public string $nameOfficialDisplay,
        #[SerializedName('eter_id')]
        public ?string $eterId = null,
        #[SerializedName('website_link')]
        public ?string $website = null,
        #[SerializedName('country')]
        public array $countries = [],
        #[SerializedName('qf_ehea_level')]
        public array $qfEheaLevel = [],
    ) {
    }
}
