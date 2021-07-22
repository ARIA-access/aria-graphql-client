<?php

use ARIA\GraphQLClient\API\Fields\GroupFields;
use ARIA\GraphQLClient\APIDefinition;
use ARIA\GraphQLClient\Client;
use ARIA\GraphQLClient\CallException;
use ARIA\GraphQLClient\JSONEncodedGQL;

class GroupAPI extends APIDefinition
{

  use GroupFields;
  
  /**
   * Update User
   * Returns an array of scopes based on site_id
   * 
   * @param array $filter - array must include username
   */
  public function createGroup(array $filter): array
  {
    $mutation = "
      mutation {
        createGroup(
          input: " . JSONEncodedGQL::encode($filter) . "
        ) {
          {$this->groupFields}
        }
      }
    ";

    $result = $this->getClient()->call($mutation, Client::METHOD_POST);

    if (!empty($result['data'])) {

      if ($result['data']['createGroup']) {
        return $result['data']['createGroup'];
      }
    }

    return [];
  }



}