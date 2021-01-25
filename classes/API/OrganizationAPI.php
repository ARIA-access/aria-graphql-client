<?php

namespace ARIA\GraphQLClient\API;

use ARIA\GraphQLClient\APIDefinition;
use ARIA\GraphQLClient\Client;
use ARIA\GraphQLClient\CallException;
use ARIA\GraphQLClient\JSONEncodedGQL;

class OrganizationAPI extends APIDefinition
{
  private $organizationFields = '
    id,
    name,
    legal_name,
    reference,
    description,
    legal_status,
    website,
    address,
    country,
    lat,
    lng,
    confidence,
    old_aria_id
  ';

  /**
   * Query the organization API
   *
   * @param array $filter
   * @return array
   */
  public function organization(array $filter = []): array
  {

    $query = "
    query {
      organizationItems(
        filters: " . JSONEncodedGQL::encode($filter) . "
      ){
        {$this->organizationFields}
      }
    }
    ";

    $result = $this->getClient()->call($query, Client::METHOD_GET);

    if (!empty($result['data'])) {

      if ($result['data']['organizationItems']) {
        return $result['data']['organizationItems'];
      }
    }

    return [];
  }
}
