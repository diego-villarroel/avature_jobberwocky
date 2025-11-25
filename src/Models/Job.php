<?php
declare(strict_types=1);

namespace Jobberwocky\Models;

use DateTime;
use InvalidArgumentException;

class Job {
  private string    $id;
  private string    $title;
  private string    $company;
  private string    $description;
  private string    $location;
  private string    $salary;
  private array     $skills;
  private DateTime  $postedAt;
  private string    $source;
  private bool      $status;

  public function __construct(array $data){
    // Validate input data
    $this->validate($data);

    $this->id = $data['id'] ?? $this->generateId();
    $this->title = $data['title'];
    $this->company = $data['company'] ?? '';
    $this->description = $data['description'] ?? '';
    $this->location = $data['location'] ?? '';
    $this->salary = $data['salary'] ?? '';
    $this->skills = $data['skills'] ?? [];
    $this->postedAt = $data['postedAt'] ?? new DateTime;
    $this->source = $data['source'] ?? 'external';
    $this->status = true;
  }

  // Utils

  private function generateId() : string {
    return sprintf(
      'job_%s_%s',
      time(),
      bin2hex(random_bytes(4))
    );
  }

  private function validate(array $data): void {
    if (is_null($data['title']) || empty(trim($data['title']))) {
      throw new InvalidArgumentException('Title is required');
    }

    if (is_null($data['salary']) || empty(trim($data['salary']))) {
      throw new InvalidArgumentException('Salary is required');
    }

    if (empty($data['skills'])) {
      throw new InvalidArgumentException('Skills is required');
    }

    if (is_null($data['location']) || empty(trim($data['location']))) {
      throw new InvalidArgumentException('Location is required');
    }

    if (is_null($data['company']) || empty(trim($data['company']))) {
      throw new InvalidArgumentException('Company is required');
    }
  }

  // Getters
  public function getId(): string {
    return $this->id;
  }

  public function getTitle(): string {
    return $this->title;
  }

  public function getCompany(): string {
    return $this->company;
  }

  public function getDescription(): string {
    return $this->description;
  }

  public function getLocation(): string {
    return $this->location;
  }

  public function getSalary(): string {
    return $this->salary;
  }

  public function getSkills(): array {
    return $this->skills;
  }

  public function getPostedAt(): DateTime {
    return $this->postedAt;
  }

  public function getSource(): string {
    return $this->source;
  }

  public function getStatus(): bool {
    return $this->status;
  }

  // Setters

  public function setStatus(bool $status): void {
    $this->status = $status;
  }
  
  public function setSource(string $source): void {
    $this->source = $source;
  }
  
}