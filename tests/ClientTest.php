<?php

use ARIA\GraphQLClient\Client;

class ClientTest extends \PHPUnit\Framework\TestCase {
  
  private $definition;
  
  public function setUp() :void {
    
    $this->definition = new TestDefinition( new Client( Client::TEST_SERVER ));
    
  }
  
  public function testHelloworld() {
    
    $result = $this->definition->helloworld();
    
    $this->assertNotEmpty($result);
    
    $this->assertNotEmpty($result['data']['documentItems']);
    
  }
  
}