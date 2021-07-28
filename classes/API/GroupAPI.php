<?php

use ARIA\GraphQLClient\API\Fields\GroupFields;
use ARIA\GraphQLClient\API\Fields\GroupMembershipFields;
use ARIA\GraphQLClient\APIDefinition;
use ARIA\GraphQLClient\Client;
use ARIA\GraphQLClient\CallException;
use ARIA\GraphQLClient\JSONEncodedGQL;

class GroupAPI extends APIDefinition
{

  use GroupFields;
  use GroupMembershipFields;

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

  /**
   * Add a user to a group.
   * This will either join a user to an already existing static group and add them to your list of groups, or if a dynamic group, it will just add
   * the group to your list with paramters. 
   *
   * @param array $filter
   * @return array
   */
  public function addToGroup( array $filter ) : array 
  {
    $mutation = "
      mutation {
        addToGroup(
          input: " . JSONEncodedGQL::encode($filter) . "
        ) {
          {$this->groupMembershipFields},
          options
        }
      }
    ";

    $result = $this->getClient()->call($mutation, Client::METHOD_POST);

    if (!empty($result['data'])) {

      if ($result['data']['addToGroup']) {
        return $result['data']['addToGroup'];
      }
    }

    return [];
  }

  /**
   * When passed a "group" array of uuids, this method will return a list of sites to which are not on the list.
   *
   * @param array $filter
   * @return array|null
   */
  public function newUserGroups( array $filter ): ?array 
  {

    $query = "
    query {
      newUserGroupsItems(
        filters: " . JSONEncodedGQL::encode($filter) . "
      ){
        {$this->groupFields}
      }
    }
    ";

    $result = $this->getClient()->call($query, Client::METHOD_GET);

    if (!empty($result['data'])) {

      if ($result['data']['newUserGroupsItems']) {
        return $result['data']['newUserGroupsItems'];
      }
    }

    return [];
  }

  /**
   * Retrieve my groups for a given site to which I am a member
   *
   * @param array $filter
   * @return array|null
   */
  public function myGroups( array $filter ): ?array 
  {

    $query = "
    query {
      myGroupsItems(
        filters: " . JSONEncodedGQL::encode($filter) . "
      ){
        site_id,
        group
      }
    }
    ";

    $result = $this->getClient()->call($query, Client::METHOD_GET);

    if (!empty($result['data'])) {

      if ($result['data']['myGroupsItems']) {
        return $result['data']['myGroupsItems'];
      }
    }

    return [];
  }



}