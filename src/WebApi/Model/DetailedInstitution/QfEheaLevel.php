<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\WebApi\Model\DetailedInstitution;

use Symfony\Component\Serializer\Annotation\SerializedName;

final class QfEheaLevel
{
    public function __construct(
        #[SerializedName('qf_ehea_level')]
        public string $level,
        #[SerializedName('qf_ehea_level_source')]
        public string $source,
        #[SerializedName('qf_ehea_level_source_note')]
        public string $sourceNote,
        #[SerializedName('identifier_valid_from')]
        public ?string $validFrom = null,
        #[SerializedName('identifier_valid_to')]
        public ?string $validTo = null,
    ) {
    }
}
