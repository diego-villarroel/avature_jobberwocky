<?php
declare(strict_types=1);

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use RuntimeException;
use Jobberwocky\Services\AlertService;
use Jobberwocky\Models\Job;

class AlertServiceTest extends TestCase {
  private AlertService $alertService;

  protected function setUp() : void {
    $this->alertService = new AlertService();
  }

  public function testSubscribeWithValidEmail() : void {
    $data = [
      'email' => 'candidate@example.com',
      'search' => 'python developer'
    ];
    
    $alert = $this->alertService->subscribe($data);
    
    $this->assertNotNull($alert->getId());
    $this->assertEquals('candidate@example.com', $alert->getEmail());
    $this->assertEquals('python developer', $alert->getSearch());
  }

  public function testSubscribeWithInvalidEmail() : void {
    $data = [
      'email' => 'invalid-email',
      'search' => 'developer'
    ];
    
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Valid email is required');
    
    $this->alertService->subscribe($data);
  }

  public function testSubscribeWithoutSearchPattern() : void {
    $data = [
      'email' => 'candidate@example.com'
    ];
    
    $alert = $this->alertService->subscribe($data);
    
    $this->assertNotNull($alert->getId());
    $this->assertNull($alert->getSearch());
  }

  public function testCannotSubscribeTwiceWithSameEmailAndPattern() : void {
    $data = [
      'email' => 'candidate@example.com',
      'search' => 'python'
    ];
    $this->alertService->subscribe($data);
    
    $this->expectException(RuntimeException::class);
    $this->expectExceptionMessage('Alert already exists');
    
    $this->alertService->subscribe($data);
  }

  public function testUnsubscribe() : void {
    $alert = $this->alertService->subscribe([
      'email' => 'test@example.com',
      'search' => 'developer'
    ]);
    
    $this->alertService->unsubscribe($alert->getId());
    
    $this->assertNull($this->alertService->findById($alert->getId()));
  }

  public function testFindAll() : void {
    $this->alertService->subscribe(['email' => 'user1@example.com']);
    $this->alertService->subscribe(['email' => 'user2@example.com']);
    
    $alerts = $this->alertService->findAll();
    
    $this->assertCount(2, $alerts);
  }

  public function testGetSentNotifications() : void {
    $this->alertService->subscribe([
      'email' => 'test@example.com',
      'search' => 'php'
    ]);

    $job = new Job([
      'title' => 'PHP Developer',
      'company' => 'TechCorp',
      'salary' => '65k',
      'skills' => ['PHP', 'Laravel'],
      'location' => 'Remote'
    ]);
    
    $this->alertService->notifySubscribers($job);
    $notifications = $this->alertService->getSentNotifications();
    
    $this->assertCount(1, $notifications);
    $this->assertEquals('test@example.com', $notifications[0]['to']);
    $this->assertEquals('PHP Developer', $notifications[0]['job_title']);
  }
}