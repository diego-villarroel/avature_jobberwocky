<?php
declare(strict_types=1);

namespace Jobberwocky\Repositories;

use Jobberwocky\Models\Job;

interface IJobRepository {
  public function save(Job $job) : void;
  public function getAll() : array;
  public function findById(string $id) : ?Job;
  public function searchJobs(array $criteria) : array;
}