<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\WebApi\Model\DetailedInstitution;

use Symfony\Component\Serializer\Annotation\SerializedName;

final class Identifier
{
    /**
     * @param string $id
     * @param string $resource
     * @param string $validFrom
     * @param string|null $agency
     * @param string|null $validTo
     */
    public function __construct(
        #[SerializedName('identifier')]
        public string $id,
        #[SerializedName('resource')]
        public string $resource,
        #[SerializedName('identifier_valid_from')]
        public string $validFrom,
        #[SerializedName('agency')]
        public ?string $agency = null,
        #[SerializedName('identifier_valid_to')]
        public ?string $validTo = null,
    ) {
    }
}
