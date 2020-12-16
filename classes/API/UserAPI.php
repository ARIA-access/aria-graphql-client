<?php

namespace ARIA\GraphQLClient\API;

use ARIA\GraphQLClient\APIDefinition;
use ARIA\GraphQLClient\Client;
use ARIA\GraphQLClient\CallException;
use ARIA\GraphQLClient\JSONEncodedGQL;

class UserAPI extends APIDefinition
{

  /**
   * Defining profile fields
   */
  private $userProfileFields = '
        username,
        first_name, 
        last_name,
        email,
        perm_group_id,
        avatar,
        gender,
        nationality,
        country_of_residence,
        organization_id,
        publication,
        bio,
        specialization,
        career_stage,
        orcid,
        orcid_settings
  ';

  /**
   * Retrieve users.
   * Returns an array of users based on fields
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
        {$this->userProfileFields}
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

  /**
   * Search for users.
   * Returns an array of users based on search string
   * 
   * @param string $search Search string to query for.
   * @param array $filter array of additional variables to filter on
   */
  public function search( string $search, array $filter = []): array
  {
    $filter['search'] = $search;

    return $this->user($filter);
  }

  /**
   * Retrieve site scopes
   * Returns an array of scopes based on site_id
   * 
   * @param string $site_id 
   */
  public function site_scope( string $site_id): array
  {
    $query = <<< END
      query {
        siteScopeItems(
          filters: {
            site_id: "$site_id"
          }
        ) {
            site_id,
            scope_id,
            scopeItems {
              reference,
              name,
              description
            }
        }
      }
    END;

    $result = $this->getClient()->call($query, Client::METHOD_GET);

    if (!empty($result['data'])) {

      if ($result['data']['siteScopeItems']) {
        return $result['data']['siteScopeItems'];
      }
    }

    return [];
  }

}
