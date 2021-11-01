<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\WebApi\Model\DetailedInstitution;

use Symfony\Component\Serializer\Annotation\SerializedName;

final class Name
{
    /**
     * @param string $official
     * @param string $officalTransliterated
     * @param string $english
     * @param string $acronym
     * @param string $sourceNote
     * @param string|null $validTo
     */
    public function __construct(
        #[SerializedName('name_official')]
        public string $official,
        #[SerializedName('name_official_transliterated')]
        public string $officalTransliterated,
        #[SerializedName('name_english')]
        public string $english,
        #[SerializedName('acronym')]
        public string $acronym,
        #[SerializedName('name_source_note')]
        public string $sourceNote,
        #[SerializedName('identifier_valid_to')]
        public ?string $validTo = null,
    ) {
    }
}
