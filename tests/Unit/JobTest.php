<?php
declare(strict_types=1);

namespace Tests\Unit;

use DateTime;
use InvalidArgumentException;
use Jobberwocky\Models\Job;
use PHPUnit\Framework\TestCase;

class JobTest extends TestCase {

  // test

  public function testCanCreateValidJob() : void {
    $data = [
      'title' => 'Backend Developer',
      'company' => 'TechCorp',
      'description' => 'Great opportunity',
      'location' => 'Remote',
      'salary' => '100k-150k',
      'skills' => ['PHP', 'MySQL', 'Docker']
    ];

    $job = new Job($data);

    $this->assertEquals($job->getTitle(), 'Backend Developer');
    $this->assertEquals($job->getCompany(), 'TechCorp');
    $this->assertEquals($job->getDescription(), 'Great opportunity');
    $this->assertEquals($job->getLocation(), 'Remote');
    $this->assertEquals($job->getSalary(), '100k-150k');
    $this->assertEquals($job->getSkills(), ['PHP', 'MySQL', 'Docker']);
  }

  public function testEmptyTitle() : void {
    $data = [
      'title' => '',
      'company' => 'Google'
    ];
    
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Title is required');
    new Job($data);
  }

  public function testEmptySalary() : void {
    $data = [
      'title' => 'Frontend Developer',
      'salary' => ''
    ];
    
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Salary is required');
    new Job($data);
  }

  public function testEmptySkills() : void {
    $data = [
      'title' => 'Frontend Developer',
      'salary' => '50k-80k',
      'skills' => []
    ];
    
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Skills is required');
    new Job($data);
  }

  public function testEmptyLocation() : void {
    $data = [
      'title' => 'Frontend Developer',
      'salary' => '50k-80k',
      'skills' => ['JavaScript'],
      'location' => ''
    ];
    
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Location is required');
    new Job($data);
  }

  public function testEmptyCompany() : void {
    $data = [
      'title' => 'Frontend Developer',
      'salary' => '50k-80k',
      'skills' => ['JavaScript'],
      'location' => 'Remote',
      'company' => ''
    ];
    
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Company is required');
    new Job($data);
  }
}