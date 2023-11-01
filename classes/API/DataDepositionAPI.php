<?php

namespace ARIA\GraphQLClient\API;

use ARIA\GraphQLClient\API\Fields\DataDepositionFields;
use ARIA\GraphQLClient\API\Fields\SiteExFields;
use ARIA\GraphQLClient\API\Fields\SiteFields;
use ARIA\GraphQLClient\APIDefinition;
use ARIA\GraphQLClient\Client;
use ARIA\GraphQLClient\CallException;
use ARIA\GraphQLClient\JSONEncodedGQL;
use RuntimeException;

class DataDepositionAPI extends APIDefinition
{

  use DataDepositionFields;

  /**
   * Retrieve an individual bucket by its ID
   *
   * @param string $bucket_id
   * @return void
   */
  public function bucket(string $bucket_id) {

    $items = $this->buckets([ 'id' => $bucket_id]);

    if (!empty($items)) {
      return $items[0];
    }

    return null;
  }

  /**
   * Retrieve buckets matching query
   *
   * @param array $filter
   * @return array|null
   */
  public function buckets(array $filter = []): ?array
  {

    $query = "
    query {
      bucketItems(
        filters: " . JSONEncodedGQL::encode($filter) . "
      ){
        ". $this->bucketFields ."
      }
    }
    ";

    $result = $this->getClient()->call($query, Client::METHOD_GET);

    if (!empty($result['data'])) {

      if ($result['data']['bucketItems']) {
        return $result['data']['bucketItems'];
      }
    }

    return [];
  }

  /**
   * Retrieve an individual record by its ID
   *
   * @param string $record_id
   * @return void
   */
  public function record(string $record_id) {

    $items = $this->records([ 'id' => $record_id]);

    if (!empty($items)) {
      return $items[0];
    }

    return null;
  }

  /**
   * Retrieve records matching query
   *
   * @param array $filter
   * @return array|null
   */
  public function records(array $filter = []): ?array
  {

    $query = "
    query {
      recordItems(
        filters: " . JSONEncodedGQL::encode($filter) . "
      ){
        ". $this->recordFields ."
        fieldItems {
         " . $this->fieldFields . "
        }
      }
    }
    ";

    $result = $this->getClient()->call($query, Client::METHOD_GET);

    if (!empty($result['data'])) {

      if ($result['data']['recordItems']) {
        return $result['data']['recordItems'];
      }
    }

    return [];
  }


  // TODO: Mutations
  
}
