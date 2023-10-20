<?php

namespace ARIA\GraphQLClient\API;


use ARIA\GraphQLClient\API\Fields\SiteExFields;
use ARIA\GraphQLClient\API\Fields\SiteFields;
use ARIA\GraphQLClient\APIDefinition;
use ARIA\GraphQLClient\Client;
use ARIA\GraphQLClient\CallException;
use ARIA\GraphQLClient\JSONEncodedGQL;
use RuntimeException;

class PermissionAPI extends APIDefinition
{

  protected function __permission(string $name, int $id): array
  {
    $filter = [
      'entity_id' => $id
    ];

    $query = "
    query {
      ".$name."Items(
        filters: " . JSONEncodedGQL::encode($filter) . "
      ){
        entity_id
        scopes
      }
    }
    ";

    $result = $this->getClient()->call($query, Client::METHOD_GET);

    if (!empty($result['data'])) {

      if ($result['data'][$name.'Items'][0]) {
        return $result['data'][$name.'Items'][0];
      }
    }

    return [];
  }

  /**
   * Find permissions of a proposal
   *
   * @param integer $pid
   * @return array
   */
  public function proposalPermission(int $pid) : array {

    return $this->__permission('proposalPermission', $pid);

  }

  /**
   * Find permissions of a visit
   *
   * @param integer $vid
   * @return array
   */
  public function visitPermission(int $vid) : array {

    return $this->__permission('visitPermission', $vid);

  }

  /**
   * Find user permissions
   *
   * @param integer $uid
   * @return array
   */
  public function userPermission(int $uid) : array {

    return $this->__permission('userPermission', $uid);

  }

  /**
   * Find access route permissions
   *
   * @param integer $acid
   * @return array
   */
  public function accessPermission(int $acid) : array {

    return $this->__permission('accessPermission', $acid);

  }

  /**
   * Find call route permissions
   *
   * @param integer $cid
   * @return array
   */
  public function callPermission(int $cid) : array {

    return $this->__permission('callPermission', $cid);

  }

  /**
   * Find call application route permissions
   *
   * @param integer $caid
   * @return array
   */
  public function callApplicationPermission(int $caid) : array {

    return $this->__permission('callApplicationPermission', $caid);

  }
}
