<?php

namespace ARIA\GraphQLClient\API;

use ARIA\GraphQLClient\APIDefinition;
use ARIA\GraphQLClient\Client;
use ARIA\GraphQLClient\CallException;
use ARIA\GraphQLClient\JSONEncodedGQL;

class UserAPI extends APIDefinition
{

  /**
   * Search users
   * Returns an array of users
   * 
   * @param array $filter array of variables to filter on
   */
  public function user(array $filter): array
  {

    $query = "
    query {
      userItems(
        filters: " . JSONEncodedGQL::encode($filter) . "
      ){
        username,
        first_name, 
        last_name,
        email,
        perm_group_id,
        avatar,
        gender:,
        nationality,
        country_of_residence,
        organization_id,
        publication,
        bio,
        specialization,
        career_stage,
        orcid,
        orcid_settings
      }
    }
    ";
    
    $result = $this->getClient()->call($query, Client::METHOD_GET);

    if (!empty($result['data'])) {

      if ($result['data']['userItems']) {
        return $result['data']['userItems'];
      }
    }

    return [];
  }
}
