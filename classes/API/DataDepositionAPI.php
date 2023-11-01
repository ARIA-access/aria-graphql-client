<?php

namespace ARIA\GraphQLClient\API;

use ARIA\GraphQLClient\API\Fields\DataDepositionFields;
use ARIA\GraphQLClient\API\Fields\SiteExFields;
use ARIA\GraphQLClient\API\Fields\SiteFields;
use ARIA\GraphQLClient\APIDefinition;
use ARIA\GraphQLClient\Client;
use ARIA\GraphQLClient\CallException;
use ARIA\GraphQLClient\DateTime;
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

  /**
   * Create a bucket.
   *
   * @param integer $aria_id
   * @param string $aria_entity_type
   * @param integer $embargoed_until
   * @return array|null
   */
  public function createDataBucket(
    int $aria_id,
    string $aria_entity_type,
    int $embargoed_until
  )
  {
    $embargoed_until = DateTime::iso8601Date($embargoed_until);

    $mutation = <<< END
      mutation {
        createDataBucket(input: {
          aria_id: $aria_id,
          aria_entity_type: "$aria_entity_type",
          embargoed_until: "$embargoed_until",
        }) {
            $this->bucketFields
        }
      }
  END;

    $result = $this->getClient()->call($mutation, Client::METHOD_POST);

    if (!empty($result['data'])) {

      if (!empty($result['data']['createDataBucket'])) {
        return $result['data']['createDataBucket'];
      }
    }

    return null;
  }

  /**
   * Create a storage record in a given bucket.
   *
   * @param string $bucket_id
   * @param string $schema
   * @return array|null
   */
  public function createDataRecord(
    string $bucket_id,
    string $schema
  )
  {
   
    $mutation = <<< END
      mutation {
        createDataRecord(input: {
          bucket: "$bucket_id",
          schema: "$schema"
        }) {
            $this->recordFields
        }
      }
  END;

    $result = $this->getClient()->call($mutation, Client::METHOD_POST);

    if (!empty($result['data'])) {

      if (!empty($result['data']['createDataRecord'])) {
        return $result['data']['createDataRecord'];
      }
    }

    return null;
  }

  /**
   * Add a field to a record
   *
   * @param string $record_id The record ID
   * @param string $type Type of field (e.g. URL, Text)
   * @param array $content Content blob
   * @param array $options Array of options
   * @param integer $order Optional ordering value
   * @return void
   */
  public function createDataField(
    string $record_id,
    string $type,
    array $content = [],
    array $options = [],
    int $order = 0
  )
  {
    $content = JSONEncodedGQL::encode($content);
    $options = JSONEncodedGQL::encode($options);

    $mutation = <<< END
      mutation {
        createDataField(input: {
          record: "$record_id",
          type: "$type",
          content: $content,
          options: $options,
          order: $order
        }) {
            $this->fieldFields
        }
      }
  END;
  
    $result = $this->getClient()->call($mutation, Client::METHOD_POST);

    if (!empty($result['data'])) {

      if (!empty($result['data']['createDataField'])) {
        return $result['data']['createDataField'];
      }
    }

    return null;
  }

}
