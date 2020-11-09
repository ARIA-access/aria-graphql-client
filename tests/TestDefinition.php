<?php

use ARIA\GraphQLClient\APIDefinition;

class TestDefinition extends APIDefinition 
{
  
  public function helloworld() {
    
    $client = $this->getClient();
    
    return $client->call('query { documentItems { id } }');
    
  }
  
}