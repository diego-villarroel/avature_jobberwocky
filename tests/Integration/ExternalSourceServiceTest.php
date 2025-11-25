<?php
declare(strict_types=1);

namespace Tests\Integration;

use Jobberwocky\Services\ExternalSourceService;
use Jobberwocky\Services\JobService;
use Jobberwocky\Repositories\InMemoryJobRepository;
use Jobberwocky\Adapters\ExternalJobSourceAdapter;
use PHPUnit\Framework\TestCase;

class ExternalSourceServiceTest extends TestCase {
  private ExternalSourceService $externalSource;
  private JobService $jobService;
  private InMemoryJobRepository $jobRepository;
  private ExternalJobSourceAdapter $externalAdapter;
  private array $data;
  private array $data2;
  
  protected function setUp() : void {
    $this->jobRepository = new InMemoryJobRepository();
    $this->jobService = new JobService($this->jobRepository);
    $this->externalAdapter = new ExternalJobSourceAdapter();
    $this->externalSource = new ExternalSourceService(
      $this->jobService, 
      $this->externalAdapter
    );

    $this->data = [
      'title' => 'Test Job',
      'company' => 'Test Company',
      'location' => 'Uruguay',
      'description' => 'Test Description',
      'salary' => '50000',
      'skills' => ['PHP', 'MySQL'],
      'source' => 'internal'
    ];
    $this->data2 = [
      'title' => 'Test Job 2',
      'company' => 'Test Company 2',
      'location' => 'Chile',
      'description' => 'Test Description 2',
      'salary' => '60000',
      'skills' => ['JavaScript', 'React'],
      'source' => 'internal'
    ];
  }

  protected function tearDown() : void {
    $this->jobRepository->clear();
  }
  
  public function testInternalSource() : void {
    $this->jobService->createJob($this->data);
    $this->jobService->createJob($this->data2);

    $results = $this->externalSource->searchAllSources([], false);

    $this->assertCount(2, $results);
    foreach ($results as $job) {
      $this->assertEquals('internal', $job->getSource());
    }
  }

  public function testBothSources() : void {
    $this->jobService->createJob($this->data);
    $this->jobService->createJob($this->data2);

    $results = $this->externalSource->searchAllSources([], true);
    $this->assertGreaterThanOrEqual(2, count($results));
  }

  public function testSearchMultiCriteria() : void {
    $this->jobService->createJob($this->data);
    $this->jobService->createJob($this->data2);
    $this->jobService->createJob([
      'title' => 'Test Job 3',
      'company' => 'Test Company 3',
      'location' => 'Argentina',
      'description' => 'Test Description 3',
      'salary' => '70000',
      'skills' => ['Python', 'Django'],
      'source' => 'internal'
    ]);

    $criteria = [
      'title' => 'senior python',
      'location' => 'remote',
      'company' => 'techcorp'
    ];
    $resutls = $this->externalSource->searchAllSources($criteria, true);

    $this->assertNotEmpty($resutls);
    $this->assertGreaterThanOrEqual(1, count($resutls));
  }

  public function testSearchReturnsEmpty() : void {
    $this->jobService->createJob($this->data);
    
    $results = $this->externalSource->searchAllSources(
      ['pattern' => 'nonexistent'],
      false
    );
    
    $this->assertIsArray($results);
    $this->assertEmpty($results);
  }

  public function testExternalSourceUnavailable() : void {
    $badAdapter = new ExternalJobSourceAdapter('http://localhost:9999');
    $externalSource = new ExternalSourceService($this->jobService, $badAdapter);

    $this->jobService->createJob($this->data);
    
    $results = $externalSource->searchAllSources([], true);
    
    $this->assertCount(1, $results);
    $this->assertEquals('internal', $results[0]->getSource());
  }
}
