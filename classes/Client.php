<?php

namespace ARIA\graphql-client;
use GuzzleHttp\Client;

class Client 
{
  const LIVE_SERVER = 'https://graphql.aria.services/graphql';
  const TEST_SERVER = 'https://graphql-test.aria.services/graphql';
  const BETA_SERVER = 'https://graphql-beta.aria.services/graphql';
  
  private $endpoint;
 
  public function __construct(string $endpoint = 'https://graphql.aria.services/graphql') {
    
    $this->setEndpoint($endpoint);
    
  }
  
  public function setEndpoint(string $endpoint) {
    
    $this->endpoint = $endpoint;
    
  }
  
  public function call(array $query) : ? array {
    
    $response = $client->request('POST', $this->endpoint, [
        'json' => $query
    ]);
    
    return json_decode( $response->getBody(), true );
    
  }
  
  public function callRaw(string $query) : ? array {
    
    return $this->call( json_decode($query, true) );
    
  }
}