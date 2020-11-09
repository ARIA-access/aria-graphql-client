<?php

namespace ARIA\GraphQLClient;

/**
 * API Definition.
 * 
 * Override this class in order to implement the specific API interface you require.
 */
abstract class APIDefinition 
{
  private $client;
  
  /**
   * Define a new API definition.
   * 
   * @param Client $client The GraphQL client
   */
  public function __construct(Client $client) 
  {
    $this->setClient($client);
  }
  
  protected function setClient(Client $client) {
    $this->client = $client;
  }
  
  protected function getClient(): Client {
    return $this->client;
  }
  
}