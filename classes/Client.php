<?php

namespace ARIA\GraphQLClient;
use GuzzleHttp\Client as http;

/**
 * Low level GraphQL client for communicating with ARIA
 */
class Client 
{
  const LIVE_SERVER = 'https://graphql.aria.services/graphql';
  const TEST_SERVER = 'https://graphql-test.aria.services/graphql';
  const BETA_SERVER = 'https://graphql-beta.aria.services/graphql';
  
  private $endpoint;
  private $token;
 
  /**
   * Define a client
   * @param string $endpoint API Endpoint
   */
  public function __construct(string $endpoint = 'https://graphql.aria.services/graphql') {
    
    $this->setEndpoint($endpoint);
    
  }
  
  /**
   * 
   * @param string $tokenSet authentication token
   */
  public function setToken( string $token ) {
    $this->token = $token;
  }
  
  /**
   * Set the endpoint
   */
  public function setEndpoint(string $endpoint) {
    
    $this->endpoint = $endpoint;
    
  }

  /**
   * Retrieve the currently defined endpoint.
   */
  public function getEndpoint() : string {

    return $this->endpoint;

  }
  
  /**
   * Execute a GraphQL Call.
   * 
   * @param string $query The graphql query
   * @param string $mutations Optional mutations
   * @param string $variables Optional variables
   * @return array
   */
  public function call(string $query, string $mutations = '', string $variables = '', string $method = 'GET') : ? array {
    
    $client = new http();
    
    $headers = [
    ];
    
    if (!empty($this->token)) {
      $headers['Authorization'] = 'Bearer ' . $this->token;
    }
      
    $response = $client->request($method, $this->endpoint, [
      'headers' => $headers,
      'form_params' => [
          'query' => $query,
          'mutations' => $mutations,
          'variables' => $variables
      ]
    ]);
    
    return json_decode( $response->getBody(), true );
    
  }
  
}