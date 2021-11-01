<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\WebApi\Model\DetailedInstitution;

use Symfony\Component\Serializer\Annotation\SerializedName;

final class Country
{
    /**
     * @param int $id
     * @param string $city
     * @param string $lat
     * @param string $long
     * @param string $countrySource
     * @param string $validFrom
     * @param bool $verified
     * @param string|null $validTo
     */
    public function __construct(
        #[SerializedName('country.id')]
        public int $id,
        #[SerializedName('city')]
        public string $city,
        #[SerializedName('lat')]
        public string $lat,
        #[SerializedName('long')]
        public string $long,
        #[SerializedName('country_source')]
        public string $countrySource,
        #[SerializedName('country_valid_from')]
        public string $validFrom,
        #[SerializedName('country_verified')]
        public bool $verified,
        #[SerializedName('country_valid_to')]
        public ?string $validTo = null,
    ) {
    }
}
