<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\SubmissionApi\Model;

interface SubmitReportResponseInterface
{
    public function getData(): array;

    public function successful(): bool;
}
