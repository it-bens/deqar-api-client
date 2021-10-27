<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\SubmissionApi\Model;

final class DeleteReportResponseError implements DeleteReportResponseInterface
{
    public function __construct(private string $reportId)
    {
    }

    public function getReportId(): string
    {
        return $this->reportId;
    }

    public function successful(): bool
    {
        return false;
    }
}
