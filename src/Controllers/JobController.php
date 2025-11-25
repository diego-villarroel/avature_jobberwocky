<?php
declare(strict_types=1);

namespace Jobberwocky\Controllers;

use Jobberwocky\Services\JobService;
use Jobberwocky\Services\ExternalSourceService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Jobberwocky\Utils\Utils;

class JobController {
  public function __construct(
    private JobService $jobService,
    private ExternalSourceService $externalSource,
    private Utils $utils = new Utils()
  ) {}

  public function getAll(Request $request, Response $response) : Response{
    try {
      $jobs = $this->jobService->getAll();
      $data = [];
      foreach ($jobs as $job) {
        $data[] = $this->utils->objToArray($job);
      }
      $response->getBody()->write(json_encode([
        'success' => true,
        'data' => $data,
        'count' => count($jobs)
      ]));

      return $response->withHeader('Content-Type', 'application/json');

    } catch (\Exception $e) {
      $response->getBody()->write(json_encode([
        'success' => false,
        'error' => 'Internal server error'
      ]));

      return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(500);
    }
  }

  public function createJob(Request $request, Response $response) : Response {
    $data = $request->getParsedBody();
    $job = $this->jobService->createJob($data);

    $response->getBody()->write(json_encode([
      'success' => true,
      'data' => $this->utils->objToArray($job)
    ]));

    return $response
      ->withHeader('Content-Type', 'application/json')
      ->withStatus(201);
  }

  public function findById(Request $request, Response $response, array $args) : Response {
    try {
      $job = $this->jobService->findById($args['id']);
      $response->getBody()->write(json_encode([
        'success' => true,
        'data' => $this->utils->objToArray($job)
      ]));

      return $response->withHeader('Content-Type', 'application/json');
    } catch (\Exception $e) {
      $response->getBody()->write(json_encode([
        'success' => false,
        'error' => 'Internal server error'
      ]));

      return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(500);
    }
  }

  public function searchJobs(Request $request, Response $response) : Response {
    try {
      var_dump($request->getQueryParams());
      $jobs = $this->jobService->searchJobs($request->getQueryParams());
      $data = [];
      foreach ($jobs as $job) {
        $data[] = $this->utils->objToArray($job);
      }
      $response->getBody()->write(json_encode([
        'success' => true,
        'data' => $data,
        'count' => count($jobs)
      ]));

      return $response->withHeader('Content-Type', 'application/json');
    } catch (\Exception $e) {
      $response->getBody()->write(json_encode([
        'success' => false,
        'error' => 'Internal server error'
      ]));

      return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(500);
    }
  }

}