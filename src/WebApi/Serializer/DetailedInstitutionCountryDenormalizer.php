<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\WebApi\Serializer;

use ITB\DeqarApiClient\WebApi\Model\DetailedInstitution\Country;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class DetailedInstitutionCountryDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface, CacheableSupportsMethodInterface
{
    use DenormalizerAwareTrait;

    /**
     * @phpstan-ignore-next-line
     *
     * @param mixed $data
     * @param string $type
     * @param string|null $format
     * @param array $context
     * @return mixed
     * @throws ExceptionInterface
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        // The id is extracted from the detailed country data and added 'plain' to the top array.
        $detailedCountryData = $data['country'];
        $data['country.id'] = $detailedCountryData['id'];

        // The detailed country data is removed as a flag if this denormalizer was already called.
        unset($data['country']);

        return $this->denormalizer->denormalize($data, $type, $format, $context);
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return false;
    }

    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        if (Country::class !== $type) {
            return false;
        }
        if (!isset($data['country'])) {
            return false;
        }

        return true;
    }
}
