<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\SubmissionApi\Model;

interface DeleteReportResponseInterface
{
    public function getReportId(): string;

    public function successful(): bool;
}
