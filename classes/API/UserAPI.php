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
        orcid_settings,
        aria_uid
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
   * @param array $filter array of additional variables to filter on (options include search, username, email, site_id etc)
   * @param array $order (ie. 'id' => asc)
   * @param int $limit
   * @param int $offset
   */
  public function search(array $filter = [], array $order = [], int $limit = 10, int $offset = 0 ): array
  {

    $query = "
      query {
        userItemFeed(
          filters: " . JSONEncodedGQL::encode($filter) . ",
          first: ". $limit. ",
          fromIndex: ". $offset. ",
          sort: " . JSONEncodedGQL::encode($order) . "
        ){
          totalCount,
          pageInfo {
            hasNext,
            endCursor,
            hasNextSlice
          },
          nodes {
            {$this->userProfileFields}
          }
        }
      }
    ";
    
    $result = $this->getClient()->call($query, Client::METHOD_GET);

    if (!empty($result['data'])) {

      if ($result['data']['userItemFeed']) {
        return $result['data']['userItemFeed'];
      }
    }

    return [];

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
        site_scopeItems(
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

      if ($result['data']['site_scopeItems']) {
        return $result['data']['site_scopeItems'];
      }
    }

    return [];
  }

  /**
   * Update User
   * Returns an array of scopes based on site_id
   * 
   * @param array $filter - array must include username
   */
  public function updateUserData(array $filter): array
  {
    $mutation = "
      mutation {
        updateUserData(
          input: " . JSONEncodedGQL::encode($filter) . "
        ) {
          {$this->userProfileFields}
        }
      }
    ";

    $result = $this->getClient()->call($mutation, Client::METHOD_POST);

    if (!empty($result['data'])) {

      if ($result['data']['updateUserData']) {
        return $result['data']['updateUserData'];
      }
    }

    return [];
  }

}
