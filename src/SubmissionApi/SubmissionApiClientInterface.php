<?php

declare(strict_types=1);

namespace ITB\DeqarApiClient\SubmissionApi;

use ITB\DeqarApiClient\SubmissionApi\Model\SubmitReportRequest;
use ITB\DeqarApiClient\SubmissionApi\Model\SubmitReportResponseInterface;

interface SubmissionApiClientInterface
{
    public function deleteReport(string $id);

    public function submitReport(SubmitReportRequest $request): SubmitReportResponseInterface;
}
