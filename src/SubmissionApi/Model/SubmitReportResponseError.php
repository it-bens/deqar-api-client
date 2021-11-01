<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\SubmissionApi\Model;

final class SubmitReportResponseError implements SubmitReportResponseInterface
{
    /**
     * @phpstan-ignore-next-line
     * @param array $data
     */
    public function __construct(private array $data)
    {
    }

    /**
     * @phpstan-ignore-next-line
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    public function successful(): bool
    {
        return false;
    }
}
