<?php

declare(strict_types=1);

use Jobberwocky\Adapters\ExternalJobSourceAdapter;
// use Jobberwocky\Controllers\AlertController;
use Jobberwocky\Controllers\JobController;
use Jobberwocky\Repositories\SQLiteJobRepository;
// use Jobberwocky\Services\AlertService;
use Jobberwocky\Services\ExternalSourceService;
use Jobberwocky\Services\JobService;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// Crear directorio de base de datos si no existe
if (!is_dir(__DIR__ . '/../database')) {
    mkdir(__DIR__ . '/../database', 0755, true);
}

// Dependency Injection Container
$repository = new SQLiteJobRepository(__DIR__ . '/../database/jobs.sqlite');
$jobService = new JobService($repository);
$externalAdapter = new ExternalJobSourceAdapter('http://localhost:8081');
$externalSource = new ExternalSourceService($jobService, $externalAdapter);
// $alertService = new AlertService();

$jobController = new JobController(
  $jobService, 
  $externalSource, 
  // $alertService
);
// $alertController = new AlertController($alertService);

// Crear aplicación Slim
$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// ============================================
// ROUTES - Job Endpoints
// ============================================

// POST /api/v1/jobs - Create a new job
$app->post('/api/v1/jobs', function ($request, $response) use ($jobController) {
    return $jobController->createJob($request, $response);
});

// GET /api/v1/jobs - Get all jobs
$app->get('/api/v1/jobs', function ($request, $response) use ($jobController) {
    return $jobController->getAll($request, $response);
});

// GET /api/v1/jobs/search - Search jobs (internal + external)
$app->get('/api/v1/jobs/search', function ($request, $response) use ($jobController) {
    return $jobController->searchJobs($request, $response);
});

// GET /api/v1/jobs/{id} - Get job by ID
$app->get('/api/v1/jobs/{id}', function ($request, $response, $args) use ($jobController) {
    return $jobController->findById($request, $response, $args);
});

// DELETE /api/v1/jobs/{id} - Delete job
// $app->delete('/api/v1/jobs/{id}', function ($request, $response, $args) use ($jobController) {
//     return $jobController->delete($request, $response, $args);
// });

// ============================================
// ROUTES - Alert Endpoints
// ============================================

// POST /api/v1/alerts/subscribe - Subscribe to job alerts
// $app->post('/api/v1/alerts/subscribe', function ($request, $response) use ($alertController) {
//     return $alertController->subscribe($request, $response);
// });

// DELETE /api/v1/alerts/{id} - Unsubscribe from alerts
// $app->delete('/api/v1/alerts/{id}', function ($request, $response, $args) use ($alertController) {
//     return $alertController->unsubscribe($request, $response, $args);
// });

// GET /api/v1/alerts - Get all alerts
// $app->get('/api/v1/alerts', function ($request, $response) use ($alertController) {
//     return $alertController->getAll($request, $response);
// });

// ============================================
// Health Check
// ============================================

$app->get('/health', function ($request, $response) {
  $response->getBody()->write(json_encode([
    'status' => 'healthy',
    'service' => 'Jobberwocky API',
    'timestamp' => date('Y-m-d H:i:s')
  ]));
  return $response->withHeader('Content-Type', 'application/json');
});

// Root endpoint
$app->get('/', function ($request, $response) {
  $response->getBody()->write(json_encode([
    'message' => 'Welcome to Jobberwocky API',
    'version' => '1.0.0',
    'endpoints' => [
      'POST /api/v1/jobs' => 'Create a new job',
      'GET /api/v1/jobs' => 'Get all jobs',
      'GET /api/v1/jobs/search' => 'Search jobs (q, location, company, sources)',
      'GET /api/v1/jobs/{id}' => 'Get job by ID',
      'DELETE /api/v1/jobs/{id}' => 'Delete job',
      'POST /api/v1/alerts/subscribe' => 'Subscribe to job alerts',
      'DELETE /api/v1/alerts/{id}' => 'Unsubscribe from alerts',
      'GET /api/v1/alerts' => 'Get all alerts'
    ]
  ]));
  return $response->withHeader('Content-Type', 'application/json');
});

$app->run();