<?php

declare(strict_types=1);

namespace Jobberwocky\Controllers;

use Jobberwocky\Services\AlertService;
use JobberWocky\Utils;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use RuntimeException;
use InvalidArgumentException;

class AlertController {
  private AlertService $alertService;
  private Utils $util;

  public function __construct(AlertService $alertService, Utils $util) {
      $this->alertService = $alertService;
      $this->util = $util;
  }

  public function subscribe(Request $request, Response $response) : Response {
    try {
      $data = $request->getParsedBody();
      $alert = $this->alertService->subscribe($data);
      $util = new Utils;

      $response->getBody()->write(json_encode([
        'success' => true,
        'data' => $this->util->objToArray($alert),
        'message' => 'Successfully subscribed to job alerts'
      ]));

      return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(201);

    } catch (InvalidArgumentException | RuntimeException $e) {
      $response->getBody()->write(json_encode([
        'success' => false,
        'error' => $e->getMessage()
      ]));

      return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(400);

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

  public function unsubscribe(Request $request, Response $response, array $args) : Response {
    try {
      $this->alertService->unsubscribe($args['id']);

      $response->getBody()->write(json_encode([
        'success' => true,
        'message' => 'Successfully unsubscribed from job alerts'
      ]));

      return $response->withHeader('Content-Type', 'application/json');

    } catch (RuntimeException $e) {
      $response->getBody()->write(json_encode([
        'success' => false,
        'error' => $e->getMessage()
      ]));

      return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus(404);

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

  public function getAll(Request $request, Response $response) : Response {
    try {
      $alerts = $this->alertService->findAll();

      $response->getBody()->write(json_encode([
        'success' => true,
        'data' => array_map(fn($alert) => $this->util->objToArray($alert), $alerts),
        'count' => count($alerts)
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