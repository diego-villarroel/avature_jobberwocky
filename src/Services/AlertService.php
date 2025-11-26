<?php
declare(strict_types=1);

namespace Jobberwocky\Services;

use Jobberwocky\Models\JobAlert;
use Jobberwocky\Models\Job;
use RuntimeException;

class AlertService {
  private array $alerts = [];
  private array $sentNotifications = [];
  
  public function subscribe(array $data) : JobAlert {
    $alert = new JobAlert($data);
    
    foreach ($this->alerts as $existing) {
      if ($existing->getEmail() === $alert->getEmail() && $existing->getSearch() === $alert->getSearch()) {
        throw new RuntimeException('Alert already exists');
      }
    }

    $this->alerts[$alert->getId()] = $alert;
    return $alert;
  }

  public function unsubscribe(string $id) : void{
    if (!isset($this->alerts[$id])) {
      throw new RuntimeException("Alert not found with id: $id");
    }

    unset($this->alerts[$id]);
  }

  public function findAll() : array {
    return array_values($this->alerts);
  }

  public function findById(string $id) : ?JobAlert {
    return $this->alerts[$id] ?? null;
  }

  public function notifySubscribers(Job $job) : array {
    $notified = [];

    foreach ($this->alerts as $alert) {
      if ($alert->matchesJob($job)) {
        $this->sendNotification($alert, $job);
        $notified[] = [
          'email' => $alert->getEmail(),
          'job' => $job->getTitle()
        ];
      }
    }

    return $notified;
  }

  private function sendNotification(JobAlert $alert, Job $job) : void {
    $this->sentNotifications[] = [
      'to' => $alert->getEmail(),
      'job_id' => $job->getId(),
      'job_title' => $job->getTitle(),
      'sent_at' => date('Y-m-d H:i:s')
    ];

    error_log(sprintf(
      "Email sent to %s: New job '%s' at %s",
      $alert->getEmail(),
      $job->getTitle(),
      $job->getCompany()
    ));
  }

  public function getSentNotifications() : array {
    return $this->sentNotifications;
  }

  public function clearNotifications() : void {
    $this->sentNotifications = [];
  }
}