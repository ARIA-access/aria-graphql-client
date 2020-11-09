<?php

namespace ARIA\graphql-client;

abstract class APIDefinition 
{
  private $client;
  
  public function __construct(Client $client) 
  {
    $this->setClient($client);
  }
  
  protected function setClient(Client $client) {
    $this->client = $client;
  }
  
}