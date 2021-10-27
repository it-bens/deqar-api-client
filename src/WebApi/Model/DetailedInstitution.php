<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\WebApi\Model;

use ITB\DeqarApiClient\WebApi\Model\DetailedInstitution\Identifier;
use ITB\DeqarApiClient\WebApi\Model\DetailedInstitution\Name;
use ITB\DeqarApiClient\WebApi\Model\DetailedInstitution\QfEheaLevel;
use Symfony\Component\Serializer\Annotation\SerializedName;

final class DetailedInstitution
{
    public function __construct(
        #[SerializedName('id')]
        public int $id,
        #[SerializedName('identifiers')]
        /** @var Identifier[] */ public array $identifiers,
        #[SerializedName('website_link')]
        public string $website,
        #[SerializedName('names')]
        /** @var Name[] */ public array $names,
        #[SerializedName('countries')]
        public array $countries,
        #[SerializedName('qf_ehea_levels')]
        /** @var QfEheaLevel[] */ public array $qfEheaLevels,
        #[SerializedName('eter')]
        public ?string $eter = null,
        #[SerializedName('founding_date')]
        public ?string $foundation = null,
    ) {
    }
}
