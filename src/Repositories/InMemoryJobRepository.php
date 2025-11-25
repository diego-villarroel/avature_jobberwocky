<?php
declare(strict_types=1);

namespace Jobberwocky\Repositories;

use Jobberwocky\Models\Job;

class InMemoryJobRepository implements IJobRepository {
  private array $jobs = [];

  public function save(Job $job) : void {
    $this->jobs[$job->getId()] = $job;
  }

  public function clear() : void {
    $this->jobs = [];
  }

  public function getAll() : array {
    return array_values($this->jobs);
  }

  public function findById(string $id) : ?Job {
    return $this->jobs[$id] ?? null;
  }

  public function searchJobs(array $criteria) : array {
    $results = $this->getAll();

    if (empty($criteria)) {
      return $results;
    }
    
    $results = array_filter($results, function(Job $job) use ($criteria) {
      foreach ($criteria as $value) {
        if (!$this->matchesPattern($value, $job)) {
          return false;
        }
      }      
      return true;
    });
    
    return array_values($results);
  }

  public function matchesPattern(string $pattern, Job $job) : bool {
    if (empty($pattern)) {
      return true;
    }
    
    $pattern = strtolower($pattern);
    $searchableText = strtolower(implode(' ', [
      $job->getTitle(),
      $job->getSalary(),
      $job->getCompany(),
      $job->getDescription(),
      $job->getLocation(),
      implode(' ', $job->getSkills())
    ]));

    return str_contains($searchableText, $pattern);
  }
}