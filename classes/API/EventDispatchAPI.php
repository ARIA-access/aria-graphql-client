<?php

namespace ARIA\GraphQLClient\API;

use ARIA\GraphQLClient\APIDefinition;
use ARIA\GraphQLClient\Client;
use ARIA\GraphQLClient\CallException;
use ARIA\GraphQLClient\JSONEncodedGQL;

class EventDispatchAPI extends APIDefinition
{
  /**
   * Dispatch an event
   *
   * @param string $origin The originating service (e.g. aria-core, profiledata )
   * @param string $event Type of event, e.g. 'profileUpdate'
   * @param array $payload Optional payload
   * @return boolean
   */
  public function dispatchEvent(string $origin, string $event, array $payload = []): bool
  {

    $mutation = "
      mutation {
        dispatchEvent(input: {
          origin: \"$origin\",
          event: \"$event\",
          payload: " . JSONEncodedGQL::encode($payload) . "
        })
      }
      ";

    $result = $this->getClient()->call($mutation, Client::METHOD_POST);

    if (!empty($result['data'])) {

      if ($result['data']['dispatchEvent']['status'] !== false) {
        return true;
      }
    }

    return false;
  }
}