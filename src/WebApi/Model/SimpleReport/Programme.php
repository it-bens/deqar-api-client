<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\WebApi\Model\SimpleReport;

use Symfony\Component\Serializer\Annotation\SerializedName;

final class Programme
{
    /**
     * @param int $id
     * @param string $namePrimary
     * @param string $nqfLevel
     * @param string $qfEheaLevel
     */
    public function __construct(
        #[SerializedName('id')]
        public int $id,
        #[SerializedName('name_primary')]
        public string $namePrimary,
        #[SerializedName('nqf_level')]
        public string $nqfLevel,
        #[SerializedName('qf_ehea_level')]
        public string $qfEheaLevel
    ) {
    }
}
