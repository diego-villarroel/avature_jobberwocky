<?php
declare(strict_types=1);

namespace Jobberwocky\Models;

use Datetime;
use InvalidArgumentException;

class JobAlert {
  private string $id;
  private string $email;
  private ?string $search;
  private DateTime $createdAt;

  public function __construct(array $data) {
    $this->validate($data);

    $this->id = $data['id'] ?? $this->generateId();
    $this->email = trim(strtolower($data['email']));
    $this->search = isset($data['search']) ? trim($data['search']) : null;
    $this->createdAt = $data['createdAt'] ?? new DateTime();
  }

  public function getId() : string {
    return $this->id;
  }

  public function getEmail() : string {
    return $this->email;
  }

  public function getSearch() : string|null {
    return $this->search;
  }

  public function getCreated() : Datetime {
    return $this->createdAt;
  }

  public function matchesJob(Job $job) : bool {
    if ($this->search === null) {
      return true;
    }
    
    $searchableText = strtolower(implode(' ', [
      $this->title,
      $this->company,
      $this->description,
      $this->location,
      is_array($this->skills) ? implode(' ', $this->skills) : $this->skills
    ]));

    return str_contains($searchableText,$this->search);
  }

  private function validate(array $data) : void {
    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
      throw new InvalidArgumentException('Valid email is required');
    } 
  }

  private function generateId() : string {
    return sprintf(
      'alert_%s_%s',
      time(),
      bin2hex(random_bytes(4))
    );
  }
}