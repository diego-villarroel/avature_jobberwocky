<?php
declare(strict_types=1);

namespace Jobberwocky\Test\Unit;

use Jobberwocky\Models\JobAlert;
use PHPUnit\Framework\TestCase;
use Datetime;

class JobAlertTest extends TestCase {
  public function testCreateValidAlert() : void {
    $data = [
      'id' => 'test_id_1',
      'email' => 'emal@test.com',
      'search' => 'test_search',
      'created' => new DateTime()
    ];

    $alert = new JobAlert($data);

    $this->assertEquals($alert->getId(), $data['id']);
    $this->assertEquals($alert->getEmail(), $data['email']);
    $this->assertEquals($alert->getSearch(), $data['search']);
  }
}