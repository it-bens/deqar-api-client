<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\WebApi\Model\SimpleReport;

use Symfony\Component\Serializer\Annotation\SerializedName;

final class ReportLink
{
    /**
     * @param int $link
     * @param string $displayName
     */
    public function __construct(
        #[SerializedName('link')]
        public int $link,
        #[SerializedName('link_display_name')]
        public string $displayName
    ) {
    }
}
