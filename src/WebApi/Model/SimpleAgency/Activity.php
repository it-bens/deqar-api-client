<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\WebApi\Model\SimpleAgency;

use Symfony\Component\Serializer\Annotation\SerializedName;

final class Activity
{
    /**
     * @param int $id
     * @param string $activity
     * @param string $description
     * @param string $type
     * @param string|null $reportsLink
     * @param string|null $validTo
     */
    public function __construct(
        #[SerializedName('id')]
        public int $id,
        #[SerializedName('activity')]
        public string $activity,
        #[SerializedName('activity_description')]
        public string $description,
        #[SerializedName('activity_type')]
        public string $type,
        #[SerializedName('reports_link')]
        public ?string $reportsLink = null,
        #[SerializedName('activity_valid_to')]
        public ?string $validTo = null,
    ) {
    }
}
