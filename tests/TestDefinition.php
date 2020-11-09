<?php

use ARIA\graphql-client\APIDefinition;

class TestDefinition extends APIDefinition 
{
  
  public function helloworld() {
    
    $client = $this->getClient();
    
    $client->call([
        'query' => [
            'documentItems' => [
                'id'
            ]
        ]
    ])
    
  }
  
}