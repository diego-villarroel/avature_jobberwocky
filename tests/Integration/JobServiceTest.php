<?php
declare(strict_types=1);

namespace Tests\Integration;

use Jobberwocky\Repositories\InMemoryJobRepository;
use Jobberwocky\Services\JobService;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class JobServiceTest extends TestCase {
  private JobService $jobService;
  private InMemoryJobRepository $jobRepository;
  private array $data;
  private array $data2;

  protected function setUp() : void {
    $this->jobRepository = new InMemoryJobRepository();
    $this->jobService = new JobService($this->jobRepository);
    $this->data = [
    'title'       => 'Software Engineer',
    'description' => 'Develop software applications',
    'location'    => 'Remote',
    'salary'      => '75000',
    'skills'      => ['PHP', 'JavaScript'],
    'company'     => 'AWS'
  ];
    $this->data2 = [
    'title'       => 'Frontend Developer',
    'description' => 'Build user interfaces',
    'location'    => 'Argentina',
    'salary'      => '65000',
    'skills'      => ['JavaScript', 'React'],
    'company'     => 'Google'
  ];
  }

  protected function tearDown() : void {
    $this->jobRepository->clear();
  }

  public function testCreateJob() : void {
    
    $result = $this->jobService->createJob($this->data);
    
    $this->assertNotNull($result);
    $this->assertEquals($this->data['title'], $result->getTitle());
  }

  public function testGetAll() : void {
    $this->jobService->createJob($this->data);
    $this->jobService->createJob($this->data2);

    $jobs = $this->jobService->getAll();

    $this->assertCount(2, $jobs);
  }

  public function testFindById() : void {
    $job = $this->jobService->createJob($this->data2);
    $found = $this->jobService->findById($job->getId());

    $this->assertNotNull($found);
    $this->assertEquals($job->getTitle(), $found->getTitle());
    $this->assertEquals($job->getDescription(), $found->getDescription());
    $this->assertEquals($job->getLocation(), $found->getLocation());
    $this->assertEquals($job->getSalary(), $found->getSalary());
    $this->assertEquals($job->getSkills(), $found->getSkills());
    $this->assertEquals($job->getCompany(), $found->getCompany());
  }

  public function testFindByIdNotFound() : void {
    $this->expectException(RuntimeException::class);
    $this->expectExceptionMessage('Job not found');

    $this->jobService->findById('non-existent-id');
  }

  public function testSearchJobs() : void {
    $this->jobService->createJob($this->data);
    $this->jobService->createJob($this->data2);
    $this->jobService->createJob([
    'title'       => 'Android Developer',
    'description' => 'Build user interfaces',
    'location'    => 'Argentina',
    'salary'      => '65000',
    'skills'      => ['Kotlin', 'React Native'],
    'company'     => 'Google']);

    $criteria1 = ['location' => 'Argentina'];
    $criteria2 = ['skills' => 'React'];
    $criteria3 = ['location' => 'Remote', 'company' => 'AWS'];

    $results1 = $this->jobService->searchJobs($criteria1);
    $results2 = $this->jobService->searchJobs($criteria2);
    $results3 = $this->jobService->searchJobs($criteria3);

    $this->assertCount(2, $results1);
    $this->assertCount(2, $results2);
    $this->assertCount(1, $results3);
  }
}