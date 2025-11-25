<?php
declare(strict_types=1);

namespace Jobberwocky\Repositories;

use Jobberwocky\Models\Job;
use DateTime;
use PDO;

class SQLiteJobRepository implements IJobRepository {
  private PDO $pdo;

  public function __construct(string $databasePath = 'database/jobs.sqlite') {
    $this->pdo = new PDO("sqlite:$databasePath");
    $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $this->createTable();
  }

  private function createTable(): void {
    $sql = <<<SQL
      CREATE TABLE IF NOT EXISTS jobs (
        id TEXT PRIMARY KEY,
        title TEXT NOT NULL,
        company TEXT NOT NULL,
        description TEXT,
        location TEXT,
        salary TEXT,
        skills TEXT,
        posted_at TEXT NOT NULL,
        source TEXT DEFAULT 'internal',
        status BOOL DEFAULT 1
      )
    SQL;

    $this->pdo->exec($sql);
  }

  public function save(Job $job) : void {
    $sql = <<<SQL
      INSERT OR REPLACE INTO jobs
      (id, title, company, location, salary, skills, description, posted_at, source, status)
      VALUES (:id, :title, :company, :location, :salary, :skills, :description, :posted_at, :source, :status )
    SQL;
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
      ':id' => $job->getId(),
      ':title' => $job->getTitle(),
      ':company' => $job->getCompany(),
      ':location' => $job->getLocation(),
      ':salary' => $job->getSalary(),
      ':skills' => json_encode($job->getSkills()),
      ':description' => $job->getDescription(),
      ':posted_at' => $job->getPostedAt()->format('Y-m-d H:i:s'),
      ':source' => $job->getSource(),
      ':status' => $job->getStatus()
    ]);
  }

  public function getAll() : array {
    $stmt = $this->pdo->query('SELECT * FROM jobs ORDER BY posted_at DESC');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map([$this, 'hydrate'], $rows);
  }

  public function findById (string $id) : ?Job {
    $stmt = $this->pdo->prepare('SELECT * FROM jobs WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ? $this->hydrate($row) : null;    
  }

  public function searchJobs(array $criteria) : array {
    $sql = 'SELECT * FROM jobs WHERE 1=1';
    $params = [];

    if (isset($criteria['pattern']) && !empty($criteria['pattern'])) {
      $sql .= ' AND (
          title LIKE :pattern 
          OR company LIKE :pattern 
          OR description LIKE :pattern 
          OR location LIKE :pattern
          OR skills LIKE :pattern
      )';
      $params[':pattern'] = '%' . $criteria['pattern'] . '%';
    }

    if (isset($criteria['location']) && !empty($criteria['location'])) {
      $sql .= ' AND location LIKE :location';
      $params[':location'] = '%' . $criteria['location'] . '%';
    }

    if (isset($criteria['company']) && !empty($criteria['company'])) {
      $sql .= ' AND company LIKE :company';
      $params[':company'] = '%' . $criteria['company'] . '%';
    }

    $sql .= ' ORDER BY posted_at DESC';

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return array_map([$this, 'hydrate'], $rows);
  }

  private function hydrate(array $row) : Job {
    return new Job([
      'id' => $row['id'],
      'title' => $row['title'],
      'company' => $row['company'],
      'description' => $row['description'],
      'location' => $row['location'],
      'salary' => $row['salary'],
      'skills' => json_decode($row['skills'], true) ?? [],
      'postedAt' => new DateTime($row['posted_at']),
      'source' => $row['source']
    ]);
  }

}