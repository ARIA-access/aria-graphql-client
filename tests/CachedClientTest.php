<?php

use ARIA\GraphQLClient\CachedClient;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class CachedClientTest extends \PHPUnit\Framework\TestCase {
  
  private $definition;
  
  public function setUp() :void {
    
    $this->definition = new TestDefinition( new CachedClient( new ArrayAdapter(), 600, CachedClient::TEST_SERVER ));
    
  }
  
  public function testCachedHelloworld() {
    
    $result = $this->definition->helloworld();
    
    $this->assertNotEmpty($result);
    
    $this->assertNotEmpty($result['data']['documentItems']);

    $result2 = $this->definition->helloworld();

    $this->assertNotEmpty($result2);
    
    $this->assertNotEmpty($result2['data']['documentItems']);

    $this->assertEquals($result['data']['documentItems'], $result2['data']['documentItems']);
    
  }
  
}