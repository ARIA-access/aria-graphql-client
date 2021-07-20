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

  private $groupMembershipFields = '
    group
    username
  ';

  private $groupFields = '
    id
    site_id
    label
    handler
    options
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
   * Retrieve user's groups
   * Returns an array of users based on fields
   * 
   * @param array $filter array of variables to filter on
   */
  public function userGroupMembership(array $filter, bool $expanded = true): array
  {
    $expandedQuery = '';
    if ($expanded) {

      $expandedQuery = "
      userItems {
        {$this->userProfileFields}
      }
      groupItems {
        {$this->groupFields}
      }
      ";
    }

    $query = "
    query {
      userGroupMembershipItems(
        filters: " . JSONEncodedGQL::encode($filter) . "
      ){
        {$this->groupMembershipFields}
        $expandedQuery
      }
    }
    ";
    
    $result = $this->getClient()->call($query, Client::METHOD_GET);

    if (!empty($result['data'])) {

      if ($result['data']['userGroupMembershipItems']) {
        return $result['data']['userGroupMembershipItems'];
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
   * Retrieve scope attributes
   * Returns an array of attributes based on scope ID
   * 
   * @param string $scope_id 
   */
  public function scope_attributes( string $scope_id): array
  {
    $query = <<< END
      query {
        scope_attributeItems(
          filters: {
            scope_id: "$scope_id"
          }
        ) {
            id,
            attributeItems {
              reference,
              name,
              description
            }
        }
      }
END;

    $result = $this->getClient()->call($query, Client::METHOD_GET);

    if (!empty($result['data'])) {

      if ($result['data']['scope_attributeItems']) {
        return $result['data']['scope_attributeItems'];
      }
    }

    return [];
  }

  /**
   * Refresh scopes granted to a user for a specific client.
   *
   * @param string $client_id Optional client ID
   * @return boolean
   */
  public function refreshScopes( string $client_id = null ) : bool 
  {

    $client = "";
    if (!empty($client_id)) {
      $client = "client_id: \"$client_id\"";
    }
    $mutation = "
      mutation {
        refreshClientScopes(input: {
          $client
        })
      }
      ";

    $result = $this->getClient()->call($mutation, Client::METHOD_POST);

    if (!empty($result['data'])) {

      if ($result['data']['refreshClientScopes']['status'] !== false) {
        return true;
      }
    }

    return false;
  }

  /**
   * Is the user profile completed for a bundle of attributes
   * Returns an array of attributes based on array ofscopes
   * 
   * @param array $scope_id 
   * @param string $username
   * 
   * @return bool
   */
  public function isProfileComplete( array $scope_id, string $username): bool
  {

    $scope_id = json_encode($scope_id); 

    $query = <<< END
      query {
        isProfileCompleteItems(
          filters: {
            username: "$username"
            scope_id: $scope_id
        }
      ) {
          is_profile_complete
      }
    }
    
END;

    $result = $this->getClient()->call($query, Client::METHOD_GET);

    if (!empty($result['data'])) {

      if ($result['data']['isProfileCompleteItems'][0]['is_profile_complete'] === true) {
        return true;
      }
    }

    return false;
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
