<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\SubmissionApi\Model;

interface SubmitReportResponseInterface
{
    /**
     * @phpstan-ignore-next-line
     * @return array
     */
    public function getData(): array;

    public function successful(): bool;
}
