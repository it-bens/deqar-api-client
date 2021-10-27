<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\SubmissionApi\Model;

final class SubmitReportResponseError implements SubmitReportResponseInterface
{
    public function __construct(private array $data)
    {
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function successful(): bool
    {
        return false;
    }
}
