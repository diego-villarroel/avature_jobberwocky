<?php
declare(strict_types=1);

namespace Jobberwocky\Adapters;

use Jobberwocky\Models\Job;
use InvalidArgumentException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ExternalJobSourceAdapter {
  private Client $httpClient;
  private string $baseUrl;

  public function __construct(string $baseUrl = 'http://external-source:8080') {
    $this->baseUrl = rtrim($baseUrl, '/');
    $this->httpClient = new Client([
        'timeout' => 10,
        'connect_timeout' => 5
    ]);
  }

  public function fetchJobs(array $criteria = []): array {
    try {
      $queryParams = $this->buildQueryParams($criteria);
      $url = $this->baseUrl . '/jobs' . ($queryParams ? '?' . $queryParams : '');
      
      $response = $this->httpClient->get($url, ['debug' => true, 'http_errors' => false]);
      $data = json_decode($response->getBody()->getContents(), true);

      return $this->normalizeData($data);
    } catch (GuzzleException $e) {
      error_log("Error fetching from external source: {$e->getMessage()}");
      return [];
    }
  }

  private function buildQueryParams(array $criteria): string {
    $params = [];

    if (isset($criteria['pattern']) && !empty($criteria['pattern'])) {
      $params['name'] = $criteria['pattern'];
    }

    if (isset($criteria['salary_min'])) {
      $params['salary_min'] = $criteria['salary_min'];
    }

    if (isset($criteria['salary_max'])) {
      $params['salary_max'] = $criteria['salary_max'];
    }

    if (isset($criteria['country'])) {
      $params['country'] = $criteria['country'];
    }

    return http_build_query($params);
  }

  public function parseSkillsXML($xml) : array {
    $this->validates($xml);
    $xmlObject = simplexml_load_string($xml);
    $skills = [];
    
    foreach ($xmlObject->skill as $skill) {
      $skills[] = trim((string)$skill);
    }
    
    return $skills;
  }

  public function normalizeData(array $externalData) : array {
    $jobs = [];

    foreach ($externalData as $country => $jobsData) {
      foreach ($jobsData as $data) {
        try {
          $job = $this->transformJob($data, $country);
          
          if ($job !== null) {
            $jobs[] = $job;
          }
        } catch (\Exception $e) {
          error_log("Skipping invalid job: {$e->getMessage()}");
          continue;
        }
      }
    }
    
    return $jobs;
  }

  private function validates($xml){
    if ($xml === null || $xml === '') {
      throw new InvalidArgumentException('XML string cannot be null or empty');
    }
  }

  private function transformJob(array $job, string $country) : ?Job {
    if (count($job) < 3) {
      return null;
    }

    [$name, $salary, $skillsXml] = $job;  

    if (empty($name)) {
      return null;
    }

    return new Job([
      'title' => $name,
      'company' => 'Confidential',
      'location' => $country,
      'salary' => strval($salary),
      'skills' => $this->parseSkillsXML($skillsXml)
    ]);
  }
}