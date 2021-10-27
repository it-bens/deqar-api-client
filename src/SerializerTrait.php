<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient;

use ITB\DeqarApiClient\SubmissionApi\Model\SubmitReportRequest;
use JsonException;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

trait SerializerTrait
{
    private static function buildSerializer(): Serializer
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader());
        $metadataAwareNameConverter = new MetadataAwareNameConverter($classMetadataFactory);

        $arrayNormalizer = new ArrayDenormalizer();
        $propertyNormalizer = new PropertyNormalizer($classMetadataFactory, $metadataAwareNameConverter, propertyTypeExtractor: new PhpDocExtractor());
        $dateTimeNormalizer = new DateTimeNormalizer([DateTimeNormalizer::FORMAT_KEY => str_replace('%', '', SubmitReportRequest::REPORT_DATE_FORMAT)]);

        return new Serializer([$arrayNormalizer, $propertyNormalizer, $dateTimeNormalizer], [new JsonEncoder()]);
    }

    private function deserializeFromArray(array $data, string $type, SerializerInterface $serializer): mixed
    {
        try {
            $encodedData = json_encode($data, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            // TODO: throw exception
        }

        return $serializer->deserialize($encodedData, $type, 'json');
    }

    private function serializeToArray(object $data, SerializerInterface $serializer, array $context = []): array
    {
        $serializedData = $serializer->serialize($data, 'json', $context);

        try {
            return json_decode($serializedData, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            // TODO: throw exception
        }
    }
}
