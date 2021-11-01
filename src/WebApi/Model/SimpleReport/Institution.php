<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\WebApi\Model\SimpleReport;

use Symfony\Component\Serializer\Annotation\SerializedName;

final class Institution
{
    /**
     * @param int $id
     * @param string $deqarId
     * @param string $namePrimary
     */
    public function __construct(
        #[SerializedName('id')]
        public int $id,
        #[SerializedName('deqar_id')]
        public string $deqarId,
        #[SerializedName('name_primary')]
        public string $namePrimary
    ) {
    }
}
