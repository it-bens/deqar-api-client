<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient;

use ITB\DeqarApiClient\SubmissionApi\Validation\ExistingActivityValidator;
use ITB\DeqarApiClient\SubmissionApi\Validation\ExistingAgencyValidator;
use ITB\DeqarApiClient\SubmissionApi\Validation\ExistingInstitutionValidator;
use ITB\DeqarApiClient\SubmissionApi\Validation\ValidActivityInstitutionProgrammeCombinationValidator;
use ITB\DeqarApiClient\SubmissionApi\Validation\WebFilePdfValidator;
use ITB\DeqarApiClient\WebApi\WebApiClientInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\ContainerConstraintValidatorFactory;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

trait ValidatorTrait
{
    private static function buildValidator(
        HttpClientInterface $httpClient,
        WebApiClientInterface $webApiClient
    ): ValidatorInterface {
        $container = new ValidatorContainer();
        $container->add(new ExistingActivityValidator($webApiClient));
        $container->add(new ExistingAgencyValidator($webApiClient));
        $container->add(new ExistingInstitutionValidator($webApiClient));
        $container->add(new ValidActivityInstitutionProgrammeCombinationValidator($webApiClient));
        $container->add(new WebFilePdfValidator($httpClient));

        // The internal annotation loader also loads attributes.
        return Validation::createValidatorBuilder()
            ->addMethodMapping('loadValidatorMetadata')
            ->setConstraintValidatorFactory(new ContainerConstraintValidatorFactory($container))
            ->getValidator();
    }
}

final class ValidatorContainer implements ContainerInterface
{

    /** @var array<string, ConstraintValidatorInterface> $services */
    private array $services = [];

    public function __construct()
    {
    }

    public function add(ConstraintValidatorInterface $service): void
    {
        $this->services[get_class($service)] = $service;
    }

    public function get(string $id): ConstraintValidatorInterface
    {
        // TODO: add exception if key does not exist
        return $this->services[$id];
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->services);
    }
}
