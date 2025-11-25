<?php
declare(strict_types=1);

namespace Jobberwocky\Services;

use Jobberwocky\Repositories\IJobRepository;
use Jobberwocky\Models\Job;
use RuntimeException;

class JobService {
  private IJobRepository $repository;

  public function __construct(IJobRepository $repository) {
    $this->repository = $repository;
  }

  public function createJob(array $data) : Job {
    $job = new Job($data);
    $this->repository->save($job);
    return $job;
  }

  public function getAll() : array {
    return $this->repository->getAll();
  }

  public function findById(string $id) : Job {
    $job = $this->repository->findById($id);
    if ($job === null) {
      throw new RuntimeException('Job not found');
    }
    return $job;
  }

  public function searchJobs(array $criteria) : array {
    return $this->repository->searchJobs($criteria);
  }
    
}