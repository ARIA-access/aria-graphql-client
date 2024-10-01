<?php

namespace ARIA\GraphQLClient\API;

use ARIA\GraphQLClient\API\Exceptions\ProfileValidationException;
use ARIA\GraphQLClient\API\Fields\GroupFields;
use ARIA\GraphQLClient\API\Fields\GroupMembershipFields;
use ARIA\GraphQLClient\API\Fields\UserProfileFields;
use ARIA\GraphQLClient\APIDefinition;
use ARIA\GraphQLClient\Client;
use ARIA\GraphQLClient\CallException;
use ARIA\GraphQLClient\JSONEncodedGQL;

class UserAPI extends APIDefinition
{

  use UserProfileFields;
  use GroupFields;
  use GroupMembershipFields;

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
   * Retrieve scope
   * 
   * @param string $scope_id 
   */
  public function scope( string $scope_id): array
  {
    $query = <<< END
      query {
        scopeItems(
          filters: {
            id: "$scope_id"
          }
        ) {
          reference,
          name,
          description
        }
      }
END;

    $result = $this->getClient()->call($query, Client::METHOD_GET);

    if (!empty($result['data'])) {

      if ($result['data']['scopeItems']) {
        return $result['data']['scopeItems'];
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
   * Retrieve scope validations
   * 
   * @param string $scope_id 
   */
  public function scope_validation( string $scope_id, string $attribute_id = null): array
  {

    $filters['scope_id'] = $scope_id;
    if (!empty($attribute_id)) $filters['attribute_id'] = $attribute_id;

    $filters_json = JSONEncodedGQL::encode($filters);

    $query = <<< END
      query {
        scopeItems(
          filters: $filters_json
        ) {
          id
          scope_id
          attribute_id
          restriction
          description
          value
        }
      }
END;

    $result = $this->getClient()->call($query, Client::METHOD_GET);

    if (!empty($result['data'])) {

      if ($result['data']['scope_validationItems']) {
        return $result['data']['scope_validationItems'];
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
        }) {
          status
        }
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
   * Returns an array of attributes based on array of scopes
   * 
   * @param array $scope_id 
   * @param string $username
   * @param bool $show_messages 
   * 
   * @return bool
   */
  public function isProfileComplete( array $scope_id, string $username, bool $show_messages = false): bool
  {

    $scope_id = json_encode($scope_id); 

    if (!is_array(json_decode($scope_id))) {
      throw new \RuntimeException("The scopes must be provided in a flattened array with normal indices. Dump: ".var_export($scope_id, true));
    }

    $messages = '';
    if ($show_messages) {
      $messages = 'messages';
    }

    $query = <<< END
      query {
        isProfileCompleteItems(
          filters: {
            username: "$username"
            scope_id: $scope_id
        }
      ) {
          is_profile_complete
          $messages
      }
    }
    
END;

    $result = $this->getClient()->call($query, Client::METHOD_GET);

    if (!empty($result['data'])) {

      if ($result['data']['isProfileCompleteItems'][0]['is_profile_complete'] === true) {
        return true;
      }

      if ($show_messages) {
        
        throw new ProfileValidationException($result['data']['isProfileCompleteItems'][0]['messages'] ? $result['data']['isProfileCompleteItems'][0]['messages'] : "User profile is incomplete");
        
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
   * @return array
   */
  public function isProfileCompleteDetail( array $scope_id, string $username): bool
  {

    $scope_id = json_encode($scope_id); 

    $query = <<< END
      query {
        isProfileCompleteDetailItems(
          filters: {
            username: "$username"
            scope_id: $scope_id
        }
      ) {
        username
        scope_id
        attribute
        complete
      }
    }
    
END;

    $result = $this->getClient()->call($query, Client::METHOD_GET);

    if (!empty($result['data'])) {

      if ($result['data']['isProfileCompleteDetail']) {
        return $result['data']['isProfileCompleteDetail'];
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
  
  /**
   * Retrieve usernames who are members of a given site.
   *
   * @param string $site_id
   * @return array|null
   */
  public function siteMembers(string $site_id): ?array
  {

    $query = <<< END
      query {
        siteMembersItems(
          filters: {
            site_id: "$site_id"
          }
      ) {
          username
        }
      }
    END;
    
    $result = $this->getClient()->call($query, Client::METHOD_GET);

    if (!empty($result['data'])) {
      if (!empty($result['data']['siteMembersItems'])) {
        return array_column($result['data']['siteMembersItems'], "username");
      }
    }

    return null;
  }


}
