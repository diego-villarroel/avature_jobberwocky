<?php
declare(strict_types=1);

namespace Jobberwocky\Tests\Unit;

use Jobberwocky\Adapters\ExternalJobSourceAdapter;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class ExternalAdapterTest extends TestCase {
  private ExternalJobSourceAdapter $adapter;

  protected function setUp() : void {
    $this->adapter = new ExternalJobSourceAdapter();
  }

  public function testParseSkillsXML() : void {
    $xmlString = '<skills><skill>AWS</skill><skill>Azure</skill><skill>Docker</skill></skills>';

    $skills = $this->adapter->parseSkillsXML($xmlString);

    $this->assertIsArray($skills);
    $this->assertCount(3, $skills);
    $this->assertContains('AWS', $skills);
    $this->assertContains('Azure', $skills);
    $this->assertContains('Docker', $skills);
  }

  public function testEmptyXML() : void {
    $xmlString = '<skills></skills>';

    $skills = $this->adapter->parseSkillsXML($xmlString);

    $this->assertIsArray($skills);
    $this->assertCount(0, $skills);
  }

  public function testInvalidXML() : void {
    $xmlString = 'invalid xml';

    $skills = $this->adapter->parseSkillsXML($xmlString);

    $this->assertIsArray($skills);
    $this->assertCount(0, $skills);
  }

  public function testNullXML() : void {    
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('XML string cannot be null or empty');
    
    $this->adapter->parseSkillsXML(null);
  }

  public function testWhitespacesXML() : void {
    $xmlString = '<skills>
      <skill>  Python  </skill>
      <skill>TensorFlow</skill>
      <skill>  Deep Learning  </skill>
    </skills>';

    $skills = $this->adapter->parseSkillsXML($xmlString);

    $this->assertIsArray($skills);
    $this->assertCount(3, $skills);
    $this->assertContains('Python', $skills);
    $this->assertContains('TensorFlow', $skills);
    $this->assertContains('Deep Learning', $skills);
  }

  public function testNormalize() : void {
    $externalData = [
      'USA' => [
        [
          'Cloud Engineer',
          65000,
          '<skills><skill>AWS</skill><skill>Azure</skill></skills>'
        ],
        [
          'DevOps Engineer',
          60000,
          '<skills><skill>CI/CD</skill><skill>Docker</skill></skills>'
        ]
      ],
      'Spain' => [
        [
          'Machine Learning Engineer',
          75000,
          '<skills><skill>Python</skill><skill>TensorFlow</skill></skills>'
        ]
      ]
    ];
    
    $jobs = $this->adapter->normalizeData($externalData);

    $this->assertCount(3, $jobs);
  }

}