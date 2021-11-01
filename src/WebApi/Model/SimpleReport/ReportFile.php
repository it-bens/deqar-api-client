<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\WebApi\Model\SimpleReport;

use Symfony\Component\Serializer\Annotation\SerializedName;

final class ReportFile
{
    /**
     * @param int $file
     * @param string $displayName
     * @param string[] $languages
     */
    public function __construct(
        #[SerializedName('file')]
        public int $file,
        #[SerializedName('file_display_name')]
        public string $displayName,
        #[SerializedName('languages')]
        public array $languages
    ) {
    }
}
