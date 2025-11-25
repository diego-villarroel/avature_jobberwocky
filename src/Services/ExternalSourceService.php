<?php
declare(strict_types=1);

namespace Jobberwocky\Services;

use Jobberwocky\Adapters\ExternalJobSourceAdapter;
use Jobberwocky\Services\JobService;

class ExternalSourceService {
  
  public function __construct(
    private JobService $jobService,
    private ExternalJobSourceAdapter $externalAdapter
  ) {}

  public function searchAllSources(array $criteria, bool $includeExternal = false) : array {
    $results = !empty($criteria) ? $this->jobService->searchJobs($criteria) : $this->jobService->getAll();

    if ($includeExternal) {
      $externalJobs = $this->externalAdapter->fetchJobs($criteria);
      $results = array_merge($results, $externalJobs);
    }
    return $results;
  }
}