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

  const METHOD_GET = 'GET';
  const METHOD_POST = 'POST';
  
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
   * Called before call, override to do stuff
   *
   * @return void
   */
  protected function beginCallHook() {}


  /**
   * Called after call, override to do stuff
   *
   * @return void
   */
  protected function endCallHook() {}
  
  /**
   * Execute a GraphQL Call.
   * 
   * @param string $query The graphql query
   * @param string $method GET/POST
   * @param string $variables Optional variables
   * @return array
   */
  public function call(string $query, string $method = Client::METHOD_GET, ?string $variables = null) : ? array {
    
    $this->beginCallHook();

    $client = new http();
    
    $headers = [
    ];
    
    if (!empty($this->token)) {
      $headers['Authorization'] = 'Bearer ' . $this->token;
    }

    $body = [];

    if (!empty($query)) $body['query'] = $query;
    //if (!empty($mutations)) $body['mutations'] = $mutations;
    if (!empty($variables)) $body['variables'] = $variables;

    // Catch a nulled endpoint
    if (empty($this->endpoint)) {
      throw new CallException('No GraphQL endpoint specified');
    }
      
    $response = $client->request($method, $this->endpoint, [
      'headers' => $headers,
      'form_params' => $body,
      'timeout' => 10
    ]);
    
    $responsebody = json_decode( $response->getBody(), true );
    
    // Has the GQL server responded with an error?
    if (!empty( $responsebody['errors']) ) {
      if (!empty($responsebody['errors'][0]['message'])) {
        throw new CallException( $responsebody['errors'][0]['message'] );
      }
      if (!empty($responsebody['errors']['message'])) {
        throw new CallException( $responsebody['errors']['message'] );
      }

      throw new CallException('Unknown GraphQL Error');
    }

    $this->endCallHook();

    return $responsebody;
  }
  
}